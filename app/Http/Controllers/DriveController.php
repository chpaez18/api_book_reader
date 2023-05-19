<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Services\Drive\DriveService;

class DriveController extends ApiController
{
    public $driveService;

    public function __construct(DriveService $driveService, \Google_Client $client)
    {
        $this->driveService = $driveService;
    }

    public function getDrive()
    {
        try {

            return $this->successResponse($this->driveService->ListFolders('root'), 200);

        } catch (Exception $exception) {

            throw $exception;

        }
    }

    public function getFolderFiles()
    {
        try {

            return $this->successResponse($this->driveService->folderFiles(), 200);

        } catch (Exception $exception) {

            throw $exception;

        }
    }

    public function uploadFile(Request $request){

        try {
            $data = [
                'file' => $request->file('file'),
                'quote_number' => $request->input('quote_number')
            ];
            $this->driveService->createFile($data);

        } catch (Exception $exception) {

            throw $exception;

        }
        return $this->showMessage("The file was uploaded", 200);
    }

    public function deleteFile($id){

        try {

            $this->driveService->deleteFile($id);

        } catch (Exception $exception) {

            throw $exception;

        }
        return $this->showMessage("The file was deleted", 200);
    }

}
