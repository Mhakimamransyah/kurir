<?php 

namespace App\Http\Helper;
use App\Model\Pelanggan_patokan;

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

}

?>

