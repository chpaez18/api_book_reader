<?php

namespace App\Services\Codes;

use Carbon\Carbon;

use App\Models\Code;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;


use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Illuminate\Support\Facades\Mail;
use App\Mail\Mailer;


class CodeService
{

    
    public function getCodes()
    {

        //Get code list
        //------------------------------------------------------------------------------------------------
            $codes = Code::select('id', 'name', 'email', 'code', 'created_at', 'status')->get();
        //------------------------------------------------------------------------------------------------

        return $codes;
    }

    public function generateCode($request)
    {
        //Get Data
        //------------------------------------------------------------------------------------------------
            $name = $request->input('name');
            $email = $request->input('email');
            $user = auth('api')->user();
        //------------------------------------------------------------------------------------------------
        
        //Save Code
        //------------------------------------------------------------------------------------------------
            $randomPart = $this->getRandomString(10); // Ajusta la longitud a 10 para dejar espacio para la fecha y el separador
            $datePart = date("is");
            $code = substr(base64_encode($randomPart . "-" . $datePart), 0, 20);
            $model = Code::updateOrCreate(
                [
                    //Attributes for search record
                    'email' => $email,
                ],
                [
                    //Attributes for update is exists or create
                    'name' => $name,
                    'email' => $email,
                    'code' => $code
                ]
            );
        //------------------------------------------------------------------------------------------------

        // Send email to user with the code
        //------------------------------------------------------------------------------------------------
            if (env('SEND_MAILS')) {
                Mail::to($email)
                ->send(new Mailer($code, 'emails.bienvenida', '¡Bienvenid@ a 100 Citas!'));
            }
        //------------------------------------------------------------------------------------------------

        return $model;
    }

    public function validateCode($request)
    {
        //Get Data
        //------------------------------------------------------------------------------------------------
            $code = $request->input('code');
            $user = auth('api')->user();
        //------------------------------------------------------------------------------------------------
    
        //Validate Code
        //------------------------------------------------------------------------------------------------
            $model = Code::where('code', $code)->first();
        
            if (!$model) {
                return [
                    "message" => "El código no existe",
                    "code" => Response::HTTP_OK
                ];
            }
        
            if ($model->status == Code::Inactive) {
                return [
                    "message" => "Código invalido",
                    "code" => Response::HTTP_OK
                ];
            }
        
            if ($model->is_validated == 1) {
                return [
                    "message" => "Este código ya ha sido válidado",
                    "code" => Response::HTTP_OK
                ];
            }
        //------------------------------------------------------------------------------------------------
            
        $model->is_validated = 1;
        $model->save();

        return [
            "message" => "Código válido",
            "code" => Response::HTTP_OK
        ];
    }

    public function chanegCodeStatus($request)
    {
        //Get Data
        //------------------------------------------------------------------------------------------------
            $email = $request->input('email');
            $code = Code::where('email', $email)->first();

            if (!$code) {
                throw new ModelNotFoundException;
            } 
        //------------------------------------------------------------------------------------------------

        //Change Status
        //------------------------------------------------------------------------------------------------
            $newStatus = ($code->status == Code::Inactive ? Code::Active : Code::Inactive);
            $code->update(['status' => $newStatus]);
        //------------------------------------------------------------------------------------------------

        return true;
    }


    public function deleteCode($id)
    {
        //Get Data
        //------------------------------------------------------------------------------------------------
            $model = Code::where('id', $id)->first();

            if ($model == null) {
                return [
                    "message" => "Codigo no existe",
                    "code" => Response::HTTP_OK
                ];
            }
        //------------------------------------------------------------------------------------------------

        //Delete Code
        //------------------------------------------------------------------------------------------------
            if ($model->delete()) {
                return [
                    "message" => "El codigo ha sido eliminado",
                    "code" => Response::HTTP_OK
                ];
            }
        //------------------------------------------------------------------------------------------------

        return true;
    }

    protected function getRandomString($length = 16)
    {
        $stringSpace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $stringLength = strlen($stringSpace);
        $randomString = '';
        for ($i = 0; $i < $length; $i ++) {
            $randomString = $randomString . $stringSpace[rand(0, $stringLength - 1)];
        }
        return $randomString;
    }

}
