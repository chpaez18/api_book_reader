<?php

namespace App\Services\Book;

use App\Models\User;

use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;


use Illuminate\Support\Facades\Storage;
use App\Models\{Book, Photo, UserPhoto, Quote, UserQuote};
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;


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

    public function saveAnecdote($data, $driveService)
    {
        //Get anecdote data
        //------------------------------------------------------------------------------------------------
            $dates = json_decode($data->input('dates'), true);
            $day = $dates['day'];
            $month = $dates['month'];
            $year = "20".$dates['year'];
            $date = Carbon::createFromDate($year, $month, $day);
            $user = auth('api')->user();
        //------------------------------------------------------------------------------------------------
        
        //Save anecdote
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
            
            if ($data->has('image')) {
                
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

            }
        //------------------------------------------------------------------------------------------------

        return $anecdote;
    }



}
