<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clear-tables {--codes} {--users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina los datos de las tablas en correspondiente orden';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Eliminando datos de las tablas...');

        $tables = [
            'failed_jobs',
            'jobs',
            'log_errors',
            'user_quotes',
            'user_photos',
            'photos'
        ];

        if ($this->option('codes')) {
            $tables[] = 'codes';
        }

        if ($this->option('users')) {
            $tables[] = 'users';
            $tables[] = 'model_has_roles';
        }

        foreach ($tables as $table) {
            try {
                if ($table != "users" && $table != "model_has_roles") {

                    DB::table($table)->delete();
                    $this->info("Datos de la tabla {$table} eliminados.");

                    // Reiniciamos la secuencia de AUTO_INCREMENT
                    DB::statement("ALTER TABLE {$table} AUTO_INCREMENT = 1;");
                    $this->info("Secuencia de la tabla {$table} reiniciada. ". PHP_EOL);

                } elseif ($table == "users") {
                    // Eliminar todos los usuarios excepto el administrador
                    DB::table('users')
                    ->where('email', '!=', 'admin@testing.com')
                    ->delete();
                    $this->info("Usuarios eliminados, excepto el administrador.");

                } elseif ($table == "model_has_roles") {
                    // Eliminar todos los roles excepto los relacionados con el administrador
                    DB::table('model_has_roles')
                        ->whereNotIn('model_id', function ($query) {
                            $query->select('id')
                                  ->from('users')
                                  ->where('email', '=', 'admin@testing.com'); // Ajusta para identificar al admin
                        })
                        ->delete();

                    $this->info("Roles eliminados, excepto los del administrador.");
                }

            } catch (\Exception $e) {
                $this->error("Error al eliminar datos de la tabla {$table}: " . $e->getMessage());
            }
        }

        $this->info('Eliminación de datos completada.');
    }
}
