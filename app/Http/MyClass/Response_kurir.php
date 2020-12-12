<?php 

namespace App\Http\MyClass;
use App\Http\Helper\Distance;
use App\Http\Helper\Destinasi;
use App\Http\Helper\Notification;
use App\Model\Order_m;
use App\Model\Kurir_Kordinat;
use App\Model\Kurir;
use App\Model\Pelanggan_patokan;
use App\Model\Order_Kurir;
use App\Model\Order_jenis;
use App\Model\Order_barang;
use App\Model\Order_destinasi;
use App\Model\Order_detail;
use App\Model\Kurir_Geotracking;
//use Exception;
use DB;

class Response_kurir{
	
	// Kelas hanya untuk response kurir

	private $parameter = [];

	public function __construct()
  {

  }

  public function kurir_setuju($param){
    // periksa apakah sesi sudah berakhir
    $order = Order_Kurir::where("id_order",$param["id_order"])->latest()->first();
    // periksa juga apakah sudah batal atau belum
    $status = Order_m::where("id_order",$param['id_order'])->first()->status;
    if($order->expired_count > 0){
      if($status != "pelanggan_batal"){
        Order_Kurir::where("id_order",$param["id_order"])->where("id_kurir",$param['id_kurir'])->update([
          "aksi" => "Setuju"
        ]);
        Order_m::where("id_order",$param['id_order'])->update([
          "status" => "kurir_setuju"
        ]);
     // update kordinat terkini kurir
        Kurir_Geotracking::where("id_kurir",$param["id_kurir"])->update([
          "kordinat_terkini" => $param["kordinat_kurir"]
        ]);
      }else{
        throw new \App\Exceptions\MyException([
          "message" => "Pelanggan membatalkan pesanan ini, Silahkan kembali"
        ]);
      }
      
    }else{
     throw new \App\Exceptions\MyException([
      "message" => "Sesi telah habis, Silahkan kembali"
    ]);
   }
 }

 public function kurir_tolak($param){
  // periksa apakah sesi sudah berakhir
  $order = Order_Kurir::where("id_order",$param["id_order"])->latest()->first();
  // periksa juga apakah sudah batal atau belum
  $status = Order_m::where("id_order",$param['id_order'])->first()->status;
  if($order->expired_count > 0){
    if($status != "pelanggan_batal"){
      Order_Kurir::where("id_order",$param["id_order"])->where("id_kurir",$param['id_kurir'])->update([
        "aksi" => "Tolak"
      ]);
    }else{
     throw new \App\Exceptions\MyException([
      "message" => "Pelanggan membatalkan pesanan ini, Silahkan kembali"
    ]);
   }

 }else{
  throw new \App\Exceptions\MyException([
    "message" => "Sesi telah habis, Silahkan kembali"
  ]);
}

}

public function kurir_charge($param){
  // kurir melakukan charge order
  $order = Order_Kurir::where("id_order",$param["id_order"])->latest()->first();
  // periksa juga apakah sudah batal atau belum
  $status = Order_m::where("id_order",$param['id_order'])->first()->status;
  if($order->expired_count > 0){
    $result = [];
    try{
     if($status != "pelanggan_batal"){
      DB::beginTransaction();
      Order_detail::where("id_order",$param['id_order'])->where("id_order_barang",$param["id_barang"])->update([
        "tarif_charge_beban" => $param['tarif_charge']
      ]);
      Order_Kurir::where("id_kurir",$param["id_kurir"])->where("id_order",$param["id_order"])->update([
        "aksi" => "Charge"
      ]);
        // update kordinat terkini kurir
      Kurir_Geotracking::where("id_kurir",$param["id_kurir"])->update([
        "kordinat_terkini" => $param["kordinat_kurir"]
      ]);
      DB::commit();

       // FCM DI SINI KASIH TAHU PELANGGAN
       // KURIR MELAKUKAN CHARGE LAGI

      $id_pelanggan = Order_m::where("id_order",$param['id_order'])->first()->id_pelanggan;
     // Notification::send_notif($id_pelanggan,"pelanggan",["data" => ["status" => "kurir_charge"],"title" => "Tarif di charge", "body" => "Pesanan anda di charge oleh kurir"]);
      $notif = Notification::send_notif($id_pelanggan,"pelanggan",["data" => $param["id_order"],"title" => "Tarif di charge", "body" => "Pesanan anda di charge oleh kurir"]);

      return [
       "status" => true
     ];
   }else{
     throw new \App\Exceptions\MyException([
      "message" => "Pelanggan membatalkan pesanan ini, Silahkan kembali"
    ]);
   }


 }catch(Exception $e){
   DB::rollBack();
   return [
     "status" => false,
     "message" => $e->getMessage()
   ];
 }
}else{
 throw new \App\Exceptions\MyException([
  "message" => "Sesi telah habis, Silahkan kembali"
]);
}


}

public function cancel_order($param){
 Order_m::where("id_order",$param["id_order"])->update(["status" => "kurir_batal"]);

       // FCM di SINI
}

public function order_detail_general($param){
  // anomali
  // Digunakan
  $result = [];
  $order_kurir = Order_Kurir::where("id_order",$param['id_order'])->where("id_kurir",$param["id_kurir"]);
  if($order_kurir != null)
  {
    if($order_kurir->first()->expired_count > 0){
      $result = Destinasi::getCompleteOrderDataByIdOrder($param["id_order"]);
    }else{
        // sudah expired
      throw new \App\Exceptions\MyException([
        "message" => "Pesanan sudah expired"
      ]);
    }

  }
  return $result;
}



public function list_order_baru_kurir($param){
  $result = [];
  if(isset($param["id_order"])){
   $result = $this->order_detail_general($param);
 }else{
  $res = Order_Kurir::where("id_kurir",$param["id_kurir"])->where("aksi","Baru")->where("expired_count",">","0")->get();
  $index = 0;
  foreach ($res as $value) {
    $order = Order_m::find($value->id_order);
    if($order->status != "pelanggan_batal"){
     $result[$index]["waktu"] = $value->created_date->format('d-m-Y h:m:s');
     $pelanggan = $order->pelanggan;
     $detail = $order->order_detail;
     $result[$index]["id_order"] = $value->id_order;
     $result[$index]["nama_pelanggan"] = $pelanggan->nama_pelanggan;
     $result[$index]["jumlah_destinasi"] = $detail->count();
     $index++;
   }
 }
}


return $result;
}

public function transaksi_aktif($param){

 $result = [];
 $index = 0;

 $charge = Order_Kurir::where("id_kurir",$param["id_kurir"])->where("aksi","Charge")->orderBy("modified_date","DESC")->get();
 foreach ($charge as $value) {
   $order = Order_m::find($value->id_order);
   $order_status = $order->status;
   if($order_status != "selesai" && $order_status != "pelanggan_batal" && $order_status != "kurir_tidak_response" && $order_status != "kurir_batal"){
     $pelanggan = $order->pelanggan;
     $detail    = $order->order_detail;
     $jenis     = $order->order_jenis;
     $result[$index]["jenis"]            = "Charge";
     $result[$index]["jenis_order"]      = $jenis->jenis;
     $result[$index]["waktu"] = $value->modified_date->format('d-m-Y H:m:s');
     $result[$index]["nama_pelanggan"]   = $pelanggan->nama_pelanggan;
     $result[$index]["jumlah_destinasi"] = $detail->count();
     $result[$index]["status"]           = $order_status;
     $result[$index]["id_order"]         = $value->id_order;
     $index++;
   }
 }
 
 $setuju = Order_Kurir::where("id_kurir",$param["id_kurir"])->where("aksi","Setuju")->orderBy("modified_date","DESC")->get();
 foreach ($setuju as $value) {
  $order = Order_m::find($value->id_order);
  $order_status = $order->status;
  if($order_status != "selesai" && $order_status != "pelanggan_batal" && $order_status != "kurir_tidak_response" && $order_status != "kurir_batal"){
   $pelanggan = $order->pelanggan;
   $detail    = $order->order_detail;
   $jenis     = $order->order_jenis;
   $result[$index]["jenis"]   = "Setuju";
   $result[$index]["jenis_order"]      = $jenis->jenis;
   $result[$index]["waktu"] = $value->modified_date->format('d-m-Y H:m:s');
   $result[$index]["nama_pelanggan"]   = $pelanggan->nama_pelanggan;
   $result[$index]["jumlah_destinasi"] = $detail->count();
   $result[$index]["status"]           = $order_status;
   $result[$index]["id_order"]         = $value->id_order;
   $index++;
 }
}



return $result;

}





}

?>

