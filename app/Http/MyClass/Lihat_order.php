<?php 

namespace App\Http\MyClass;
use App\Http\Helper\Destinasi;
use App\Http\Helper\Distance;
use App\Http\Helper\Tarif;
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

class Lihat_order{
	
	// Kelas hanya untuk melihat order
  
	private $result = [];

	public function __construct()
  {
        
  }

  public function do_lihat_order($param){
     
     // periksa dulu yang destination failed nya 0
     // periksa jika status tidak sama dengan selesai dengan rating tidak null, pelanggan_batal, kurir_batal,kurir_tidak_ada 
     
     $order = Order_m::where("id_order",$param['id_order'])->first();
     if($order != "" && $order->status != 'pelanggan_batal' && $order->status != 'kurir_batal' && $order->status != 'kurir_tidak_response' && $order->rating == ""){

         $this->destination_failed($param["id_order"]);

         if(isset($param["id_order_detail"])){
               $this->lihat_detail($param);
         }else{
               $this->listing($param);
         }
        return $this->result;
     
     }else{
        throw new \App\Exceptions\MyException([
         "message" => "Order tidak ada"
       ]);
     }

     
  }

  private function listing($param){

     $this->result["jenis"] = "Daftar destinasi";
     $data = Order_Kurir::where("id_order",$param["id_order"])->whereIn("aksi",["Setuju","Charge"])->latest()->first();
     $order  = Order_m::find($param["id_order"]);
     if(!empty($data)){
       $this->result["kondisi_order"]   = $order->status;
       $this->result["kurir"]           = Kurir::find($data->id_kurir)->toArray();
     
       $detail = $order->order_detail;

       $this->result["Jenis_order"]  = Order_m::find($param["id_order"])->order_jenis->toArray();
       
       $list = [];
       $sum  = 0;
       foreach ($detail as $obj_detail) {

          $barang = $obj_detail->order_barang;
          $destinasi = Destinasi::get_detail($obj_detail->order_destinasi);
          
          $tarif_destinasi = Tarif::get_total($param["id_order"],$obj_detail->tarif_charge_jarak,$obj_detail->tarif_charge_beban);
          // $list[] = [
          //     "id_order_detail"        => $obj_detail->id_order_detail,
          //     "nama_barang"            => $barang->nama_barang,
          //     "tujuan"                 => $destinasi->alamat_destinasi,
          //     "jarak"                  => $obj_detail->jarak." km",
          //     "charge_jarak"           => $obj_detail->tarif_charge_jarak,
          //     "charge_beban"           => $obj_detail->tarif_charge_beban,
          //     "tarif_total_destinasi"  => $tarif_destinasi,
          //     "selesai"                => ($obj_detail->foto_selesai != "")? true : false,
          //     "foto_selesai"           => $obj_detail->foto_selesai
          // ];
          $check_patokan = Destinasi::get_detail($obj_detail["order_destinasi"]);
          $jenis_patokan;
          if($destinasi["kode_patokan"] != null || $destinasi["kode_patokan"] != "" || $destinasi["kode_patokan"] != " "){
             $jenis_patokan = Pelanggan_patokan::jenis_patokan( Pelanggan_patokan::where("kode_patokan",$destinasi["kode_patokan"])->first()["id_jenis_patokan"]);
             $destinasi["jenis_patokan"] = $jenis_patokan;
          }
          $list[] = [
              "detail"      => $obj_detail,
              //"jenis"       => $jenis_patokan,
              "destinasi"   => $destinasi,
              "barang"      => $barang,
              "selesai"     => ($obj_detail->foto_selesai != "")? true : false,
          ];

          $sum = $sum + $tarif_destinasi;
       }
       
       //$this->result["id_order"] = $order->id_order;
       $this->result["list"] = $list;
       $this->result["total_harga"] = $sum;
      
     }else{
       // throw back error // tidak ada kurir yang setuju atau charge
       throw new \App\Exceptions\MyException([
         "message" => "Terjadi kesalahan, tidak ada kurir yang tersedia"
       ]);
     }

  }

  private function lihat_detail($param){
    $this->result["jenis"] = "Detail destinasi";
    $this->result["kondisi_order"]      = Order_m::where("id_order",$param['id_order'])->first()->status;

    $detail = Order_detail::find($param["id_order_detail"]);
    if($detail!= '' && $detail->id_order == $param["id_order"]){
       if($detail != null){ 
        $barang    = $detail->order_barang;
        $destinasi = Destinasi::get_detail($detail->order_destinasi);
        $this->result["Jenis_order"] = Order_m::find($param["id_order"])->order_jenis->toArray();
        $this->result["data_barang"]    = $barang;
        $this->result["foto_selesai"]   = [
                                              "foto_selesai"     => $detail->foto_selesai,
                                              "selesai_pada_jam" => $detail->modified_date
                                          ];
        $this->result["data_destinasi"] = $destinasi;
        $this->result["jarak"]          = $detail->jarak." km";
        $this->result["charge_jarak"]   = $detail->tarif_charge_jarak;
        $this->result["charge_beban"]   = $detail->tarif_charge_beban;
        $this->result["charge_beban"]   = $detail->tarif_charge_beban;
        $this->result["tarif_total_destinasi"]   = Tarif::get_total($param["id_order"],$detail->tarif_charge_jarak,$detail->tarif_charge_beban);
        $this->result["kondisi"] = "order_ada";

        }else{
            throw new \App\Exceptions\MyException([
             "message" => "Terjadi kesalahan, detail tidak ditemukan"
           ]);
        }
    }else{
       throw new \App\Exceptions\MyException([
             "message" => "Terjadi kesalahan, detail tidak ditemukan "
        ]);
    }
  }

  private function destination_failed($id_order){
     $failed = Order_m::where("id_order",$id_order)->first()->destination_failed;
     if($failed > 0){
       throw new \App\Exceptions\MyException([
         "message" => "Terjadi kesalahan, Terdapat destinasi yang tidak terekam sistem"
       ]);
     }
  }

  public function do_lihat_maps($param){
     // periksa dulu yang destination failed nya 0
     $deskripsi = "";
     $jarak_dihitung = 0;
     $zoom = false;
     $this->destination_failed($param["id_order"]);
     $result = [];
     $kurir = Kurir_Geotracking::where("id_kurir",$param["id_kurir"])->first();
     
     // $result["kurir"] = [
     //    "kordinat_terkini" => $kurir->kordinat_terkini,
     //    "modified_date"  => $kurir->modified_date
     // ];

     $saya = Order_m::where("id_order",$param["id_order"])->first()->kordinat_order;
     $sumber[0] = explode(",", $saya)[0];
     $sumber[1] = str_replace(" ","",explode(",", $saya)[1]);
     $tujuan[0] = explode(",", $kurir->kordinat_terkini)[0];
     $tujuan[1] = explode(",", $kurir->kordinat_terkini)[1];

     $jarak = Distance::haversine_circle_distance($sumber,$tujuan);
     // ada juga di looping bawah
     if($jarak >= 0.5 && $jarak<1){
      $deskripsi = "dengan dengan alamat pemesan";
        $zoom = 13;
     }else if($jarak > 0.1 && $jarak<0.5){
       $deskripsi = "dengan dengan alamat pemesan";
        $zoom = 15;
     }else if($jarak >= 0.0 && $jarak<0.1){
        $zoom = 18;
     }
     // $result["saya"] = [
     //    "kordinat_saya" => $saya->kordinat_order
     // ];

     $detail = Order_m::find($param["id_order"])->order_detail;

     $kordinat_ke = 1;
     $result["tujuan"] = [];
     foreach ($detail as $obj_detail) {
       $destinasi = $obj_detail->order_destinasi;
       if($zoom == false){ 
         // hanya jika ada jarak yang belum ada yang dekat
        $sumber[0] = explode(",", Destinasi::get_detail($destinasi)->kordinat_destinasi)[0];
        $sumber[1] = str_replace(" ","",explode(",", Destinasi::get_detail($destinasi)->kordinat_destinasi)[1]);
        $jarak = Distance::haversine_circle_distance($sumber,$tujuan);
          if($jarak >= 0.5 && $jarak<1){
          $deskripsi = "dengan dengan alamat pemesan";
            $zoom = 13;
         }else if($jarak > 0.1 && $jarak<0.5){
           $deskripsi = "dengan dengan alamat pemesan";
            $zoom = 15;
         }else if($jarak >= 0.0 && $jarak<0.1){
            $zoom = 18;
         }
       }
         
       $result["tujuan"][$kordinat_ke] = Destinasi::get_detail($destinasi)->kordinat_destinasi;
       $kordinat_ke++;
     }

     // hitung dulu jarak untuk setiap marker..
     // jika kurang dari 2 km aktifkan zoom 15
     // jika kurang dari 1 km  aktifkan zoom 20

     
     $jarak_dihitung = $jarak;
     return [
        "kordinat_kurir_terkini" => $kurir->kordinat_terkini,
        "update"    => $kurir->modified_date->format("h:m:s"),
        "zoom" => $zoom,
        "deskripsi" => $deskripsi." , jarak : ".$jarak_dihitung
     ];
     
  }

  public function destination_finish($param){
     
  }

}

?>

