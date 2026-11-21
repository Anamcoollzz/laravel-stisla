<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DatabaseTableController extends Controller
{
    /**
     * Display a listing of all database tables.
     */
    public function index(): View
    {
        Gate::authorize('database-tables.view');

        $rawTables = Schema::getTables();
        $tables = [];
        $totalRows = 0;

        foreach ($rawTables as $tableInfo) {
            $tableName = $tableInfo['name'];

            if (str_starts_with($tableName, 'sqlite_')) {
                continue;
            }

            $columnsCount = count(Schema::getColumns($tableName));
            $rowsCount = (int) DB::table($tableName)->count();
            $totalRows += $rowsCount;

            $tables[] = [
                'name' => $tableName,
                'schema' => $tableInfo['schema'] ?? null,
                'columns_count' => $columnsCount,
                'rows_count' => $rowsCount,
                'engine' => $tableInfo['engine'] ?? null,
                'collation' => $tableInfo['collation'] ?? null,
                'size' => isset($tableInfo['size']) ? $this->formatBytes((int) $tableInfo['size']) : null,
            ];
        }

        usort($tables, fn (array $a, array $b) => strcmp($a['name'], $b['name']));

        $connection = config('database.default');
        $dbConfig = config("database.connections.{$connection}", []);
        $databaseName = $dbConfig['database'] ?? '-';
        if ($connection === 'sqlite' && is_string($databaseName)) {
            $databaseName = basename($databaseName);
        }

        $stats = [
            'total_tables' => count($tables),
            'total_rows' => $totalRows,
            'connection' => strtoupper((string) $connection),
            'database' => (string) $databaseName,
        ];

        return view('modules.database-tables.index', compact('tables', 'stats'));
    }

    /**
     * Display table columns schema and paginated rows data.
     */
    public function show(Request $request, string $table): View
    {
        Gate::authorize('database-tables.view');

        $validTables = collect(Schema::getTables())->pluck('name')->all();
        if (! in_array($table, $validTables, true)) {
            abort(404, __('Tabel tidak ditemukan.'));
        }

        $columns = Schema::getColumns($table);
        $indexes = Schema::getIndexes($table);
        $foreignKeys = Schema::getForeignKeys($table);
        $columnNames = array_column($columns, 'name');

        $search = trim((string) $request->query('search', ''));
        $perPage = (int) $request->query('per_page', 15);
        if (! in_array($perPage, [10, 15, 25, 50, 100], true)) {
            $perPage = 15;
        }

        $defaultSort = in_array('id', $columnNames, true) ? 'id' : ($columnNames[0] ?? null);
        $sort = (string) $request->query('sort', (string) $defaultSort);
        if (! in_array($sort, $columnNames, true)) {
            $sort = $defaultSort;
        }

        $direction = strtolower((string) $request->query('direction', in_array('id', $columnNames, true) ? 'desc' : 'asc'));
        if (! in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'asc';
        }

        $query = DB::table($table);

        if ($search !== '' && ! empty($columnNames)) {
            $query->where(function ($q) use ($columnNames, $search) {
                foreach ($columnNames as $col) {
                    $q->orWhere($col, 'like', "%{$search}%");
                }
            });
        }

        if ($sort !== null) {
            $query->orderBy($sort, $direction);
        }

        $rows = $query->paginate($perPage)->withQueryString();
        $totalRows = (int) DB::table($table)->count();

        $activeTab = (string) $request->query('tab', 'data');
        if (! in_array($activeTab, ['data', 'columns'], true)) {
            $activeTab = 'data';
        }

        $primaryKey = $this->resolvePrimaryKey($indexes, $columnNames);

        return view('modules.database-tables.show', compact(
            'table',
            'columns',
            'indexes',
            'foreignKeys',
            'columnNames',
            'rows',
            'totalRows',
            'search',
            'perPage',
            'sort',
            'direction',
            'activeTab',
            'primaryKey'
        ));
    }

    /**
     * Update a column definition or rename a column in the specified table.
     */
    public function updateColumn(Request $request, string $table, string $column): RedirectResponse
    {
        Gate::authorize('database-tables.view');

        $validTables = collect(Schema::getTables())->pluck('name')->all();
        if (! in_array($table, $validTables, true)) {
            abort(404, __('Tabel tidak ditemukan.'));
        }

        $columnNames = Schema::getColumnListing($table);
        if (! in_array($column, $columnNames, true)) {
            abort(404, __('Kolom tidak ditemukan.'));
        }

        $validated = $request->validate([
            'new_name' => ['required', 'string', 'max:64', 'regex:/^[a-zA-Z_][a-zA-Z0-9_]*$/'],
            'type' => ['required', Rule::in([
                'string',
                'text',
                'longText',
                'integer',
                'bigInteger',
                'unsignedBigInteger',
                'float',
                'decimal',
                'boolean',
                'date',
                'dateTime',
                'json',
            ])],
            'length' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'default_value' => ['nullable', 'string', 'max:255'],
            'comment' => ['nullable', 'string', 'max:255'],
        ]);

        $newName = $validated['new_name'];
        if ($newName !== $column && in_array($newName, $columnNames, true)) {
            return redirect()
                ->route('database-tables.show', ['table' => $table, 'tab' => 'columns'])
                ->with('error', __('Nama kolom baru sudah ada di tabel ini.'));
        }

        $isNullable = $request->boolean('nullable');
        $type = $validated['type'];
        $length = $validated['length'] ?? null;
        $defaultValue = $validated['default_value'] ?? null;
        $comment = $validated['comment'] ?? null;

        $oldColumnMeta = collect(Schema::getColumns($table))->firstWhere('name', $column);
        $beforeColumn = $oldColumnMeta ? [
            'name' => $oldColumnMeta['name'] ?? $column,
            'type' => $oldColumnMeta['type_name'] ?? ($oldColumnMeta['type'] ?? null),
            'nullable' => (bool) ($oldColumnMeta['nullable'] ?? false),
            'default' => $oldColumnMeta['default'] ?? null,
            'comment' => $oldColumnMeta['comment'] ?? null,
        ] : ['name' => $column];

        try {
            if ($newName !== $column) {
                Schema::table($table, function (Blueprint $blueprint) use ($column, $newName): void {
                    $blueprint->renameColumn($column, $newName);
                });
            }

            Schema::table($table, function (Blueprint $blueprint) use ($newName, $type, $length, $isNullable, $defaultValue, $comment): void {
                $colDef = match ($type) {
                    'string' => $blueprint->string($newName, (int) ($length ?: 255)),
                    'text' => $blueprint->text($newName),
                    'longText' => $blueprint->longText($newName),
                    'integer' => $blueprint->integer($newName),
                    'bigInteger' => $blueprint->bigInteger($newName),
                    'unsignedBigInteger' => $blueprint->unsignedBigInteger($newName),
                    'float' => $blueprint->float($newName),
                    'decimal' => $blueprint->decimal($newName, 15, 2),
                    'boolean' => $blueprint->boolean($newName),
                    'date' => $blueprint->date($newName),
                    'dateTime' => $blueprint->dateTime($newName),
                    'json' => $blueprint->json($newName),
                };

                $colDef->nullable($isNullable);

                if ($defaultValue !== null && $defaultValue !== '') {
                    $colDef->default($type === 'boolean' ? filter_var($defaultValue, FILTER_VALIDATE_BOOLEAN) : $defaultValue);
                }

                if ($comment !== null && $comment !== '') {
                    $colDef->comment($comment);
                }

                $colDef->change();
            });
        } catch (\Throwable $e) {
            return redirect()
                ->route('database-tables.show', ['table' => $table, 'tab' => 'columns'])
                ->with('error', __('Gagal mengubah kolom: ').$e->getMessage());
        }

        ActivityLog::record(
            module: 'database-tables',
            action: 'updated_column',
            description: "Updated column {$table}.{$column}".($newName !== $column ? " (renamed to {$newName})" : ''),
            subjectLabel: "{$table}.{$newName}",
            before: $beforeColumn,
            after: [
                'name' => $newName,
                'type' => $type,
                'length' => $length,
                'nullable' => $isNullable,
                'default' => $defaultValue,
                'comment' => $comment,
            ],
            subjectId: "{$table}.{$newName}",
        );

        return redirect()
            ->route('database-tables.show', ['table' => $table, 'tab' => 'columns'])
            ->with('success', __('Kolom berhasil diperbarui.'));
    }

    /**
     * Drop the specified column from the table.
     */
    public function destroyColumn(string $table, string $column): RedirectResponse
    {
        Gate::authorize('database-tables.view');

        $validTables = collect(Schema::getTables())->pluck('name')->all();
        if (! in_array($table, $validTables, true)) {
            abort(404, __('Tabel tidak ditemukan.'));
        }

        $columnNames = Schema::getColumnListing($table);
        if (! in_array($column, $columnNames, true)) {
            abort(404, __('Kolom tidak ditemukan.'));
        }

        if ($column === 'id' || count($columnNames) <= 1) {
            return redirect()
                ->route('database-tables.show', ['table' => $table, 'tab' => 'columns'])
                ->with('error', __('Kolom id atau satu-satunya kolom tidak dapat dihapus.'));
        }

        $oldColumnMeta = collect(Schema::getColumns($table))->firstWhere('name', $column);
        $beforeColumn = $oldColumnMeta ? [
            'name' => $oldColumnMeta['name'] ?? $column,
            'type' => $oldColumnMeta['type_name'] ?? ($oldColumnMeta['type'] ?? null),
            'nullable' => (bool) ($oldColumnMeta['nullable'] ?? false),
            'default' => $oldColumnMeta['default'] ?? null,
        ] : ['name' => $column];

        try {
            Schema::table($table, function (Blueprint $blueprint) use ($column): void {
                $blueprint->dropColumn($column);
            });
        } catch (\Throwable $e) {
            return redirect()
                ->route('database-tables.show', ['table' => $table, 'tab' => 'columns'])
                ->with('error', __('Gagal menghapus kolom: ').$e->getMessage());
        }

        ActivityLog::record(
            module: 'database-tables',
            action: 'deleted_column',
            description: "Dropped column {$table}.{$column}",
            subjectLabel: "{$table}.{$column}",
            before: $beforeColumn,
            after: null,
            subjectId: "{$table}.{$column}",
        );

        return redirect()
            ->route('database-tables.show', ['table' => $table, 'tab' => 'columns'])
            ->with('success', __('Kolom berhasil dihapus.'));
    }

    /**
     * Update a row in the specified table.
     */
    public function updateRow(Request $request, string $table, string $rowId): RedirectResponse
    {
        Gate::authorize('database-tables.view');

        $validTables = collect(Schema::getTables())->pluck('name')->all();
        if (! in_array($table, $validTables, true)) {
            abort(404, __('Tabel tidak ditemukan.'));
        }

        $columns = Schema::getColumns($table);
        $indexes = Schema::getIndexes($table);
        $columnNames = array_column($columns, 'name');
        $primaryKey = $this->resolvePrimaryKey($indexes, $columnNames);

        if ($primaryKey === null) {
            return redirect()
                ->route('database-tables.show', ['table' => $table, 'tab' => 'data'])
                ->with('error', __('Tabel tidak memiliki primary key tunggal untuk diubah.'));
        }

        $values = $request->input('values', []);
        $nulls = $request->input('nulls', []);
        $updateData = [];

        foreach ($columns as $col) {
            $colName = $col['name'];
            if ($colName === $primaryKey && ($col['auto_increment'] ?? false)) {
                continue;
            }

            if (isset($nulls[$colName]) && ($col['nullable'] ?? false)) {
                $updateData[$colName] = null;

                continue;
            }

            if (array_key_exists($colName, $values)) {
                $updateData[$colName] = $values[$colName];
            }
        }

        $existingRow = DB::table($table)->where($primaryKey, $rowId)->first();
        $beforeRow = $existingRow ? (array) $existingRow : [];

        try {
            DB::table($table)->where($primaryKey, $rowId)->update($updateData);
        } catch (\Throwable $e) {
            return redirect()
                ->route('database-tables.show', ['table' => $table, 'tab' => 'data'])
                ->with('error', __('Gagal mengubah data: ').$e->getMessage());
        }

        $changedBefore = [];
        $changedAfter = [];
        foreach ($updateData as $colKey => $newVal) {
            $oldVal = $beforeRow[$colKey] ?? null;
            if ((string) $oldVal !== (string) $newVal || ($oldVal === null) !== ($newVal === null)) {
                $changedBefore[$colKey] = $oldVal;
                $changedAfter[$colKey] = $newVal;
            }
        }

        if (! empty($changedBefore) || ! empty($changedAfter)) {
            ActivityLog::record(
                module: 'database-tables',
                action: 'updated_row',
                description: "Updated row {$table}#{$rowId}",
                subjectLabel: "{$table}#{$rowId}",
                before: $changedBefore,
                after: $changedAfter,
                subjectId: "{$table}:{$rowId}",
            );
        }

        return redirect()
            ->route('database-tables.show', ['table' => $table, 'tab' => 'data'])
            ->with('success', __('Data baris berhasil diperbarui.'));
    }

    /**
     * Delete a row from the specified table.
     */
    public function destroyRow(string $table, string $rowId): RedirectResponse
    {
        Gate::authorize('database-tables.view');

        $validTables = collect(Schema::getTables())->pluck('name')->all();
        if (! in_array($table, $validTables, true)) {
            abort(404, __('Tabel tidak ditemukan.'));
        }

        $columns = Schema::getColumns($table);
        $indexes = Schema::getIndexes($table);
        $columnNames = array_column($columns, 'name');
        $primaryKey = $this->resolvePrimaryKey($indexes, $columnNames);

        if ($primaryKey === null) {
            return redirect()
                ->route('database-tables.show', ['table' => $table, 'tab' => 'data'])
                ->with('error', __('Tabel tidak memiliki primary key tunggal untuk dihapus.'));
        }

        $existingRow = DB::table($table)->where($primaryKey, $rowId)->first();
        $beforeRow = $existingRow ? (array) $existingRow : ['id' => $rowId];

        try {
            DB::table($table)->where($primaryKey, $rowId)->delete();
        } catch (\Throwable $e) {
            return redirect()
                ->route('database-tables.show', ['table' => $table, 'tab' => 'data'])
                ->with('error', __('Gagal menghapus data: ').$e->getMessage());
        }

        ActivityLog::record(
            module: 'database-tables',
            action: 'deleted_row',
            description: "Deleted row {$table}#{$rowId}",
            subjectLabel: "{$table}#{$rowId}",
            before: $beforeRow,
            after: null,
            subjectId: "{$table}:{$rowId}",
        );

        return redirect()
            ->route('database-tables.show', ['table' => $table, 'tab' => 'data'])
            ->with('success', __('Data baris berhasil dihapus.'));
    }

    /**
     * Resolve a single-column primary key name for a table.
     *
     * @param  array<int, array<string, mixed>>  $indexes
     * @param  array<int, string>  $columnNames
     */
    protected function resolvePrimaryKey(array $indexes, array $columnNames): ?string
    {
        foreach ($indexes as $index) {
            if (($index['primary'] ?? false) && count($index['columns'] ?? []) === 1) {
                return $index['columns'][0];
            }
        }

        return in_array('id', $columnNames, true) ? 'id' : null;
    }

    /**
     * Format bytes into human-readable string.
     */
    protected function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        }
        if ($bytes < 1048576) {
            return round($bytes / 1024, 2).' KB';
        }

        return round($bytes / 1048576, 2).' MB';
    }
}
