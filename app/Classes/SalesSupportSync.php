<?php

namespace App\Classes;

use App\Classes\Sales;

class SalesSupportSync{
    protected $sales;

    public function __construct(){
        $this->sales = new Sales();
    }

    public function deactivateAccount($email, $exitDate)
    {
        $fields['email']    = $email;
        $fields['leaving_date'] = $exitDate;
        // if ($type == 'Internal') {
        //     $end_point = 'https://cliv.tka-in.com/api/send/tutor/detail';
        // }else {
            $end_point = 'https://salessupport.theknowledgeacademy.com/api/v1/deactivate/ems/employee';
        // }

        $response =$this->sales->post($end_point, $fields);

        if($response->getStatusCode() == 200){
            return $response->getBody()->getContents();
        } else{
            return null;
        }
    }


}
