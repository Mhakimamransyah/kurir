<?php 

namespace App\Http\Helper;
use App\Model\Pelanggan_patokan;
use App\Model\Order_m;
use App\Model\Order_Kurir;
use App\Model\Order_jenis;
use App\Model\Order_barang;
use App\Model\Order_destinasi;
use App\Model\Order_detail;

class Destinasi{

    //Helper untuk mengambil data destinasi apakah menggunakan kode patokan atau tidak
  public function __construct()
  {
        //
  }


  public static function get_detail($param){
        // input berupa array

    $result;
    if($param["kode_patokan"] == null){
            // tidak menggunakan kode patokan
      $result = $param;
    }else{
            // menggunakan kode patokan
      $kode_patokan = $param["kode_patokan"];
      $result       = Pelanggan_patokan::select("alamat_patokan as alamat_destinasi","kordinat_patokan as kordinat_destinasi","foto_patokan","detail_patokan as detail_destinasi","id_jenis_patokan","nama_penerima_patokan as nama_penerima","no_hp_penerima_patokan as no_hp_penerima","kode_patokan")->where("kode_patokan", $kode_patokan)->first();
    }
    return $result;
        // output berupa objek
  }

  public static function getCompleteOrderDataByIdOrder($id_order,$data_kurir=false){
   $result = [];
   $order = Order_m::find($id_order);
   if($order != null){

    $order_kurir = $order->order_kurir->sortByDesc("id_order_kurir")->first();
    $jenis = $order->order_jenis;
    $pelanggan = $order->pelanggan;
    $detail = $order->order_detail;
    $user_pelanggan = $pelanggan->user;
    
    $result["order_kurir"]["aksi"] = $order_kurir->aksi;
    $result["order_kurir"]["expired"] = $order_kurir->expired_count;
    $result["order_kurir"]["date_modified"] = $order_kurir->modified_date->format('d-m-Y h:m:s');
    $result["pelanggan"]["nama_pelanggan"] = $pelanggan->nama_pelanggan;
    $result["pelanggan"]["no_handphone"] = $pelanggan->nomor_hp_pelanggan;
    $result["pelanggan"]["id_pelanggan"] = $pelanggan->id_pelanggan;
    $result["pelanggan"]["id_user"] = $user_pelanggan->id_user;
    $result["order"]["alamat_order"] = $order->alamat_order;
    $result["order"]["kordinat_order"] = $order->kordinat_order;
    $result["order"]["status"] = $order->status;
    $result["order"]["rating"] = $order->rating;
    $result["order"]["date"] = $order->created_date->format('d-m-Y h:m:s');
    $result["jenis"]["jenis_order"] = $jenis->jenis;
    $result["jenis"]["tarif_order"] = $jenis->tarif;
    $result["jenis"]["deskripsi"] = $jenis->deskripsi_jenis_order;
    if($data_kurir == true){
       // ambi data kurir coming soon in user apps

    }
    $index = 0;
    foreach ($detail as $value) {
      # code...
      $kode_patokan = false;
      $barang = $value->order_barang;
      $destinasi = $value->order_destinasi;
      $result["detail"][$index]['detail']["id_detail"] = $value->id_order_detail;
      $result["detail"][$index]['detail']["jarak"] = $value->jarak." KM";
      $result["detail"][$index]['detail']["tarif_charge_jarak"] = $value->tarif_charge_jarak;
      $result["detail"][$index]['detail']["tarif_charge_beban"] = $value->tarif_charge_beban;
      $result["detail"][$index]['detail']["foto_selesai"] = $value->foto_selesai;
      $result["detail"][$index]['detail']["date"] = $value->modified_date->format('d-m-Y h:m:s');

      $result["detail"][$index]['barang']["id_barang"] = $barang->id_order_barang;
      $result["detail"][$index]['barang']["nama_barang"] = $barang->nama_barang;
      $result["detail"][$index]['barang']["jumlah_paket"] = $barang->jumlah_paket;
      $result["detail"][$index]['barang']["foto_barang"] = $barang->foto_barang;
      $result["detail"][$index]['barang']["catatan_kurir"] = $barang->catatan_kurir;
      $result["detail"][$index]['barang']["estimasi_berat"] = $barang->estimasi_berat;


      $result["detail"][$index]['destinasi']["id_destinasi"] = $destinasi->id_order_destinasi;
      if($destinasi->kode_patokan != null){
       $kode_patokan = true;
       $destinasi = Destinasi::get_detail(["kode_patokan" => $destinasi->kode_patokan]);
     }else{
       $kode_patokan = false;
     }

     $result["detail"][$index]['destinasi']["pakai_kode_patokan"] = $kode_patokan;
     $result["detail"][$index]['destinasi']["foto_patokan"] = ($kode_patokan)? $destinasi->foto_patokan: null;
     $result["detail"][$index]['destinasi']["alamat_destinasi"] = $destinasi->alamat_destinasi;
     $result["detail"][$index]['destinasi']["kordinat_destinasi"] = $destinasi->kordinat_destinasi;
     $result["detail"][$index]['destinasi']["detail_destinasi"] = $destinasi->detail_destinasi;
     $result["detail"][$index]['destinasi']["nama_penerima"] = $destinasi->nama_penerima;
     $result["detail"][$index]['destinasi']["no_hp_penerima"] = $destinasi->no_hp_penerima;
     $result["detail"][$index]['destinasi']["kode_patokan"] = $destinasi->kode_patokan;
     $index++;
   }
 }
 return $result;

}

}

?>

