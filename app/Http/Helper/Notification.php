<?php 

// Helper untuk mengirimkan pesan menggunakan FCM

namespace App\Http\Helper;
use App\Model\User;

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


}