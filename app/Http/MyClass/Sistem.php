<?php 

namespace App\Http\MyClass;
use App\Model\Order_m;
use App\Http\Helper\ResponseBuilder;
use App\Http\Helper\Notification;
use App\Model\Sistem_chat;
use App\Model\Role;
use App\Model\User;

//use Exception;
use DB;

class Sistem{
  
  // Kelas untuk handle yg berkaitan dengan sistem

  public function __construct()
  {
           
  }

  public static function send_chat($param){
     
     Sistem_chat::create([
       "pesan" => $param['pesan'],
       "id_user_pengirim" => $param["id_user"],
       "id_order" => $param["id_order"]
     ]);

     $role_saya = User::find($param["id_user"])->role->jenis;

     // FCM here
     $notif = Notification::send_notif($param["id_user_tujuan"],"user",["data" => [],"title" => "Pesan Baru", "body" => "Pesan masuk dari ".$role_saya]);

     // ambil listing
     $listing = Sistem_chat::where("id_order",$param["id_order"])->get();
     return ResponseBuilder::result(true,"Sukses, Pesan di tambahkan",$listing,200,true);
  }

  public static function read_chat($param){
     // cari id_user_lawan
     $lawan = Sistem_chat::where("id_order",$param['id_order'])->where("id_user_pengirim","!=",$param["id_user"]);

     if($lawan->exists()){
        // sudah pernah ada chat yang masuk sebelumnya dari lawan
       $id_lawan = $lawan->first()->id_user_pengirim;
       // update seluruh pesan dari id lawan dengan status terbaca
       $chat_lawan = Sistem_chat::where("id_user_pengirim",$id_lawan);
       $chat_lawan->update([
          "terbaca" => "ya"
       ]);
     }

     //ambil listing
     $listing = Sistem_chat::where("id_order",$param["id_order"])->get();
     return ResponseBuilder::result(true,"Sukses",$listing,200,true);  
  }

 

}

?>

