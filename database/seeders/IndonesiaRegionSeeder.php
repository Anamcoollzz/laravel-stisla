<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndonesiaRegionSeeder extends Seeder
{
    /**
     * Seed provinces and regencies from database/indonesia.sql.
     */
    public function run(): void
    {
        $sqlPath = database_path('indonesia.sql');

        if (! is_file($sqlPath)) {
            $this->command?->warn('File database/indonesia.sql not found.');

            return;
        }

        $handle = fopen($sqlPath, 'rb');
        if ($handle === false) {
            return;
        }

        $provinces = [];
        $regencies = [];
        $currentTable = null;

        while (($line = fgets($handle)) !== false) {
            $trimmed = trim($line);

            if (str_starts_with($trimmed, 'INSERT INTO `provinces`')) {
                $currentTable = 'provinces';

                continue;
            }

            if (str_starts_with($trimmed, 'INSERT INTO `regencies`')) {
                $currentTable = 'regencies';

                continue;
            }

            if (str_starts_with($trimmed, 'INSERT INTO `districts`')) {
                break;
            }

            if ($currentTable === 'provinces') {
                if (preg_match("/^\('([^']+)',\s*'((?:\\\\'|[^'])*)'\)/", $trimmed, $m)) {
                    $provinces[] = [
                        'id' => $m[1],
                        'name' => stripslashes($m[2]),
                    ];
                }
            } elseif ($currentTable === 'regencies') {
                if (preg_match("/^\('([^']+)',\s*'([^']+)',\s*'((?:\\\\'|[^'])*)'\)/", $trimmed, $m)) {
                    $regencies[] = [
                        'id' => $m[1],
                        'province_id' => $m[2],
                        'name' => stripslashes($m[3]),
                    ];
                }
            }

            if (str_ends_with($trimmed, ';')) {
                $currentTable = null;
            }
        }

        fclose($handle);

        DB::transaction(function () use ($provinces, $regencies): void {
            foreach (array_chunk($provinces, 100) as $chunk) {
                DB::table('provinces')->upsert($chunk, ['id'], ['name']);
            }

            foreach (array_chunk($regencies, 200) as $chunk) {
                DB::table('regencies')->upsert($chunk, ['id'], ['province_id', 'name']);
            }
        });
    }
}
