<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Laravel\Passport\Token;
use Laravel\Passport\Client;
use Laravel\Passport\AuthCode;
use Laravel\Passport\PersonalAccessClient;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        //'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::useTokenModel(Token::class); 
        Passport::useClientModel(Client::class); 
        Passport::useAuthCodeModel(AuthCode::class); 
        Passport::usePersonalAccessClientModel(PersonalAccessClient::class);

        //seteamos el tiempo de vencimiento de los tokens
        //Passport::tokensExpireIn(now()->addMinutes(60));DESCOMENTAR
        Passport::refreshTokensExpireIn(now()->addDays(10));

        //
    }
}
