<?php 

namespace App\Http\Helper;
use App\Model\Order_m;

class Tarif{
    
    //Helper untuk mengambil data destinasi apakah menggunakan kode patokan atau tidak
    public function __construct()
    {
        //
    }

    
    public static function get_total($id_order,$tarif_charge_jarak,$tarif_charge_beban){
        $tarif = Order_m::find($id_order)->order_jenis->tarif;
        return $tarif+$tarif_charge_beban+$tarif_charge_jarak;
    }

}

?>

