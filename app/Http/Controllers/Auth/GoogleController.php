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

    public function googleLoginUrl()
    {
        return Response::json([
            'url' => Socialite::driver('google')->stateless()->with(['access_type' => 'offline', 'scope'=>"openid profile email https://www.googleapis.com/auth/drive", 'response_type'=>"code"])->redirect()->getTargetUrl(),
        ]);
    }


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

    public function refreshGoogleToken()
    {
        $googleProvider = Socialite::driver('google');
        $oldAccessToken = auth('api')->user()->access_token;

        $newAccessToken = $googleProvider->getAccessTokenResponse($oldAccessToken);
        $user = User::where('id', auth('api')->user()->id)->first();
        $user->access_token = $newAccessToken;
        $user->update();

        return response()->json([
            'new_google_access_token' => $user->access_token
        ]);
    }

    public function googleLogin(Request $request)
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