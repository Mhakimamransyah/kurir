<?php 

// Helper untuk mengirimkan pesan menggunakan FCM

namespace App\Http\Helper;
use App\Model\User;
use App\Model\Pelanggan;
use App\Model\Kurir;
use App\Model\Order_m;

class Notification{
    
    public function __construct()
    {
        //
    }

    public static function send($param){
       $to = [$param['token_fcm']];
       $result = fcm()->to($to)->priority('high')->timeToLive(0)
       ->data([
       	  'data'  => $param['data']
        ]) ->notification([
          'title' => $param['title'],
          'body'  => $param['body'],
        ])->send();

        return $result;
    }

    public static function send_notif($id,$type,$param){
      $token = "";
      if($type == "user"){
        $token = User::where("id_user",$id)->first()->token_fcm;
      }else if($type == "pelanggan"){
        $token = Pelanggan::find($id)->user->token_fcm;
      }else if($type == "kurir"){
        $token = Kurir::find($id)->user->token_fcm;
      }else if($type == "order_id_pelanggan"){
        // menggunakan id order dan kirim ke pelanggan
        $token = Order_m::find($id)->pelanggan->token_fcm;
      }else if($type == "order_id_kurir"){
        // menggunakan id order dan kirim ke kurir
        
      }

      $to = [$token];
      $result = fcm()->to($to)->priority('high')->timeToLive(0)
       ->data([
          'data'  => $param['data']
        ]) ->notification([
          'title' => $param['title'],
          'body'  => $param['body'],
        ])->send();

        return $result;
    }


}