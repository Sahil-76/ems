<?php

namespace App\Classes;

use App\Classes\Cliv;
use Illuminate\Support\Facades\Log;

class ClivSync{
    protected $cliv;

    public function __construct(){
        $this->cliv = new Cliv();
    }

    public function deactivateAccount($email, $exitDate)
    {
        $fields['email']    = $email;
        $fields['leaving_date'] = $exitDate;

        // $end_point = 'http://127.0.0.1:8000/api/deactivate/ems/user';
        $end_point = 'https://cliv.tka-in.com/api/deactivate/ems/employee';


        $response =$this->cliv->post($end_point, $fields);


        if($response->getStatusCode() == 200){
            return $response->getBody()->getContents();
        } else{
            return null;
        }
    }


}
