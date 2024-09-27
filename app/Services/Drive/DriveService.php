<?php

namespace App\Services\Drive;

use App\Models\User;
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

    /**
     * Inicializa el cliente de Google Drive (esto se implementa para que pueda ser usado en el job)
     */
    public function initializeDriveClient(User $user)
    {
        // Configuramos el cliente de Google
        //---------------------------------------------------------------------------------------------
            $client = new \Google_Client();
            $client->setClientId(env('GOOGLE_CLIENT_ID'));
            $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        //---------------------------------------------------------------------------------------------

        // Obtenemos el token de acceso del usuario, para autenticar el cliente con google
        //---------------------------------------------------------------------------------------------
            $accessToken = $user->access_token;
        //---------------------------------------------------------------------------------------------

        // Establecemos el token de acceso en el cliente
        //---------------------------------------------------------------------------------------------
            $client->setAccessToken($accessToken);
        //---------------------------------------------------------------------------------------------

        // Verificamos si el token ha expirado
        //---------------------------------------------------------------------------------------------
            if ($client->isAccessTokenExpired()) {

                // Refrescamos el token si es necesario, dado que tiene 1 hora de duracion, y si ya expiro, se debe refrescar.
                // Por ello es importante el refresh_token, el cual almacenamos la primera vez que el usuario se autentica.
                //---------------------------------------------------------------------------------------------
                    $refreshToken = $user->refresh_token;

                    if ($refreshToken) {

                        // Refrescamos el access_token usando el refresh_token
                        //---------------------------------------------------------------------------
                            $newAccessToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);
                        //---------------------------------------------------------------------------

                        // Verificamos si hubo un error al refrescar el token
                        //---------------------------------------------------------------------------
                            if (isset($newAccessToken['error'])) {
                                \Log::error($newAccessToken);
                            }
                        //---------------------------------------------------------------------------

                        // Almacenamos el nuevo access_token en la base de datos
                        //---------------------------------------------------------------------------
                            $user->access_token = $newAccessToken['access_token'];
                            $user->save();
                        //---------------------------------------------------------------------------

                        // Establecemos el nuevo access_token en el cliente
                        //---------------------------------------------------------------------------
                            $client->setAccessToken($newAccessToken['access_token']);
                        //---------------------------------------------------------------------------

                    } else {
                        throw new \Exception('No refresh token available.');
                    }
                //---------------------------------------------------------------------------------------------
            }
        //---------------------------------------------------------------------------------------------

        // Creamos una instancia de Google Drive
        //---------------------------------------------------------------------------
            $this->drive = new \Google_Service_Drive($client);
        //---------------------------------------------------------------------------
    }

    /**
     * Obtiene el cliente de Google Drive para realizar operaciones.
     */
    public function getDrive()
    {
        if (!$this->drive) {
            throw new \Exception('Google Drive client has not been initialized.');
        }

        return $this->drive;
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

    public function getFolderIdByName($folderName) {
        $response = $this->drive->files->listFiles([
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

    public function createFile($data){

        //Get data
        //---------------------------------------------------------------------------------------------
            $file = $data['file'];
            $quoteNumber = $data['quote_number'];
        //---------------------------------------------------------------------------------------------

        //Upload file to google drive folder
        //---------------------------------------------------------------------------------------------
            $folderId = self::getFolderIdByName($this->folderName);
            
            if (!$folderId) {
                $folderId = self::createFolder($this->folderName);
            }

            $name = gettype($file) === 'object' ? $file->getClientOriginalName() : $file;
            $name = "cita_n°_".$quoteNumber."_photo";
            $fileMetadata = new \Google_Service_Drive_DriveFile();
            $fileMetadata->setName($name);
            $fileMetadata->setParents([$folderId]);

            $content = File::get($file);
            $mimeType = File::mimeType($file);

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
                //$photo->url_view = "https://drive.google.com/thumbnail?id=" . $file->getID()."=w1000";
                $photo->url_view = "https://lh3.googleusercontent.com/d/" . $file->getID()."=w1000";
                $photo->save();

                $userPhoto = new UserPhoto();
                $userPhoto->user_id = auth('api')->user()->id;
                $userPhoto->photo_id = $photo->id;
                $userPhoto->quote_id = $quoteNumber;
                $userPhoto->save();
            }
        //---------------------------------------------------------------------------------------------

        //$file->id;
        return $photo;
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

            /*$userQuote = UserQuote::where('photo_id', $photo->id)->where('user_id', $userId)->first();

            if ($userQuote) {
                $userQuote->delete();
            } */

            $userPhoto = UserPhoto::where('photo_id', $photo->id)->where('user_id', $userId)->first();

            if ($userPhoto) {
                $userPhoto->delete();
            }

            //$photo->delete();
        }

        $this->drive->files->delete($id);

    }

}
