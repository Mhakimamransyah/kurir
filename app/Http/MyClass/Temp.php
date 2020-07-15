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
use Symfony\Component\HttpFoundation\File\UploadedFile;
//use Exception;
use DB;

class Temp{
	
	// Kelas untuk membuat sebuah order
	private $parameter = [];

	public function __construct()
    {
        
    }

  
    public function detail_edit($param, $foto){
        
        try{
           // detail destinasi 
            DB::beginTransaction();
            if(array_key_exists("kode_patokan", $param)){
                //menggunakan id_patokan
                $patokan = Pelanggan_patokan::where("kode_patokan",$param["kode_patokan"]);
                if($patokan->count() == 0){
                    $this->parameter['status'] = false;
                    $this->parameter['message'] = "Kode patokan tidak ditemukan";
                    return $this->parameter;
                    exit();    
                }
                
                Order_destinasi::where("id_order_destinasi",$param['id_order_destinasi'])->update([
                   "kode_patokan" => $param['kode_patokan'],
                   "detail_destinasi" => null,
                   "kordinat_destinasi" => null,
                   "alamat_destinasi" => null,
                   "nama_penerima" => null,
                   "no_hp_penerima" => null
                ]);
                $this->parameter["kordinat_tujuan"] = [doubleval(explode(",",$patokan->first()->kordinat_patokan)[0]),doubleval(explode(",",$patokan->first()->kordinat_patokan)[1])];

            }else if(array_key_exists("detail_destinasi", $param) && array_key_exists("kordinat_destinasi", $param) && array_key_exists("alamat_destinasi", $param) && array_key_exists("nama_penerima", $param) && array_key_exists("no_hp_penerima", $param) && !array_key_exists("kode_patokan", $param)){
               //tidak menggunakan id patokan
                Order_destinasi::where("id_order_destinasi",$param['id_order_destinasi'])->update([
                   "detail_destinasi" => $param['detail_destinasi'],
                   "kordinat_destinasi" => $param['kordinat_destinasi'],
                   "alamat_destinasi" => $param['alamat_destinasi'],
                   "nama_penerima" => $param['nama_penerima'],
                   "no_hp_penerima" => $param['no_hp_penerima'],
                   "kode_patokan" => null
                ]);
                $this->parameter["kordinat_tujuan"] = [doubleval(explode(",", $param['kordinat_destinasi'])[0]),doubleval(explode(",", $param['kordinat_destinasi'])[1])];
            }else{
                $this->parameter['status'] = false;
                $this->parameter['message'] = "Parameter destinasi tidak lengkap";
                return $this->parameter;
                exit();
            }

            // detail barang
            $barang = Order_barang::where("id_order_barang",$param['id_order_barang']);
            if(file_exists("barang/".$barang->first()->foto_barang)){ unlink("barang/".$barang->first()->foto_barang); }
            $nama_file = $param['id_order']."-".str_random(20).".".$foto->getClientOriginalExtension();
            $foto->move('barang',$nama_file);
            $barang->update([
                   "nama_barang" => $param['nama_barang'],
                   "jumlah_paket" => $param['jumlah_paket'],
                   "catatan_kurir" => $param['catatan_kurir'],
                   "estimasi_berat" => $param['estimasi_berat'],
                   "foto_barang"   => $nama_file
            ]);


            // order detail
            $this->parameter['kordinat_asal'] = $this->kordinat_asal_edit($param);
            $this->hitung_jarak($this->parameter['kordinat_asal'],$this->parameter['kordinat_tujuan']);
            $detail_order = Order_detail::where("id_order_detail",$param["id_order_detail"])->update([
                  "jarak" => $this->parameter['jarak(Km)'],
                  "tarif_charge_jarak" => $this->parameter['charge_jarak(Rp)']
            ]);
            $this->update_kordinat_selanjutnya($param);

            $this->parameter['status'] = true;
            $this->parameter['message'] = "Detail diubah";
            DB::commit();
            return $this->parameter;

        }catch(Exception $e){
            DB::rollBack();
            $this->parameter['status'] = false;
            $this->parameter['message'] = $e->getMessage()." line ".$e->getLine()." ".$e->getFile();
            return $this->parameter;
        }
        

    }

    private function update_kordinat_selanjutnya($param){
        // akibat perubahan alamat maka atur ulang kordinat, jarak, dan charge jarak
       $detail = Order_detail::where("id_order",$param['id_order'])->where("id_order_destinasi",">",$param['id_order_destinasi'])->oldest();
       if($detail->count() > 0){
        // masih ada selanjutnya
          $id_order_destinasi_selanjutnya = $detail->first()->id_order_destinasi;
          $id_order_detail_selanjutnya    = $detail->first()->id_order_detail;
          $destinasi = Order_destinasi::where("id_order_destinasi",$id_order_destinasi_selanjutnya)->first();
          $kordinat_tujuan = 0;
          if($destinasi->kode_patokan == null){
               // menggunakan input manual
               $kordinat = $destinasi->kordinat_destinasi;
               $kordinat_tujuan = [doubleval(explode(",", $kordinat)[0]),doubleval(explode(",", $kordinat)[1])];  
          }else{
               // menggunakan kode patokan
                $patokan = Pelanggan_patokan::where("kode_patokan",$destinasi->kode_patokan)->first();
                $kordinat = $patokan->kordinat_patokan;
                $kordinat_tujuan =  [doubleval(explode(",", $kordinat)[0]),doubleval(explode(",", $kordinat)[1])]; 
          }
          $kordinat_asal = $this->parameter['kordinat_tujuan'];
          $this->hitung_jarak($kordinat_asal,$kordinat_tujuan,true);

          //update order detail selanjutnya
          Order_detail::where("id_order_detail",$id_order_detail_selanjutnya)->update([
             "jarak" => $this->parameter["jarak_ke_destinasi_selanjutnya(Km)"],
             "tarif_charge_jarak" => $this->parameter["charge_jarak_destinasi_selanjutnya(Rp)"]
          ]);

       }else{
        // paling akhir
        // DO NOTHING
       }
    }

    private function kordinat_asal_edit($param){
        // hanya untuk edit pesanan

        $detail = Order_detail::where("id_order",$param['id_order'])->where("id_order_destinasi","<",$param['id_order_destinasi'])->latest();
        $id_order_destinasi_sebelumnya = 0;
        if($detail->count() > 0){
           // edit di pertengahan
            $id_order_destinasi_sebelumnya = $detail->first()->id_order_destinasi;
            $destinasi = Order_destinasi::where("id_order_destinasi",$id_order_destinasi_sebelumnya)->first();
            if($destinasi->kode_patokan == null){
               // menggunakan input manual
               $kordinat = $destinasi->kordinat_destinasi;
               return [doubleval(explode(",", $kordinat)[0]),doubleval(explode(",", $kordinat)[1])];  
            }else{
               // menggunakan kode patokan
                $patokan = Pelanggan_patokan::where("kode_patokan",$destinasi->kode_patokan)->first();
                $kordinat = $patokan->kordinat_patokan;
                return [doubleval(explode(",", $kordinat)[0]),doubleval(explode(",", $kordinat)[1])]; 
            }

        }else{
          // edit di awal
            $kordinat = Order_m::where("id_order",$param['id_order'])->first()->kordinat_order;
            return [doubleval(explode(",", $kordinat)[0]),doubleval(explode(",", $kordinat)[1])];
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


  

}

?>

