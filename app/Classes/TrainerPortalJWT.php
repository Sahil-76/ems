<?php

namespace App\Classes;

use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class TrainerPortalJWT 
{
    protected $client;

    public function __construct()
    {
       $options = [
           'verify'    =>  false,
           'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
       ];
       $this->client = new Client($options);
    }

    public function generateJWT($fields)
    {
        date_default_timezone_set("Europe/London");
        $data = [
            'exp'   => time() + 60*120,
            'sub'   => 'Trainer Details',
        ];
        $key = config('jwt.secret');
        $data = array_merge($data, $fields);
        $jwt = JWT::encode($data, $key, 'HS256');
        
        return $jwt;
        // JWTAuth::getJWTProvider()->setSecret( config('jwt.secret'));
        // $customClaims = JWTFactory::customClaims($data);
        // $payload = JWTFactory::make($customClaims);
        // return JWTAuth::encode($payload)->get();
    }

    public function get($end_point, $query = [])
    {
        try {
            return $this->client->request('GET', $end_point,['query'=>$query]);
        } catch (RequestException $e) {
            return $e->getResponse();
        }
    }

    public function post($end_point, $fields=[],$query=[])
    {
        $token  = $this->generateJWT($fields);
        $data   = ['_token' =>  $token];

        try {
            return $this->client->post($end_point, [
                'form_params'  => $data,
                'query' => $query
            ]);
        } catch (RequestException $e) {
            return $e->getResponse();
        }
    }

}
