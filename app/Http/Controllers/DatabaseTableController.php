<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
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
            'activeTab'
        ));
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
