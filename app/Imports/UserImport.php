<?php

namespace App\Imports;

use App\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Hash;

class UserImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $explode_name=explode(' ',$row['name']);
       
        $row['email']=$explode_name[0].'.'.$explode_name[1]."@themsptraining.com";
        $user = User::where('email',$row['email'])->get();
     
        if($user->isEmpty())
        {
          
        return new User([
            'name'                =>    $row['name'],
            'email'               =>    $row['email'],
            'password'            =>    Hash::make("welcom123"),
        ]);
        }
    }
}
