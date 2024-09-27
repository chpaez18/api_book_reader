<?php

namespace App\Services\Book;

use Carbon\Carbon;

use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Jobs\UploadImageToDriveJob;


use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\{Book, Photo, UserPhoto, Quote, UserQuote};


class BookService
{

    
    public function getBookInfo()
    {

        $finalData = [];
        $user = User::where('id', auth('api')->user()->id)->first();

        //Get book info
        //------------------------------------------------------------------------------------------------
            $bookInfo = Book::select('name', 'autor', 'description', 'number_pages', 'number_quotes')->first()->toArray();
        //------------------------------------------------------------------------------------------------

        //Get quotes
        //------------------------------------------------------------------------------------------------
            $quotes = Quote::select('id','first_title', 'second_title', 'message', 'number_quote')->where('status', Quote::Active)->orderBy('number_quote')->get()->toArray();
        //------------------------------------------------------------------------------------------------

        //Get user book info
        //------------------------------------------------------------------------------------------------
            $userBookInfo = $user->quotes()
            ->select('id','user_id', 'quote_id', 'is_completed', 'mood', 'quote_description', 'words', 'date', 'photo_id')
            ->with(['photo' => function ($query) {
                $query->select('photos.id', 'photos.google_drive_id', 'photos.url_view');
            }])
            ->orderBy('quote_id')
            ->get();

            $quotesCompleted = $userBookInfo->where('is_completed', 1)->count();
            $quotesWrited = $userBookInfo->where('quote_description', '<>', null)->count();
            $imagesUploaded = $userBookInfo->where('photo_id', '<>', null)->count();
        //------------------------------------------------------------------------------------------------

        //Build final array
        //------------------------------------------------------------------------------------------------
            $finalData['book_info'] = $bookInfo;
            $finalData['user_book_info'] = $userBookInfo;
            $finalData['quotes'] = $quotes;
            $finalData['statistics']['quotes_completed'] = $quotesCompleted;
            $finalData['statistics']['quotes_writed'] = $quotesWrited;
            $finalData['statistics']['images_uploaded'] = $imagesUploaded;
        //------------------------------------------------------------------------------------------------

        return $finalData;
    }

    public function getQuotes()
    {
        //Get quotes
        //------------------------------------------------------------------------------------------------
            $quotes = Quote::select('id','first_title', 'second_title', 'message', 'number_quote')->where('status', Quote::Active)->orderBy('number_quote')->get()->toArray();
        //------------------------------------------------------------------------------------------------

        return $quotes;
    }

    public function saveAnecdote($data, $driveService)
    {

        DB::beginTransaction();
        try {

            //Obtenemos la información de la cita
            //------------------------------------------------------------------------------------------------
                $dates = json_decode($data->input('dates'), true);
                $day = $dates['day'];
                $month = $dates['month'];
                $year = "20".$dates['year'];
                $date = Carbon::createFromDate($year, $month, $day);
                $user = auth('api')->user();
            //------------------------------------------------------------------------------------------------

            //Guardamos o actualizamos la anecdota segun sea el caso
            //------------------------------------------------------------------------------------------------
                $anecdote = UserQuote::updateOrCreate(
                    [
                        //Attributes for search record
                        'user_id' => $user->id,
                        'quote_id' => $data->input('quote_id')
                    ],
                    [
                        //Attributes for update is exists or create
                        'quote_description' => $data->input('quote_description'),
                        'words' => $data->input('words'),
                        'date' => $date,
                        'is_completed' => $data->input('is_completed'),
                        'mood' => $data->input('mood'),
                    ]
                );
            //------------------------------------------------------------------------------------------------

            //Guardamos la imagen
            //------------------------------------------------------------------------------------------------
                /* if ($data->has('image')) {

                    $imageData = $data->input('image');
                    // Encuentra la posición del primer carácter ',' que separa el tipo MIME de los datos base64
                    $commaPosition = strpos($imageData, ',') + 1;
                    // Obtén los datos base64 puros
                    $base64Str = substr($imageData, $commaPosition);
                    // Decodifica la cadena base64
                    $image = base64_decode($base64Str);


                    // Guarda la imagen en un archivo temporal
                    $tempImage = tempnam(sys_get_temp_dir(), 'image');
                    file_put_contents($tempImage, $image);

                    // Opcional: Verifica si el archivo temporal es una imagen válida
                    if (false === getimagesize($tempImage)) {
                        throw new \Exception('El archivo temporal no es una imagen válida.');
                    }

                    $photoToSave = [
                        'file' => $tempImage,
                        'quote_number' => $data->input('quote_id')
                    ];

                    if (!$anecdote->wasRecentlyCreated) {
                        $hasPhotos = $user->photos->where('quote_id', $data->input('quote_id'))->first();
                        if ($hasPhotos) {
                            
                            $photo = Photo::where('id', $hasPhotos->photo->id)->first();
                            $driveService->deleteFile($photo->google_drive_id);

                            $res = $driveService->createFile($photoToSave);
                            $anecdote->photo_id = $res->id;
                            $anecdote->save();

                            $photo->delete();

                        } else {
                            $res = $driveService->createFile($photoToSave);
                            $anecdote->photo_id = $res->id;
                            $anecdote->save();
                        }
                    
                    } else {
                        $res = $driveService->createFile($photoToSave);
                        $anecdote->photo_id = $res->id;
                        $anecdote->save();
                    }

                    unlink($tempImage);

                } */

                if ($data->has('image')) {

                    //Obtenemos la imagen en base64
                    //-----------------------------------------------------------------------
                        $imageData = $data->input('image');
                        $commaPosition = strpos($imageData, ',') + 1;
                        $base64Str = substr($imageData, $commaPosition);
                        $image = base64_decode($base64Str);
                    //-----------------------------------------------------------------------

                    // Guardamos la imagen en un archivo temporal
                    //-----------------------------------------------------------------------
                        $tempImagePath = storage_path('app/temp_images/' . uniqid() . '.png');
                        file_put_contents($tempImagePath, $image);
                    //-----------------------------------------------------------------------

                    // Preparamos los datos que pasaremos al job
                    //-----------------------------------------------------------------------
                        $photoToSave = [
                            'file_path' => $tempImagePath,
                            'quote_number' => $data->input('quote_id'),
                            'user_id' => $user->id,
                            'user_quote_id' => $anecdote->id
                        ];
                    //-----------------------------------------------------------------------

                    // Verificamos si la anecdota ha sido creada recientemente
                    //--------------------------------------------------------------------------------------------------
                        if (!$anecdote->wasRecentlyCreated) {
                            // Verificamos si el usuario ya tiene una foto para esta cita
                            //-----------------------------------------------------------------------
                                $hasPhotos = $user->photos->where('quote_id', $data->input('quote_id'))->first();
                                /* $hasPhotos = Photo::whereHas('userPhotos', function ($query) use ($user, $data) {
                                    $query->where('user_id', $user->id)
                                          ->where('quote_id', $data->input('quote_id'));
                                })->first(); */

                                if ($hasPhotos) {

                                    // Actualizamos la referencia de `photo_id` en `user_quotes` para que no apunte a esta foto
                                    //-----------------------------------------------------------------------
                                        $anecdote->photo_id = null;
                                        $anecdote->save();
                                    //-----------------------------------------------------------------------

                                    //Eliminamos la foto de Google Drive
                                    //-----------------------------------------------------------------------
                                        //$driveService->deleteFile($photo->google_drive_id);
                                        $photoToSave['photo_google_id'] = $hasPhotos->photo->google_drive_id;
                                    //-----------------------------------------------------------------------


                                    // Despachamos el evento que se encarga de subir la nueva foto a Google Drive
                                    //-----------------------------------------------------------------------
                                        UploadImageToDriveJob::dispatch($photoToSave);
                                    //-----------------------------------------------------------------------

                                    $hasPhotos->delete();
                                    $hasPhotos->photo->delete();

                                    //return $anecdote;

                                } else {

                                    // Si no tenia una foto, solo despachamos el evento para subir la foto a Google Drive
                                    //--------------------------------------------------------------------------------------
                                        UploadImageToDriveJob::dispatch($photoToSave);
                                    //--------------------------------------------------------------------------------------

                                }
                            //-----------------------------------------------------------------------

                        } else {

                            // Si la anecdota fue creada recientemente, solo despachamos el evento para subir la foto a Google Drive
                            //--------------------------------------------------------------------------------------
                                UploadImageToDriveJob::dispatch($photoToSave);
                            //--------------------------------------------------------------------------------------

                        }
                    //--------------------------------------------------------------------------------------------------

                }
            //------------------------------------------------------------------------------------------------

            //Ejecutamos el commit si todo ha salido bien
            //------------------------------------------------------------------------------------------------
                DB::commit();
            //------------------------------------------------------------------------------------------------

            return $anecdote;

        } catch (\Exception $e) {
            \Log::error('Error al guardar la anécdota: ' . $e->getMessage());
            dd($e->getMessage());
            DB::rollBack();
            throw $e;
        }

    }



}
