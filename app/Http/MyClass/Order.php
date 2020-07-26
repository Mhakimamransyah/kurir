<?php 

namespace App\Http\MyClass;

use App\Http\MyClass\Routing_kurir;
use App\Http\Helper\Distance;
use App\Model\Order_m;
use App\Model\Kurir_Kordinat;
use App\Model\Kurir;
use App\Model\Pelanggan_patokan;
use App\Model\Order_Kurir;
use App\Model\Order_barang;
use App\Model\Order_destinasi;
use App\Model\Order_detail;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Http\Middleware\MyTransaction;
use Exception;
use DB;

class Order{
	
	// Kelas untuk membuat sebuah order
	private $parameter = [];

	public function __construct()
  {
     
  }

    public function order_baru($param){
        
        /**
            TODO:
            - Method ini untuk insert ke tabel order
         */

        // periksa apakah ada kurir yang tersedia
        if(!Routing_kurir::kurir_tersedia()){
            // kurir tidak ada yang aktif
            $result["status"] = false;
            $result["message"] = "Tidak ada kurir yang aktif";
            return $result;
            exit();
        }
        $id_kurir = Routing_kurir::cari_kurir($param,"baru");
        $new_order = Order_m::create([
          "kordinat_order"     => $param['kordinat_order'],
          "alamat_order"       => $param['alamat_order'],
          "status"             => 'pelanggan_baru',
          "id_pelanggan"       => $param['id_pelanggan'],
          "id_order_jenis"     => $param['id_order_jenis'],
          'destination_failed' => $param['jumlah_destinasi']
        ]);
        $this->parameter['id_order'] = $new_order->id_order;
        //order kurir
        $detail_order_kurir = Order_Kurir::create([
            "id_order" => $this->parameter['id_order'],
            "id_kurir" => $id_kurir,
            "aksi"     => "Baru"
        ]);
        $this->parameter['id_order_kurir'] = $detail_order_kurir->id_order_kurir;
        $this->parameter['id_kurir'] = $id_kurir;
        // NOTIFIKASI FCM KURIR DISINI
        $this->parameter['status'] = true;
        return $this->parameter;      
    }

    public function detail_baru($param,$foto){
        /**
            TODO:
            - Method ini untuk detail baru
         */

        $nama_file = $param['id_order']."-".str_random(20).".".$foto->getClientOriginalExtension();
        try{
            DB::beginTransaction();
            $this->parameter["id_order"] = $param["id_order"];

            // periksa apakah destinasi telah selesai semua
            if(!$this->check_destination_failed()){
                $this->parameter['status'] = false;
                $this->parameter['message'] = "Destinasi telah mencapai batas";
                return $this->parameter;
                exit();    
            }
            // detail destinasi 
            if(array_key_exists("kode_patokan", $param)){
                //menggunakan id_patokan
                $patokan = Pelanggan_patokan::where("kode_patokan",$param["kode_patokan"]);
                if($patokan->count() == 0){
                    $this->parameter['status'] = false;
                    $this->parameter['message'] = "Kode patokan tidak ditemukan";
                    return $this->parameter;
                    exit();    
                }
                $detail_destinasi = Order_destinasi::create([
                   "kode_patokan" => $param['kode_patokan']
                ]);
                $this->parameter["id_order_destinasi"] = $detail_destinasi->id_order_destinasi;
                $this->parameter["kordinat_tujuan"] = [doubleval(explode(",",$patokan->first()->kordinat_patokan)[0]),doubleval(explode(",",$patokan->first()->kordinat_patokan)[1])];

            }else if(array_key_exists("detail_destinasi", $param) && array_key_exists("kordinat_destinasi", $param) && array_key_exists("alamat_destinasi", $param) && array_key_exists("nama_penerima", $param) && array_key_exists("no_hp_penerima", $param) && !array_key_exists("kode_patokan", $param)){
               //tidak menggunakan id patokan
                $detail_destinasi = Order_destinasi::create([
                   "detail_destinasi" => $param['detail_destinasi'],
                   "kordinat_destinasi" => $param['kordinat_destinasi'],
                   "alamat_destinasi" => $param['alamat_destinasi'],
                   "nama_penerima" => $param['nama_penerima'],
                   "no_hp_penerima" => $param['no_hp_penerima']
                ]);
                $this->parameter["id_order_destinasi"] = $detail_destinasi->id_order_destinasi;
                $this->parameter["kordinat_tujuan"] = [doubleval(explode(",", $param['kordinat_destinasi'])[0]),doubleval(explode(",", $param['kordinat_destinasi'])[1])];
            }else{
                $this->parameter['status'] = false;
                $this->parameter['message'] = "Parameter destinasi tidak lengkap";
                return $this->parameter;
                exit();
            }

            // detail barang
            $foto->move('barang',$nama_file);
            $detail_barang = Order_barang::create([
               "nama_barang" => $param['nama_barang'],
               "jumlah_paket" => $param['jumlah_paket'],
               "catatan_kurir" => $param['catatan_kurir'],
               "estimasi_berat" => $param['estimasi_berat'],
               "foto_barang"   => $nama_file
            ]);
            $this->parameter['id_order_barang'] = $detail_barang->id_order_barang;

            // order detail
            $this->parameter['kordinat_asal'] = $this->kordinat_asal();
            $this->hitung_jarak($this->parameter['kordinat_asal'],$this->parameter['kordinat_tujuan']);
            

            $detail_order = Order_detail::create([
              "jarak" => $this->parameter['jarak(Km)'],
              "tarif_charge_jarak" => $this->parameter['charge_jarak(Rp)'],
              "id_order_destinasi" => $this->parameter['id_order_destinasi'],
              "id_order_barang" => $this->parameter['id_order_barang'],
              "id_order" => $this->parameter['id_order']
            ]);

            
            // turunkan nilai destination fails
            Order_m::where("id_order",$this->parameter['id_order'])->decrement('destination_failed');
           
            $this->parameter['status'] = true;
            $this->parameter['message'] = "Detail ditambahkan";
            DB::commit();
            return $this->parameter;
        }catch(Exception $e){
            DB::rollBack();
            // hapus gambar...
            if(file_exists("barang/".$nama_file)){ unlink("barang/".$nama_file); }
            $this->parameter['status'] = false;
            $this->parameter['message'] = $e->getMessage()." line ".$e->getLine()." ".$e->getFile();
            return $this->parameter;
        }
    }

   
    private function kordinat_asal(){
       $detail = Order_detail::where("id_order",$this->parameter['id_order']);
       if($detail->count() > 0){
         // destinasi kedua atau lebih
         $id_destinasi_sebelumnya = $detail->latest()->first()->id_order_destinasi;
         $destinasi  =  Order_destinasi::where("id_order_destinasi",$id_destinasi_sebelumnya)->first();
         if($destinasi->kode_patokan == null || $destinasi->kode_patokan == ''){
            $kordinat = $destinasi->kordinat_destinasi;
            return [doubleval(explode(",", $kordinat)[0]),doubleval(explode(",", $kordinat)[1])];  
         }else{
            $patokan = Pelanggan_patokan::where("kode_patokan",$destinasi->kode_patokan)->first();
            $kordinat = $patokan->kordinat_patokan;
            return [doubleval(explode(",", $kordinat)[0]),doubleval(explode(",", $kordinat)[1])];  
         }

       }else{
        // destinasi pertama
        // ambil dari tabel order
          $order = Order_m::where("id_order",$this->parameter["id_order"])->first();
          return [doubleval(explode(",", $order->kordinat_order)[0]),doubleval(explode(",", $order->kordinat_order)[1])];
       }
    }

    private function check_destination_failed(){
        $failed = Order_m::where("id_order",$this->parameter['id_order'])->first()->destination_failed;
        if($failed <= 0 ){
          return false;
        }else{
            return true;
        }
    }


    private function hitung_jarak($kordinat_asal,$kordinat_tujuan,$selanjutnya=false){

        $asal   = $kordinat_asal;
        $tujuan = $kordinat_tujuan;

        $jarak = Distance::haversine_circle_distance($asal, $tujuan);
        
        if($selanjutnya){
           $this->parameter['jarak_ke_destinasi_selanjutnya(Km)'] = $jarak;
           $this->parameter['charge_jarak_destinasi_selanjutnya(Rp)'] = $this->charge_tarif_jarak($jarak);
           return [$jarak, $this->parameter['charge_jarak_destinasi_selanjutnya(Rp)']];
        }else{
           $this->parameter['jarak(Km)'] = $jarak;
           $this->parameter['charge_jarak(Rp)'] = $this->charge_tarif_jarak($jarak);
           return [$jarak, $this->parameter['charge_jarak(Rp)']];
        }
    }

    private function charge_tarif_jarak($jarak){
        // di atas 20 km 
        // setiap kelipatan 5 km tambahkan 5000
        if($jarak > 20){
            $res          = $jarak - 20;
            $kelebihan    = ceil($res/5);
            $charge_jarak = $kelebihan * 5000;
            return $charge_jarak;            
        }else{
            return 0;
        }
    }

    public function preview_order($param){
        
        $kordinat_asal   = [doubleval(explode(",", $param['kordinat_asal'])[0]),doubleval(explode(",", $param['kordinat_asal'])[1])];
        $kordinat_tujuan = [doubleval(explode(",", $param['kordinat_tujuan'])[0]),doubleval(explode(",", $param['kordinat_tujuan'])[1])];
        $tarif_jenis           = intval($param['tarif_jenis']); // 5000 reguler 13 000 express
        $total_tarif           = intval($param['total_tarif']);

        
        $result = $this->hitung_jarak($kordinat_asal, $kordinat_tujuan);
        return [
           "Jarak" => $result[0],
           "Charge" => $result[1],
           "Total_tarif_destinasi_ini"  => $tarif_jenis + $result[1],
           "Total_tarif_sekarang" => ($tarif_jenis + $result[1]) + $total_tarif
        ];
    }

    public function periksa_sesi_order($param){
        $order = Order_Kurir::where("id_order",$param["id_order"])->latest()->first();
        if($order->aksi == "Baru"){
          // orderan masih baru
          return [
             "status_order" => 0, // baru
             "kondisi_order" => true, // sesi order lanjut
             "message_order" => "Masih menunggu konfirmasi kurir"
          ];

        }else if($order->aksi == "Setuju"){
          // kurir setuju
          Order_m::where("id_order",$param["id_order"])->update(["status"=>"kurir_setuju"]);
          return [
             "status_order" => 1, // kurir setuju
             "kondisi_sesi_order" => true, // sesi order lanjut
             "message_order" => "Kurir ditemukan"
          ];

        }else if($order->aksi == "Tolak"){
          // kurir menolak

          // routing kurir lain
          $routing = new Routing_kurir();
          $id_kurir = $routing->cari_kurir($param,"penolakan");
          if($id_kurir != -1){
            // ada kurir lain
            Order_Kurir::create([
               "id_order" => $param["id_order"],
               "id_kurir" => $id_kurir,
               "aksi"     => "Baru"
            ]);
            return [
               "status_order" => 3, // order di tolak dan masih ada kurir yang lain masih menunggu 
               "kondisi_sesi_order" => true, // sesi order lanjut
               "message_order" => "Masih menunggu konfirmasi kurir"
            ]; 

          }else{
            // tidak ada kurir lain
             Order_m::where("id_order",$param["id_order"])->update(["status"=>"kurir_tidak_tersedia"]);
             return [
               "status_order" => 2, // order di tolak dan tidak ada kurir lagi
               "kondisi_sesi_order" => false, // sesi order batal
               "message_order" => "Kurir tidak ditemukan"
             ];            
          }

        }else{
          // kurir setuju tapi di charge
           Order_m::where("id_order",$param["id_order"])->update(["status"=>"pelanggan_menunggu_konfirmasi_charge"]);
           return [
               "status_order" => 4, // order diterima tapi dicharge oleh kurir
               "kondisi_sesi_order" => true, // sesi order batal
               "message_order" => "Kurir ditemukan dan melakukan charge barang"
            ];            
        }
    }

    public function deal_charge($param){
      // pelanggan setuju dengan charge dari kurir
      // in transaction
       Order_m::where("id_order",$param["id_order"])->update(["status"=>"pelanggan_setuju"]);

      //FCM di SINI
    }

    public function cancel_order($param){
       Order_m::where("id_order",$param["id_order"])->update(["status" => "pelanggan_batal"]);
       
       // FCM di SINI
    }

    public function kurir_selesaikan_destinasi($param,$foto){
      // PERIKSA APAKAH INI DESTINASI TERAKHIR YANG DI SELESAIKAN
       $detail = Order_detail::where("id_order",$param["id_order"]);
       $jumlah_order = $detail->count();
       $detail_selesai = $detail->where("foto_selesai","!=",null);

       $nama_file = $param['id_order']."-".str_random(20).".".$foto->getClientOriginalExtension();
       $foto->move('selesai',$nama_file);

       

       //var_dump($jumlah_order." ".$detail_selesai->count());

       if($detail_selesai->count() == ( $jumlah_order - 1)){
        // ORDER SELESAI

        Order_detail::where("id_order_detail",$param["id_order_detail"])->update([
           "foto_selesai" => $nama_file
        ]);
         
         Order_m::where("id_order",$param["id_order"])->update([
            "status" => "selesai"
         ]);

         return "order selesai";

         // BERITAHU PELANGGAN ORDER TELAH SELESAI FCM

       }else{
        // MASIH ADA ORDER LAIN
        Order_detail::where("id_order_detail",$param["id_order_detail"])->update([
           "foto_selesai" => $nama_file
        ]);
          
         return "destinasi di selesaikan";

        // BERITAHU PELANGGAN ADA DESTINASI YANG TELAH SELESAI FCM
       }       
    }
}

?>

