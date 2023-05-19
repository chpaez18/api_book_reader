<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;

use App\Models\User;
use App\Mail\ResetPasswordMail;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Http\Controllers\ApiController;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class ForgotPasswordController extends ApiController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    /**
     * A function that sends a password reset email to the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request): JsonResponse
    {
        // Validate input data
        //----------------------------------------------------------------------
            $request->validate([
                'email' => 'required|email',
            ]);
        //----------------------------------------------------------------------

        //Check if user exists
        //----------------------------------------------------------------------
            $email = $request->only('email');
            $user = User::where('email', $email)->first();
            if (!$user) {
                return $this->errorResponse('User not found', 404);
            }
        //----------------------------------------------------------------------
        
        try {
            //Generate and save recovery token
            //----------------------------------------------------------------------
                $token = Str::random(60);
                $token_hash = Hash::make($token);
                $user->update([
                    'reset_password_token' => $token_hash,
                    'reset_password_token_created_at' => Carbon::now(),
                ]);
                $tokenUrl = url(env('APP_FRONT_URL') . '/auth/reset-password?token='.$token_hash);
            //----------------------------------------------------------------------
            
            //Send email with token for recovery
            //----------------------------------------------------------------------
                Mail::to($user->email)->queue(new ResetPasswordMail($tokenUrl));
            //----------------------------------------------------------------------
        } catch (Exception $exception) {
                
            throw $exception;

        }

        return $this->successResponse(['url' => $tokenUrl, 'reset_token' => $token_hash], 200);
    }
}
