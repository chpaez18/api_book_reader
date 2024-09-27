<?php

namespace App\Jobs;

use Google_Client;
use App\Models\User;
use App\Models\Photo;
use App\Models\UserPhoto;
use App\Models\UserQuote;
use Google_Service_Drive;
use Illuminate\Bus\Queueable;
use App\Services\Drive\DriveService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UploadImageToDriveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $photoData;

    public function __construct($photoData)
    {
        $this->photoData = $photoData;
    }

    public function handle(\App\Services\Drive\DriveService $driveService)
    {

        //Get data
        //---------------------------------------------------------------------------------------------
            $user = User::find($this->photoData['user_id']);
            $filePath = $this->photoData['file_path'];
            $quoteNumber = $this->photoData['quote_number'];
            $userQuote = UserQuote::find($this->photoData['user_quote_id']);
        //---------------------------------------------------------------------------------------------

        if (!file_exists($filePath)) {
            throw new \Exception('Archivo no encontrado: ' . $filePath);
        }

        //Init the drive instance from the service
        //---------------------------------------------------------------------------------------------
            $driveService->initializeDriveClient($user);
            $drive = $driveService->getDrive();
            $fileMetadata = new \Google_Service_Drive_DriveFile();
        //---------------------------------------------------------------------------------------------


        //Upload file to google drive folder
        //---------------------------------------------------------------------------------------------
            $fileMetadata->setName("cita_n°_" . $this->photoData['quote_number'] . "_photo");
            $folderId = $this->getFolderIdByName('100 citas | carpeta de imagenes', $drive);

            if (!$folderId) {
                $folderMeta = new \Google_Service_Drive_DriveFile(array(
                    'name' => '100 citas | carpeta de imagenes',
                    'mimeType' => 'application/vnd.google-apps.folder'));
                $folder = $drive->files->create($folderMeta, array(
                    'fields' => 'id'));
                $folderId = $folder->id;
            }
            // Establece la carpeta como el parent en el metadata
            $fileMetadata->setParents([$folderId]);

            $content = File::get($filePath);
            $mimeType = File::mimeType($filePath);

            // Delete the file if it already exists
            if (isset($this->photoData['photo_google_id'])) {
                $drive->files->delete($this->photoData['photo_google_id']);
            }

            $file = $drive->files->create($fileMetadata, [
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
                $drive->permissions->create($file->id, $newPermission);
            } catch (Exception $e) {
                print "An error occurred: " . $e->getMessage();
            }
        //---------------------------------------------------------------------------------------------


        //Save file on db by user
        //---------------------------------------------------------------------------------------------
            if ($file) {
                $photo = new Photo();
                $photo->google_drive_id = $file->id;
                //$photo->url_view = "https://drive.google.com/thumbnail?id=" . $file->getID()."=w1000";
                $photo->url_view = "https://lh3.googleusercontent.com/d/" . $file->getID()."=w1000";
                $photo->save();

                $userQuote->photo_id = $photo->id;
                $userQuote->save();

                $userPhoto = new UserPhoto();
                $userPhoto->user_id = $this->photoData['user_id'];
                $userPhoto->photo_id = $photo->id;
                $userPhoto->quote_id = $quoteNumber;
                $userPhoto->save();

            }
        //---------------------------------------------------------------------------------------------

        unlink($filePath);
    }

    private function getFolderIdByName($folderName, $drive)
    {
        $response = $drive->files->listFiles([
            'q' => "mimeType='application/vnd.google-apps.folder' and name='{$folderName}' and trashed=false",
            'spaces' => 'drive',
            'fields' => 'files(id, name)'
        ]);

        foreach ($response->getFiles() as $file) {
            if ($file->getName() === $folderName) {
                return $file->getId();
            }
        }

        return null;
    }
}
