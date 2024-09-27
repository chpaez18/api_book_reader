<?php
namespace App\Http\Controllers\Auth;

use Google;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Drive\DriveService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Passport\PersonalAccessTokenResult;

class GoogleController extends Controller
{

    /**
     * SIN USAR
     */
    public function googleLoginUrl()
    {
        return Response::json([
            'url' => Socialite::driver('google')->stateless()->with(['access_type' => 'offline', 'scope'=>"openid profile email https://www.googleapis.com/auth/drive", 'response_type'=>"code"])->redirect()->getTargetUrl(),
        ]);
    }


    /**
     * SIN USAR
     */
    public function loginCallback(Request $request)
    {
        // Intercambia el código de autorización por un token de acceso
        $response = Socialite::with('google')->getAccessTokenResponse($request->code);
        $accessToken = $response['access_token'];
        $refreshToken = $response['refresh_token'];


        // Obtiene la información del usuario de Google utilizando el token de acceso
        $user = Socialite::driver('google')->userFromToken($accessToken);
        //$actualToken = $user->token;
        $actualToken = $refreshToken;

        // Verifica si el usuario ya existe en la base de datos.
        $existingUser = User::where('email', $user->getEmail())->first();

        if ($existingUser) {
            // Si el usuario ya existe, inicia sesión y crea un token de acceso.

            $token = $existingUser->createToken('access_token')->accessToken;
            $user = $existingUser;
            $user->access_token = $actualToken;
            $user->update();
        } else {
            // Si el usuario no existe, crea uno nuevo y crea un token de acceso.
            $newUser = User::create([
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'password' => bcrypt('secret'), // Puedes establecer una contraseña aleatoria o solicitar al usuario que la cambie.
                'google_id' => $user->id,
                'access_token' => $user->token
            ]);

            //Asignamos el rol
            $newUser->assignRole('Buyer');

            $token = $newUser->createToken('access_token')->accessToken;
            $user = $newUser;
            $user->access_token = $token;
            $user->update();

        }

        return redirect(env('APP_FRONT_URL').'/dashboard?token=' . $token);
        /* return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $newUser,
        ]); */
    }


    public function refreshAccessToken(User $user)
    {
        $client = new \Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));

        // Refrescar el token si hay un refresh_token almacenado
        if ($user->refresh_token) {
            $newAccessToken = $client->fetchAccessTokenWithRefreshToken($user->refresh_token);

            if (isset($newAccessToken['error'])) {
                return response()->json(['error' => 'Error al refrescar el token: ' . $newAccessToken['error']], 400);
            }

            // Guardar el nuevo access_token
            $user->access_token = $newAccessToken['access_token'];
            $user->save();

            return response()->json(['access_token' => $newAccessToken['access_token']], 200);
        }

        return response()->json(['error' => 'No refresh token available.'], 400);
    }


    public function googleLogin(Request $request)
    {
        // Obtenemos el authorization code desde el frontend
        //--------------------------------------------------------
            $code = $request->token;
        //--------------------------------------------------------

        // Configuramos el cliente de Google
        //-----------------------------------------------------------------------
            $client = new \Google_Client();
            $client->setClientId(env('GOOGLE_CLIENT_ID'));
            $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
            $client->setAccessType("offline");
            $client->setRedirectUri(env('GOOGLE_REDIRECT_URL'));
        //-----------------------------------------------------------------------

        // Intercambiamos el authorization code por un access_token y refresh_token
        //-----------------------------------------------------------------------
            $tokenResponse = $client->fetchAccessTokenWithAuthCode($code);
            if (isset($tokenResponse['error'])) {
                return response()->json(['error' => $tokenResponse], 400);
            }

            $accessToken = $tokenResponse['access_token'];
            $refreshToken = isset($tokenResponse['refresh_token']) ? $tokenResponse['refresh_token'] : null;
        //-----------------------------------------------------------------------


        // Verificamos si el usuario ya existe en la base de datos.
        //-----------------------------------------------------------------------
            $user = Socialite::driver('google')->userFromToken($accessToken);
            $existingUser = User::where('email', $user->getEmail())->first();

            if ($existingUser) {

                // Si el usuario ya existe, iniciamos sesión y creamos un token de acceso(para la app de laravel).
                //----------------------------------------------------------------------------------------------------
                    $token = $existingUser->createToken('access_token')->accessToken;
                    $user = $existingUser;
                    $user->access_token = $accessToken;
                    //$user->refresh_token = $refreshToken; // No se actualiza el refresh_token, porque se supone que ya tiene uno.
                    $user->update();
                //----------------------------------------------------------------------------------------------------

            } else {

                // Si el usuario no existe, lo registramos en la tabla users y creamos un token de acceso(para la app de laravel).
                //----------------------------------------------------------------------------------------------------------------
                    $newUser = User::create([
                        'name' => $user->getName(),
                        'email' => $user->getEmail(),
                        'password' => bcrypt('secret'),
                        'access_token' => $accessToken,
                        'refresh_token' => $refreshToken,
                    ]);

                    //Asignamos el rol del usuario
                    $newUser->assignRole('Buyer');

                    $token = $newUser->createToken('access_token')->accessToken;// para la app de laravel
                    $user = $newUser;
                    $user->access_token = $accessToken; // El acces_token que se obtiene de google
                    $user->refresh_token = $refreshToken; // Guardamos el refresh_token ya que es la primera vez que se registra.
                    $user->update();
                //----------------------------------------------------------------------------------------------------------------


            }
        //-----------------------------------------------------------------------

        return response()->json([
            'access_token' => $token,
            'refresh_token' => $user->refresh_token,
        ]);
    }

    /**
     * RESPALDO
     */
    public function googleLoginRespaldo(Request $request)
    {
        $googleToken = $request->token;
        $user = Socialite::driver('google')->userFromToken($googleToken);

        // Verificamos si el usuario ya existe en la base de datos.
        //-----------------------------------------------------------------------
        $existingUser = User::where('email', $user->getEmail())->first();

        if ($existingUser) {
            // Si el usuario ya existe, inicia sesión y crea un token de acceso.

            $token = $existingUser->createToken('access_token')->accessToken;
            $user = $existingUser;
            $user->access_token = $googleToken;
            $user->update();
        } else {
            // Si el usuario no existe, crea uno nuevo y crea un token de acceso.
            $newUser = User::create([
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'password' => bcrypt('secret'), // Puedes establecer una contraseña aleatoria o solicitar al usuario que la cambie.
                'access_token' => $googleToken
            ]);

            //Asignamos el rol
            $newUser->assignRole('Buyer');

            $token = $newUser->createToken('access_token')->accessToken;
            $user = $newUser;
            $user->access_token = $googleToken;
            $user->update();
        }
        //-----------------------------------------------------------------------

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'code_validated' => (isset($user->code) ?  $user->code->is_validated : 0)
        ]);
    }
}

