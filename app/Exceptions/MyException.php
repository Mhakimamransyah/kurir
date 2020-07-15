<?php 

namespace App\Exceptions;
use App\Http\Helper\ResponseBuilder;


use Exception;
use Illuminate\Http\Response;

class MyException extends Exception
{
    private $param;

    public function __construct($param)
    {
       $this->param = $param;
    }

    public function render()
    {
        return ResponseBuilder::result(false,$this->param['message'],[],400);
    }
}