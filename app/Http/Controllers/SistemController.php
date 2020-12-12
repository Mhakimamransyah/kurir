<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Model\Sistem_destinasi;
use App\Model\Sistem_image_information;
use App\Model\Sistem_feedback;
use App\Http\Helper\ResponseBuilder;
use App\Http\Helper\Notification;
use App\Http\MyClass\Sistem;

class SistemController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
         //$this->middleware('auth');
         $this->middleware('trans');   
    }

    public function sistem_destinasi(Request $request){
       $this->validate($request,[
          "klausa" => "required"
       ]);
       $result = Sistem_destinasi::where('verified','ya')->where('alamat','LIKE',"%".$request->klausa.'%')->get();
       return ResponseBuilder::result(true,"Sukses",$result,200);
    }

    public function sistem_send_chat(Request $request){
       $this->validate($request,[
          "pesan" => "required",
          "id_user" => "required|regex:/[0-9]/",
          "id_order" => "required|regex:/[0-9]/",
          "id_user_tujuan" => "required | regex:/[0-9]/" // untuk notifikasi
       ]);

       return Sistem::send_chat($request->toArray());
    }

    public function sistem_read_chat(Request $request){
      // listing response
       // set read terbaca
      // set read baca pada id lawannya
      $this->validate($request,[
          "id_user" => "required|regex:/[0-9]/",
          "id_order" => "required|regex:/[0-9]/"
       ]);
       return Sistem::read_chat($request->toArray());
    }

    public function order_pengiriman_aktif(Request $request){
      $this->validate($request,[
          "id_pelanggan" => "required|regex:/[0-9]/",
       ]);
      return Sistem::pesanan_pengiriman_aktif($request->toArray());
    }

    public function sistem_landing(Request $request){
       //banner 
       $this->validate($request,[
          "id_pelanggan" => "required|regex:/[0-9]/",
       ]);
       $result = Sistem_image_information::where("active",'ya')->orderBy('sequence','asc')->get();
       $feedback = Sistem_feedback::where("id_pelanggan",$request->id_pelanggan)->count();
       $response = [
         "jumlah_banner"      => $result->count(),
         "banner"             => $result,
         "feedback_form" => ($feedback>0)? false: true
       ];

       return ResponseBuilder::result(true,"Sukses",$response,200);
    }

    public function sistem_feedback(Request $request){
     $this->validate($request,[
          "id_pelanggan" => "required|regex:/[0-9]/",
          "tipe"         => "required",
          "review"       => "required"
       ]);
      // periksa apakah sudah ada review sebelumnya
      return Sistem::post_feedback($request->toArray());
    }

    public function sistem_page(Request $request){
       $this->validate($request,[
          "page" => "required"
       ]);
       // periksa apakah file ada

       return view('page.'.$request->page);
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
