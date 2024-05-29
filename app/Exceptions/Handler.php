<?php

namespace App\Exceptions;

use Throwable;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Request;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        if($exception->getMessage() != "Unauthenticated.")
        {
            $user = 'public';
            if(Auth::check())
            {
                $user = Auth::user()->email;
            }
            if($exception instanceof ValidationException)
            {
                $errors = json_encode($exception->errors());
                Log::info($exception->getMessage(),['user'=>$user,'url'=>Request::url(),"ip"=>Request::ip(),'errors'=>$errors]);

            }
            else{// normal error
                if ($exception->getMessage() != 'CSRF token mismatch.') {

                    Log::info($exception->getMessage(),['user'=>$user,'url'=>Request::url(),"ip"=>Request::ip(),'input'=>Request::except('picture')]);
                    parent::report($exception);

                }
            }
        }
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }
}
