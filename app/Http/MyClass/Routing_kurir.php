<?php 

namespace App\Http\MyClass;
use App\Http\Helper\Distance;
use App\Model\Kurir_Kordinat;
use App\Model\Kurir;
use App\Model\Order_Kurir;

class Routing_kurir{
	


	public function __construct()
    {

    }

    public static function kurir_tersedia(){
        // periksa apakah ada kurir yang tersedia
        $result = true;
        $kurir = Kurir::where("mode","aktif")->where("sesi_login_aktif","ya");
        if($kurir->count() <= 0){
         $result = false;
     }
     return $result;
 }


 public static function cari_kurir($param,$jenis="baru"){

       // cari kurir yang paling dekat dari titik kordinat dan
       // tidak pernah nolak id order ini sebelumnya dan
       // sedang aktif
       // outputnya id kurir
    $kurirs = [];
    if($jenis == "baru"){
            // untuk pesanan baru
        $kurirs = Kurir::where("mode","aktif")->where("sesi_login_aktif","ya")->get();
    }else{
            // untuk mencari kurir lagi setelah kurir yang dipilih sebelumnya menolak
        $kurir_penolak = Order_Kurir::where("id_order",$param['id_order'])->where("aksi","Tolak")->pluck('id_kurir')->toArray();
        $kurirs = Kurir::where("mode","aktif")->where("sesi_login_aktif","ya")->whereNotIn('id_kurir',$kurir_penolak)->get();
    }

        $id_kurir_terdekat = -1; // tidak ada kurir
        $jarak_terdekat = 999999;
        $alamat = '';
        foreach ($kurirs as $key => $kurir) {
            foreach ($kurir->kordinat as $key_kordinat => $kordinat_value) {
                $latitude_kurir = doubleval(explode(',', $kordinat_value->kordinat)[0]);
                $longitude_kurir = doubleval(explode(',', $kordinat_value->kordinat)[1]);
                $jarak = Distance::haversine_circle_distance([$latitude_kurir,$longitude_kurir],[doubleval(explode(",", $param['kordinat_order'])[0]),doubleval(explode(",", $param['kordinat_order'])[1])]);
                if($jarak < $jarak_terdekat){
                    $jarak_terdekat = $jarak;
                    $id_kurir_terdekat = $kurir->id_kurir;
                    $alamat = $kordinat_value->alamat;
                }
            }
        }

        return $id_kurir_terdekat;     
    }

}

?>

