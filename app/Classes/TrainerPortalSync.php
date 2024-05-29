<?php

namespace App\Classes;

use App\Classes\TrainerPortalJWT;

class TrainerPortalSync
{
    protected $trainerPortal;

    public function __construct()
    {
        $this->trainerPortal = new TrainerPortalJWT();
    }

    public function deactivateAccount($email)
    {
        $fields['email']    = $email;

        // $end_point          = 'http://trainer-portal.local/api/v1/deactivate/ems/employee';
        $end_point = 'https://tkatrainerportal.com/api/v1/deactivate/ems/employee';

        $response           = $this->trainerPortal->post($end_point, $fields);
        
        if($response->getStatusCode() == 200){
            return $response->getBody()->getContents();
        } else{
            return null;
        }
    }
}
