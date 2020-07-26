<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Model\Sistem_destinasi;
use App\Http\Helper\ResponseBuilder;
use App\Http\Helper\Notification;

class SistemController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
         $this->middleware('auth');
         $this->middleware('trans');   
    }

    public function sistem_destinasi(Request $request){
       $this->validate($request,[
          "klausa" => "required"
       ]);
       $result = Sistem_destinasi::where('verified','ya')->where('alamat','LIKE',"%".$request->klausa.'%')->get();
       return ResponseBuilder::result(true,"Sukses",$result,200);
    }

    public function sistem_notif(Request $request){
       
       $this->validate($request,[
          "token_fcm" => "required",
          "data"      => "required",
          "title"     => "required",
          "body"      => "required"
       ]);

       $result = Notification::send($request->toArray());
       
       if($result['success'] == 1 && $result['failure'] == 0){
          return ResponseBuilder::result(true,"Sukses",[],200);
       }else{
         return ResponseBuilder::result(false,$result["results"][0]['error'],[],400);
       }

    } 
}
