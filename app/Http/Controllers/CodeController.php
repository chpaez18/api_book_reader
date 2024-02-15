<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Codes\CodeService;
use App\Http\Controllers\ApiController;

class CodeController extends ApiController
{
    public $codeService;

    public function __construct(CodeService $codeService)
    {
        $this->codeService = $codeService;
        //$this->middleware(['role:admin']);
    }

    public function getCodes()
    {
        try {

            return $this->successResponse($this->codeService->getCodes(), 200);

        } catch (Exception $exception) {

            throw $exception;

        }
    }

    public function generate(Request $request)
    {
        try {

            $this->codeService->generateCode($request);

        } catch (Exception $exception) {

            throw $exception;

        }
        
    }

    public function changeStatus(Request $request)
    {
        try {

            $this->codeService->chanegCodeStatus($request);

        } catch (Exception $exception) {

            throw $exception;

        }
        return $this->showMessage("The code was updated", 200);
    }

    public function validateCode(Request $request)
    {
        try {

            $validatedCode = $this->codeService->validateCode($request);
            
        } catch (Exception $exception) {
            
            throw $exception;
            
        }
        return $this->showMessage($validatedCode["message"], $validatedCode["code"]);
    }

}
