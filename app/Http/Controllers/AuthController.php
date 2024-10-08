<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Utilities\ProxyRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    use ApiResponse;
    
    /**
     * @var ProxyRequest
     */
    protected $proxy;

    /**
    * @OA\Get(
    *     path="/",
    *     description="Home page",
    *     @OA\Response(response="default", description="Welcome page")
    * )
    */
    public function __construct(ProxyRequest $proxy)
    {
        $this->proxy = $proxy;
    }

    /** 
     * function for register a new user, SIN USAR
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function register(): JsonResponse
    {
        //We validate data send by request
        //-----------------------------------------------------
            $data = [];
            $this->validate(request(), [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
            ]);
        //-----------------------------------------------------

        try {
            //Save a new user
            //-----------------------------------------------------
                DB::beginTransaction();
                    $user = User::create([
                        'email' => request('email'),
                        'password' => bcrypt(request('password')),
                    ]);
                DB::commit();
            //-----------------------------------------------------
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->showMessage($exception->getMessage(), 500);
        }


        //We grant a token to the password
        //-----------------------------------------------------
            $resp = $this->proxy->grantPasswordToken(
                $user->email,
                request('password')
            );
        //-----------------------------------------------------

        $data = [
            'token' => $resp->access_token,
            'expiresIn' => $resp->expires_in,
            'user' => $user,
        ];
        
        return $this->successResponse($data, 200);
    }

    /**
     * function to login, returns a token that we will use for authentication
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function login(): JsonResponse
    {
        try {

            $data = [];

            //Get the user info
            //--------------------------------------------------------------
                $user = User::where('email', request('email'))->firstOrFail();
            //--------------------------------------------------------------

        } catch (\Exception $exception) {
            return $this->showMessage($exception->getMessage(), 500);
        }

        //If the data is incorrect, we return an error
        //--------------------------------------------------------------
            if (!$user) {
                return $this->errorResponse('This combination does not exists.', 401);
            }

            if (!\Hash::check(request('password'), $user->password)) {
                return $this->errorResponse('This combination does not exists.', 401);
            }
        //--------------------------------------------------------------

        try {

            //If the user exists we grant a token to the password
            //-------------------------------------------------------------------
                $resp = $this->proxy->grantPasswordToken(request('email'), request('password'));
            //-------------------------------------------------------------------

        } catch (\Exception $exception) {
            return $this->showMessage($exception->getMessage(), 500);
        }
        $user->rol = $user->getRoleNames()[0];
        $data = [
            'token' => $resp->access_token,
            'expiresIn' => $resp->expires_in,
            'user' => $user,
        ];

        return $this->successResponse($data, 200);
    }

    /**
     * function that generates a new token updated to the user, SIN USAR
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function refreshToken(): JsonResponse
    {
        try {

            $data = [];
            $resp = $this->proxy->refreshAccessToken();

        } catch (\Exception $exception) {
            return $this->showMessage($exception->getMessage(), 500);
        }
        
        $data = [
            'token' => $resp->access_token,
            'expiresIn' => $resp->expires_in
        ];
        
        return $this->successResponse($data, 200);
    }


    public function refreshAccessToken(Request $request)
    {
        $client = new \Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));

        // Verifica si hay refresh_token
        if ($user->refresh_token) {
            try {
                // Usa el refresh_token para obtener un nuevo access_token
                $client->refreshToken($user->refresh_token);
                $newAccessToken = $client->getAccessToken();

                // Actualizar el nuevo access_token en la base de datos
                $user->access_token = $newAccessToken['access_token'];
                $user->save();

                return response()->json([
                    'access_token' => $newAccessToken['access_token'],
                ], 200);

            } catch (\Exception $e) {
                // Si hay un error al refrescar el token (e.g., token caducado), devolver mensaje al frontend
                return response()->json([
                    'message' => 'Refresh token expired or invalid, need user consent',
                ], 401); // 401: Unauthorized, necesita reautenticación
            }
        } else {
            // Si no hay refresh_token, indicar que es necesario un nuevo consentimiento
            return response()->json([
                'message' => 'No refresh token, need user consent',
            ], 401); // 401: Unauthorized
        }
    }


    /**
     * function to validate reset token, SIN USAR
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function validateResetToken(Request $request): JsonResponse
    {

        try {

            //We validate data send by request
            //-----------------------------------------------------
                $request->validate([
                    'token' => 'required|string|min:6'
                ]);
                $token = $request->token;
            //-----------------------------------------------------

            //We validate if the token belongs to a user
            //-----------------------------------------------------
                $isValid = User::where('reset_password_token', $token)->first();

                if (!$isValid) {
                    return $this->showMessage('The token is invalid!.', 422);
                }
            //-----------------------------------------------------
        } catch (\Exception $exception) {
            return $this->showMessage($exception->getMessage(), 500);
        }
       
        return $this->showMessage("The token is valid.", 200);
    }

    /**
     * function to reset user password, SIN USAR
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request): JsonResponse
    {
        //We validate data send by request
        //-----------------------------------------------------
            $request->validate([
                'token' => 'required|string|min:6',
                'password' => 'required|string|min:6|',
            ]);
            $data = [
                'token' => $request->token,
                'password' => bcrypt($request->password)
            ];
        //-----------------------------------------------------

        try {
            //Save a new password for user
            //-----------------------------------------------------
                $user = User::where('reset_password_token', $data['token'])->first();
                $user->update([
                    'password' => $data['password'],
                    'reset_password_token' => null,
                    'reset_password_token_created_at' => null
                ]);
            //-----------------------------------------------------
        } catch (\Exception $exception) {
            return $this->showMessage($exception->getMessage(), 500);
        }

        return $this->showMessage("The password has been changed", 200);

    }

    /**
     * function to logout and delete http cookie
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            // delete the user cookie
            //-------------------------------------------------------------------
                $user = User::where('id', request()->user()->id)->first();
                $token = request()->user()->token();

                $user->access_token = '';

                $token->delete();
                $user->update();
            //-------------------------------------------------------------------

            // remove the httponly cookie
            //-------------------------------------------------------------------
                cookie()->queue(cookie()->forget('refresh_token'));
            //-------------------------------------------------------------------

            return $this->showMessage('You have been successfully logged out', 200);

        } catch (\Exception $exception) {
            return $this->showMessage($exception->getMessage(), 500);
        }

    }
}
