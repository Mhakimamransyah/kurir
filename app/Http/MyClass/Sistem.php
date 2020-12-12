<?php 

namespace App\Http\MyClass;
use App\Model\Order_m;
use App\Model\Kurir;
use App\Model\Order_kurir;
use App\Http\Helper\ResponseBuilder;
use App\Http\Helper\Notification;
use App\Model\Sistem_chat;
use App\Model\Pelanggan;
use App\Model\Sistem_feedback;
use App\Model\Role;
use App\Model\User;

//use Exception;
use DB;

class Sistem{

  // Kelas untuk handle yg berkaitan dengan sistem

  public function __construct()
  {

  }

  public static function pesanan_pengiriman_aktif($param){
   $result = Order_m::where("id_pelanggan",$param["id_pelanggan"])->where("rating",null)->where("destination_failed","=","0")->where("rating",null)->where(function ($query){
    $query->where("status","kurir_setuju")->orWhere("status","pelanggan_setuju")->orWhere("status","pelanggan_menunggu_konfirmasi_charge")->orWhere("status","pelanggan_setuju")->orWhere("status","selesai");
  });
       // var_dump($result->toSql());
   return ResponseBuilder::result(true,"Sukses, Pesan di tambahkan",$result->first(),200,true);
 }

 public static function send_chat($param){

   Sistem_chat::create([
     "pesan" => $param['pesan'],
     "id_user_pengirim" => $param["id_user"],
     "id_order" => $param["id_order"]
   ]);

   $role_saya = User::find($param["id_user"])->role->jenis;

   $user = User::find($param["id_user"]);
   
   $nama;
   if($role_saya == "pelanggan"){
    $nama = $user->pelanggan->nama_pelanggan;
  }else{
    $nama = $user->kurir->nama_kurir;
  }



     // FCM here
  $notif = Notification::send_notif($param["id_user_tujuan"],"user",["data" => [],"title" => "Pesan Baru", "body" => "Pesan masuk dari ".$nama]);

     // ambil listing
  $listing = Sistem_chat::where("id_order",$param["id_order"])->get();
  return ResponseBuilder::result(true,"Sukses, Pesan di tambahkan",$listing,200,true);
}

public static function read_rating_kurir($param){
  $order_kurir = Order_kurir::where("id_kurir",$param['id_kurir'])->where("aksi","!=","Tolak")->where("aksi","!=","Baru")->get();

  $counting_finish_order = 0;
  $sum = 0;
  foreach ($order_kurir as $value) {
    $id_order = $value->id_order;
    $order    = Order_m::where("id_order",$id_order);
    if($order->first()->status == "selesai" && $order->first()->rating != NULL){
      $sum = $sum + $order->first()->rating;
      $counting_finish_order++;
    }
  }
  
  $result = 0;
  if($counting_finish_order > 0){
    $result = ($sum/$counting_finish_order)*20;
    $result = (float)sprintf("%.2f", $result);
  }
  
  return $result;
}

public static function post_feedback($param){
 $pelanggan = Pelanggan::find($param["id_pelanggan"]);
 if($pelanggan != NULL){
  $feedback = Sistem_feedback::where("id_pelanggan",$param['id_pelanggan'])->count();
  if($feedback > 0){
    throw new \App\Exceptions\MyException([
     "message" => "Anda telah mengirimkan feedback sebelumnya"
   ]);
  }else{
    Sistem_feedback::create([
     "review" => $param['review'],
     "tipe" => $param["tipe"],
     "id_pelanggan" => $param["id_pelanggan"]
   ]);
    return ResponseBuilder::result(true,"Sukses, feedback dikirim",[],200,true);
  }
}else{
  throw new \App\Exceptions\MyException([
   "message" => "Id Pelanggan tidak ditemukan"
 ]);
}

}

public static function kurir_logout($param){
  $res = Kurir::where("id_kurir",$param["id_kurir"])->update(["sesi_login_aktif"=>"tidak"]);
  return ResponseBuilder::result(true,"Sukses, kurir logout",[],200,true); 
}

public static function read_chat($param){
     // cari id_user_lawan
 $lawan = Sistem_chat::where("id_order",$param['id_order'])->where("id_user_pengirim","!=",$param["id_user"]);


 if(isset($param['counting_badge'])){
  $jumlah = 0;
  if($lawan->exists()){
   $id_lawan = $lawan->first()->id_user_pengirim;
   $jumlah = Sistem_chat::where("id_user_pengirim",$id_lawan)->where("id_order",$param['id_order'])->where("terbaca", "!=","ya")->count();
 }
    // hanya untuk badge baca jumlah pesan belum terbaca dari lawan
 return ResponseBuilder::result(true,"Sukses, counting badge",$jumlah,200,true);  

}else{
  if($lawan->exists()){
   $id_lawan = $lawan->first()->id_user_pengirim;
        // sudah pernah ada chat yang masuk sebelumnya dari lawan
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



}

?>

