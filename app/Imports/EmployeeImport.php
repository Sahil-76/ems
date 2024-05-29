<?php

namespace App\Imports;

use App\User;
use App\Models\Role;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Qualification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeeImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
      
    
            ini_set('max_execution_time', -1);
            ini_set('memory_limit', -1);

            $length = rand(8,10);
            $alphabet = '@#-_$%^&+=!1234567890abcdefghijklmnopqrstuvwxyz1234567890@#-_$%^&+=!1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ@#-_$%^&+=!1234567890@#-_$%^&+=!';
            $pass = array();
            $alphaLength = strlen($alphabet) - 1;
            for ($i = 0; $i < $length; $i++) {
                $n = rand(0, $alphaLength);
                $pass[] = $alphabet[$n];
            }
           $password=implode($pass);
         
           $user=new User();
           $user->name= $row['employee'];
           $user->email =$row['email'];
           $user->is_active =1;
           $user->password= \Hash::make($password);
           $user->save();
           $user->rawPassword = $password;

    
           $role=Role::where('name','employee')->first();
           $user->roles()->sync($role->id);
           
           $data['user']=$user;
           Mail::send('email.password', $data , function($message) use ($user){
            $message->to($user->email, $user->name)->subject('Login Created');
           });
           $department= Department::where("name",$row['department'])->first();
      
           $employee =new Employee();
           $employee->department_id =$department->id ?? null;
           $employee->name          =$user->name;
           $employee->user_id      = $user->id;
           $employee->office_email = $user->email;
           $employee->is_active    = 1;
           $employee->save();
           
      
    }

}
