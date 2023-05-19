<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use App\Traits\AuthManager;

class ApiController extends Controller
{
    use ApiResponse;
    use AuthManager;

    /**
    *      @OA\Info(
    *          title="CMS API Documentation v1",
    *          version="1.0.0",
    *      )
    *          
    *          @OA\Server(
    *              description="CMS Swagger API V1 server",
    *              url="http://api.localtest.me/api/"
    *          )
    */
    public function __construct()
    {
        //
    }
    
}
