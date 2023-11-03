<?php

namespace App\Services\Drive;

use App\Models\Photo;
use App\Models\UserPhoto;

use App\Models\UserQuote;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class DriveService
{
    private $drive;
    private $folderName;

    public function __construct(\Google_Client $client)
    {
        if (auth('api')->user() != null) {

            $client->refreshToken(auth('api')->user()->access_token);
            $this->drive = new \Google_Service_Drive($client);
        }

        $this->folderName = "100 citas | carpeta de imagenes";
    }


    public function ListFolders($id){

        $files = [];
        $query = "mimeType='application/vnd.google-apps.folder' and '".$id."' in parents and trashed=false";

        $optParams = [
            'fields' => 'files(id, name)',
            'q' => $query
        ];

        $results = $this->drive->files->listFiles($optParams);

        if (count($results->getFiles()) == 0) {
            return "No files found.\n";
        } else {
            foreach ($results->getFiles() as $file) {
                $files[] = [
                    'id' => $file->getID(),
                    'name' => $file->getName()
                ];
            }
            return $files;
        }
    }

    public function folderFiles()
    {
        $folderId = self::getFolderId($this->folderName);
        $files = [];
        $parameters = [
            'fields' => 'files(id, name, webContentLink)',
            'q' => "'$folderId' in parents",
        ];

        $results = $this->drive->files->listFiles($parameters);

        if (count($results->getFiles()) == 0) {
            return "No files found.\n";
        } else {
            foreach ($results->getFiles() as $file) {
                $driveFile = $this->drive->files->get($file->getID(), ['fields' => 'webViewLink']);
                $files[] = [
                    'id' => $file->getID(),
                    'name' => $file->getName(),
                    'url' => "https://drive.google.com/uc?export=view&id=" . $file->getID(),
                    'shared_url' => $driveFile->webViewLink
                ];
            }
            return $files;
        }
    }

    public function createFile($data){

        //Get data
        //---------------------------------------------------------------------------------------------
            $file = $data['file'];
            $quoteNumber = $data['quote_number'];
        //---------------------------------------------------------------------------------------------

        //Upload file to google drive folder
        //---------------------------------------------------------------------------------------------
            $folderId = self::getFolderId($this->folderName);

            if (!$folderId) {
                $folderId = self::createFolder($this->folderName);
            }

            $name = gettype($file) === 'object' ? $file->getClientOriginalName() : $file;
            $name = "cita_n°_".$quoteNumber."_".$name;
            $fileMetadata = new \Google_Service_Drive_DriveFile();
            $fileMetadata->setName($name);
            $fileMetadata->setParents([$folderId]);

            $content = gettype($file) === 'object' ?  File::get($file) : Storage::get($file);
            $mimeType = gettype($file) === 'object' ? File::mimeType($file) : Storage::mimeType($file);

            $file = $this->drive->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => $mimeType,
                'uploadType' => 'multipart',
                'fields' => 'id'
            ]);
        //---------------------------------------------------------------------------------------------


        //Grant permissions
        //---------------------------------------------------------------------------------------------
            $newPermission = new \Google_Service_Drive_Permission();
            $newPermission->setType('anyone');
            $newPermission->setRole('reader');
            try {
                $this->drive->permissions->create($file->id, $newPermission);
            } catch (Exception $e) {
                print "An error occurred: " . $e->getMessage();
            }
        //---------------------------------------------------------------------------------------------


        //Save file on db by user
        //---------------------------------------------------------------------------------------------
            if ($file) {
                $photo = new Photo();
                $photo->google_drive_id = $file->id;
                $photo->save();

                $userPhoto = new UserPhoto();
                $userPhoto->user_id = auth('api')->user()->id;
                $userPhoto->photo_id = $photo->id;
                $userPhoto->save();
            }
        //---------------------------------------------------------------------------------------------

        //$file->id;
    }

    public function createFolder($folderName){

        $folderMeta = new \Google_Service_Drive_DriveFile(array(
            'name' => $folderName,
            'mimeType' => 'application/vnd.google-apps.folder'));
        $folder = $this->drive->files->create($folderMeta, array(
            'fields' => 'id'));
        return $folder->id;
    }

    public function getFolderId($folderName)
    {
        $files = [];
        $query = "mimeType='application/vnd.google-apps.folder' and name='$folderName' and trashed=false";

        $optParams = [
            'fields' => 'files(id, name)',
            'q' => $query
        ];

        $results = $this->drive->files->listFiles($optParams);
        if (count($results->getFiles()) == 0) {
            return false;
        } else {
            return $results->getFiles()[0]->getID();
        }
    }

    function deleteFile($id){

        $photo = Photo::where('google_drive_id', $id)->first();
        $userId = auth('api')->user()->id;
        if ($photo) {

            $userQuote = UserQuote::where('photo_id', $photo->id)->where('user_id', $userId)->first();

            if ($userQuote) {
                $userQuote->delete();
            }

            $userPhoto = UserPhoto::where('photo_id', $photo->id)->first();

            if ($userPhoto) {
                $userPhoto->delete();
            }

            $photo->delete();
        }

        $this->drive->files->delete($id);

    }

}
