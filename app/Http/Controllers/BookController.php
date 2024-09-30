<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Book\BookService;
use App\Jobs\UploadImageToDriveJob;
use App\Services\Drive\DriveService;
use App\Http\Controllers\ApiController;

class BookController extends ApiController
{
    public $bookService;
    public $driveService;

    public function __construct(BookService $bookService, DriveService $driveService)
    {
        $this->bookService = $bookService;
        $this->driveService = $driveService;
    }

    public function getInfo()
    {
        try {

            return $this->successResponse($this->bookService->getBookInfo(), 200);

        } catch (Exception $exception) {

            throw $exception;

        }
    }

    public function getQuotes()
    {
        try {

            return $this->successResponse($this->bookService->getQuotes(), 200);

        } catch (Exception $exception) {

            throw $exception;

        }
    }

    public function saveAnecdote(Request $request)
    {
        try {

            return $this->successResponse($this->bookService->saveAnecdote($request, $this->driveService), 200);

        } catch (Exception $exception) {

            throw $exception;

        }
    }

}
