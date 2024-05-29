<?php

namespace App\Traits;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

trait Encryptable {

    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);
        if (in_array($key, $this->encryptable) && ( ! is_null($value)))
        {
            try {
                $value = Crypt::decrypt($value);
            }catch (DecryptException $e) {
                $value = $value;
            }
        }
        return $value;
    }

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->encryptable))
        {
            $value = Crypt::encrypt($value);
        }

        return parent::setAttribute($key, $value);
    }

    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();
        
        foreach ($attributes as $key => $value) 
        {
            if (in_array($key, $this->encryptable)) 
            {
                if (isset($value) && $value !== '' && !is_null($value)) 
                {
                    $attributes[$key] = decrypt($value);
                }
            }
        }

        return $attributes;
    }
}