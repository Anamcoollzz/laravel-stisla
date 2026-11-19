<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $existing = DB::table('menus')->select('id', 'app')->get();

        Schema::table('menus', function (Blueprint $table) {
            $table->dropIndex(['app']);
        });

        Schema::table('menus', function (Blueprint $table) {
            $table->text('app')->nullable()->change();
        });

        foreach ($existing as $row) {
            $decoded = is_string($row->app) ? json_decode($row->app, true) : null;
            $apps = is_array($decoded)
                ? array_values(array_filter(array_map('strval', $decoded)))
                : [filled($row->app) ? (string) $row->app : 'online-shop'];

            DB::table('menus')
                ->where('id', $row->id)
                ->update(['app' => json_encode($apps)]);
        }

        Schema::table('menus', function (Blueprint $table) {
            $table->json('app')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $existing = DB::table('menus')->select('id', 'app')->get();

        Schema::table('menus', function (Blueprint $table) {
            $table->text('app')->nullable()->change();
        });

        foreach ($existing as $row) {
            $decoded = is_string($row->app) ? json_decode($row->app, true) : null;
            $firstApp = is_array($decoded) && $decoded !== []
                ? (string) reset($decoded)
                : (filled($row->app) ? (string) $row->app : 'online-shop');

            DB::table('menus')
                ->where('id', $row->id)
                ->update(['app' => $firstApp]);
        }

        Schema::table('menus', function (Blueprint $table) {
            $table->string('app', 50)->default('online-shop')->change();
            $table->index('app');
        });
    }
};
