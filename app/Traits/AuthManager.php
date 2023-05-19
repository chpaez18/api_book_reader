<?php 

namespace App\Traits;

use App\Models\State;
use Tymon\JWTAuth\Facades\JWTAuth;

trait AuthManager 
{
    protected function responseToken(String $token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
        ]);
    }

	protected function validateCredentials(Array $credentials)
	{
		if ($token = auth()->guard('api')->attempt($credentials))
            return $token;
		throw new \Exception("Datos incorrectos. verifique su email y contraseña.");
	}

    protected function validateRole($roleId)
    {
        $user = auth()->guard('api')->user();
        if (!$user || ($user->role_id != $roleId && !$user->isAdmin()))
            throw new \Exception("No tiene permisos para iniciar sesión por este rol.");
    }

    protected function loginUser($user)
    {
        if (!$user)
            throw new \Exception("Datos incorrectos. verifique su email y contraseña.");
        auth()->guard('api')->login($user);
        $token = JWTAuth::fromUser($user);
        return $token;
    }

    protected function validateState()
    {
        $user = auth()->guard('api')->user();

        if (!$user)
            return false;

        if ($user->state_id != State::enabled()->value('id'))
            throw new \Exception("Este usuario se encuentra deshabilitado.");
    }
}