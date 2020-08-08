<?php 

namespace App\Http\MyClass;
use App\Http\Helper\Distance;
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

  }

  public function kurir_tolak($param){
    Order_Kurir::where("id_order",$param["id_order"])->where("id_kurir",$param['id_kurir'])->update([
        "aksi" => "Tolak"
     ]);
  }

  public function kurir_charge($param){
    // kurir melakukan charge order
     $result = [];
     try{
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
       Notification::send_notif($id_pelanggan,"pelanggan",["data" => ["status" => "kurir_charge"],"title" => "Tarif di charge", "body" => "Pesanan anda di charge oleh kurir"]);

       return [
           "status" => true
       ];
     }catch(Exception $e){
       DB::rollBack();
       return [
           "status" => false,
           "message" => $e->getMessage()
       ];
     }

  }

  public function cancel_order($param){
       Order_m::where("id_order",$param["id_order"])->update(["status" => "kurir_batal"]);
       
       // FCM di SINI
    }

  
   
  

}

?>

