<?php

namespace App\Console\Commands;

use Throwable;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Jobs\UploadImageToDriveJob;
use Illuminate\Support\Facades\Bus;

class ProcessImageBatches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:process-batches';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Procesa imágenes pendientes en batches (lotes)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //$this->info('Ejecutando cola de Jobs...');
        //\Artisan::call('queue:work --once --queue=default');

        $this->info('Iniciando procesamiento de imágenes en batches...');

        // Obtén imágenes pendientes desde la tabla 'images_for_upload'
        $images = DB::table('images_for_upload')
                    ->where('status', 'pending')
                    ->limit(10) // Procesa 10 imágenes a la vez (ajústalo según tus necesidades)
                    ->get();

        if ($images->isEmpty()) {
            $this->info('No hay imágenes pendientes por procesar.');
            return;
        }

        foreach ($images as $imageData) {

            // Actualizar el estado de la imagen a "processing"
            DB::table('images_for_upload')
                ->where('id', $imageData->id)
                ->update(['status' => 'processing']);

            // Añadir el trabajo a la cola de jobs
            $jobs[] = new UploadImageToDriveJob($imageData);
        }


        // Crear un batch de trabajos
        Bus::batch($jobs)->dispatch();


        $this->info('Proceso de batch completado.');
    }

}
