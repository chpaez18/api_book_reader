<?php

namespace App\Services\Book;

use App\Models\User;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;


use Illuminate\Support\Facades\Storage;
use App\Models\{Book, Photo, UserPhoto};
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class BookService
{


    public function getBookInfo(){

        $finalData = [];
        $user = User::where('id', auth('api')->user()->id)->first();

        //Get book info
        //------------------------------------------------------------------------------------------------
            $bookInfo = Book::select('name', 'autor', 'description', 'number_pages', 'number_quotes')->first()->toArray();
        //------------------------------------------------------------------------------------------------

        //Get user book info
        //------------------------------------------------------------------------------------------------
            $userBookInfo = $user->quotes()
            ->select('id','user_id', 'is_completed', 'mood', 'quote_description', 'words', 'photo_id')
            ->with(['photo' => function ($query) {
                $query->select('photos.id', 'photos.google_drive_id');
            }])
            ->get();
            $quotesCompleted = $userBookInfo->where('is_completed', 1)->count();
            $quotesWrited = $userBookInfo->where('quote_description', '<>', null)->count();
            $imagesUploaded = $userBookInfo->where('photo_id', '<>', null)->count();
        //------------------------------------------------------------------------------------------------

        //Build final array
        //------------------------------------------------------------------------------------------------
            $finalData['book_info'] = $bookInfo;
            $finalData['user_book_info'] = $userBookInfo;
            $finalData['statistics']['quotes_completed'] = $quotesCompleted;
            $finalData['statistics']['quotes_writed'] = $quotesWrited;
            $finalData['statistics']['images_uploaded'] = $imagesUploaded;
        //------------------------------------------------------------------------------------------------

        return $finalData;
    }



}
