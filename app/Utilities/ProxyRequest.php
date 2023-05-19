<?php

namespace App\Utilities;

class ProxyRequest
{

    /*
     * function to grant password token
     * we make a post request with the params
     */
    public function grantPasswordToken(string $email, string $password)
    {
        $params = [
            'grant_type' => 'password',
            'username' => $email,
            'password' => $password,
        ];

        return $this->makePostRequest($params);
    }

    /*
     * function to refresh access token
     * we are checking if the request contains refresh_token 
     * if it does we are setting the parameters for refreshing the token and make POST request
     */
    public function refreshAccessToken()
    {
        //Get the refresh_token from cookie
        //-----------------------------------------------------
            $refreshToken = request()->cookie('refresh_token');
        //-----------------------------------------------------

        //if it does not exist we return a 403 error the token expired
        //----------------------------------------------------------------------
            abort_unless($refreshToken, 403, 'Your refresh token is expired.');
        //----------------------------------------------------------------------

        //if it exists, we create a variable with the parameters to send to the post call
        //---------------------------------------------------------------------------------
            $params = [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ];
        //---------------------------------------------------------------------------------
        return $this->makePostRequest($params);
    }

    /*
     * key function, takes care of setting the client_id and client_secret from the config file, 
     * and makes an internal post call to the passport routes
     */
    protected function makePostRequest(array $params)
    {
        //we add the client_id and client_secret data to the incoming parameters
        //---------------------------------------------------------------------------------
            $params = array_merge([
                'client_id' => config('services.passport.password_client_id'),
                'client_secret' => config('services.passport.password_client_secret'),
                'scope' => '*',
            ], $params);
        //---------------------------------------------------------------------------------

        //we decode the content of the json response and set the http cookie
        //---------------------------------------------------------------------------------
            $proxy = \Request::create('oauth/token', 'post', $params);
            
            $resp = json_decode(app()->handle($proxy)->getContent());
            $this->setHttpOnlyCookie($resp->refresh_token);
        //---------------------------------------------------------------------------------

        return $resp;
    }

    /*
     * set a unique cookie with the refresh_token attribute in the response
     */
    protected function setHttpOnlyCookie(string $refreshToken)
    {
        cookie()->queue(
            'refresh_token',
            $refreshToken,
            14400, // 10 days
            null,
            null,
            false,
            true // httponly
        );
    }
}