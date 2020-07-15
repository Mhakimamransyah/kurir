<?php

namespace App\Exceptions;

use Exception;
use DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Http\Helper\ResponseBuilder;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof ValidationException) {
             $response = [];
             $response['message'] = $exception->getResponse()->original; 
             return ResponseBuilder::result(false,$response['message'],[],400);        
        }

        if ($exception instanceof \PDOException) { //Database exception
             $response = [];
             $response['message'] = $exception->errorInfo[2]; 
             DB::rollBack();
             return ResponseBuilder::result(false,$response['message'],[],400);        
        }

        if($exception instanceof QueryException){
          return ResponseBuilder::result(false,"aduh salah di Query Exception",[],400);
        }

        return parent::render($request, $exception);
    }
}
