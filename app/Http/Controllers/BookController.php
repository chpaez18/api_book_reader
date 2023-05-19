<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Book\BookService;
use App\Http\Controllers\ApiController;

class BookController extends ApiController
{
    public $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    public function getInfo()
    {
        try {

            return $this->successResponse($this->bookService->getBookInfo(), 200);

        } catch (Exception $exception) {

            throw $exception;

        }
    }

}
