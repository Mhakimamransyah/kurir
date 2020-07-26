<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\MyClass\Order;
use App\Http\MyClass\Response_kurir;
use App\Http\MyClass\Lihat_order;
use Dingo\Api\Exception\ValidationHttpException;
use App\Http\Helper\ResponseBuilder;
use App\Model\Kurir;
use App\Model\Order_jenis;
use App\Model\Order_m;
use App\Model\Pelanggan_patokan;

class OrderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // middleware disini
      $this->middleware('auth');
      $this->middleware('trans',['only' => ['pelanggan_deal_charge','order_baru','pelanggan_cancel_charge','kurir_deal_order','kurir_selesaikan_destinasi','pelanggan_rating_kurir']]);      
    }

    public function order_baru(Request $request){
            // order
            $this->validate($request, [
                'id_pelanggan' => 'required|regex:/[0-9]/',
                'id_order_jenis' => 'required|regex:/[0-9]/',
                'alamat_order' => 'required',
                'kordinat_order' => "required",
                'jumlah_destinasi' => 'required'
            ]);
           $order = new Order();
           $result = $order->order_baru($request->toArray());
           return ResponseBuilder::result(true,"Order dibuat",$result,200,true);
    }

    public function detail_baru(Request $request){
        
        $this->validate($request, [
                'id_order'    => 'required|regex:/[0-9]/',
                'foto_barang' => 'required|image|max:10000',
                'nama_barang' => 'required',
                'jumlah_paket' => 'required',
                'catatan_kurir' => "required",
                'estimasi_berat' => "required"
        ]);
         $order = new Order();
         $result = $order->detail_baru($request->toArray(),$request->file('foto_barang'));
         if($result['status']){
          return ResponseBuilder::result(true,"Order dibuat",$result,200);
         }else{
           return ResponseBuilder::result(false,$result['message'],[],400);
         }
    }

    public function preview_order_tarif(Request $request){

        $this->validate($request, [
            "kordinat_asal"   => 'required',
            "kordinat_tujuan" => 'required',
            "tarif_jenis" => 'required|regex:/[0-9]/',
            "total_tarif" => 'required|regex:/[0-9]/'
        ]);
        $order = new Order();
        $result = $order->preview_order($request->toArray());
        return ResponseBuilder::result(true,"Sukses",$result,200); 
    }

    public function get_ketentuan_tarif(Request $request){
       if($request->filled(["id_order_jenis"])){
          // ambil ketentuan khusus
          return ResponseBuilder::result(true,"Sukses",Order_jenis::select('id_order_jenis','tarif','jenis')->where("id_order_jenis",$request->id_order_jenis)->get(),200); 
       }else{
          // ambil seluruh daftar
          return ResponseBuilder::result(true,"Sukses",Order_jenis::select('id_order_jenis','tarif','jenis')->get(),200); 
       }
    }

    public function get_destinasi_by_kode_patokan(Request $request){

       $this->validate($request,[
          "kode_patokan" => "required"
       ]);
       
       $data = Pelanggan_patokan::Join('jenis_patokan','jenis_patokan.id_jenis_patokan','=','pelanggan_patokkan.id_jenis_patokan')->where('pelanggan_patokkan.kode_patokan',$request->kode_patokan);

       if($data->count() > 0 ){
         return ResponseBuilder::result(true,"Sukses, Kode Patokoan ditemukan",$data->first(),200);
       }else{
          return ResponseBuilder::result(false,"Kode patokan tidak ditemukan",[],200);
       }
    }

    public function get_status_order(Request $request){
       
       $this->validate($request,[
          "id_order" => "required|regex:/[0-9]/",
          "kordinat_order" => 'required'
       ]);

       $order = new Order($request->toArray());
       return ResponseBuilder::result(true,"Sukses",$order->periksa_sesi_order($request->toArray()),200);

    }

    public function kurir_charge_order(Request $request){
        $this->validate($request,[
          "id_order" => "required|regex:/[0-9]/",
          "id_barang" => "required|regex:/[0-9]/",
          "id_kurir"  => "required|regex:/[0-9]/",
          "tarif_charge" => 'required',
          "kordinat_kurir" => 'required'
       ]); 
        $order = new Response_kurir();
        $result = $order->kurir_charge($request->toArray());
        if($result['status']){
           return ResponseBuilder::result(true,"Sukses, order di charge",[],200);    
         }else{
           return ResponseBuilder::result(false,$result["message"],[],400);
         }
    }

    public function kurir_deal_order(Request $request){
        $this->validate($request,[
          "id_order" => "required|regex:/[0-9]/",
          "id_kurir"  => "required|regex:/[0-9]/",
          "kordinat_kurir" => 'required'
       ]); 
        $order = new Response_kurir();
        $order->kurir_setuju($request->toArray());
        return ResponseBuilder::result(true,"Sukses, order di setujui",[],200,true);
    }

    public function pelanggan_deal_charge(Request $request){
       
       $this->validate($request,[
          "id_order" => "required|regex:/[0-9]/"
       ]); 
       $order = new Order();
       $order->deal_charge($request->toArray());
       return ResponseBuilder::result(true,"Sukses, charge di setujui",[],200,true);
    }

    public function pelanggan_cancel_order(Request $request){
        $this->validate($request,[
          "id_order" => "required|regex:/[0-9]/"
       ]); 
       $order = new Order();
       $order->cancel_order($request->toArray());
       return ResponseBuilder::result(true,"Sukses, order dibatalkan",[],200,true);
    }

    public function kurir_cancel_order(Request $request){
       $this->validate($request,[
          "id_order" => "required|regex:/[0-9]/"
       ]); 
       $order = new Response_kurir();
       $order->cancel_order($request->toArray());
       return ResponseBuilder::result(true,"Sukses, order dibatalkan",[],200,true);
    }

    public function lihat_order(Request $request){
       $this->validate($request,[
          "id_order" => "required|regex:/[0-9]/"
       ]);

       $order  = new Lihat_order();
       $result = $order->do_lihat_order($request->toArray());
       return ResponseBuilder::result(true,"Sukses",$result,200,true);  
    }

    public function lihat_maps(Request $request){
       
       $this->validate($request,[
          "id_order" => "required|regex:/[0-9]/",
          "id_kurir" => "required|regex:/[0-9]/",
       ]);

       $order  = new Lihat_order();
       $result = $order->do_lihat_maps($request->toArray());
       return ResponseBuilder::result(true,"Sukses",$result,200,true);
    }

    public function kurir_selesaikan_destinasi(Request $request){
      // gunakan commit

       $this->validate($request,[
          "id_order" => "required|regex:/[0-9]/",
          "id_order_detail" => "required|regex:/[0-9]/",
          "id_kurir" => "required|regex:/[0-9]/",
          "foto"     => "required|image|max:10000"
       ]);      

       $order = new Order();
       $result = $order->kurir_selesaikan_destinasi($request->toArray(),$request->file("foto"));
       return ResponseBuilder::result(true,"Sukses",$result,200,true);
    }

    public function pelanggan_rating_kurir(Request $request){
       // gunakan commit
       $this->validate($request,[
          "id_order" => "required|regex:/[0-9]/",
          "rating" => "required|regex:/[0-9]/",
       ]);
       Order_m::where("id_order",$request->id_order)->update([
          "rating" => $request->rating
       ]);  
       return ResponseBuilder::result(true,"Sukses rating diberikan",[],200,true);
    }

    







    // public function detail_edit(Request $request){
    //     $validator = \Validator::make($request->all(), [
    //             'id_order'    => 'required|regex:/[0-9]/',
    //             'id_order_barang' => 'required|regex:/[0-9]/',
    //             'foto_barang' => 'required|image|max:10000',
    //             'nama_barang' => 'required',
    //             'jumlah_paket' => 'required',
    //             'catatan_kurir' => "required",
    //             'estimasi_berat' => "required",
    //             'id_order_destinasi' => 'required|regex:/[0-9]/',
    //             'id_order_detail' => 'required|regex:/[0-9]/'
    //     ]);
    //     if($validator->fails()){
    //        return ResponseBuilder::result(false,$validator->errors(),[],400);
    //     }else{
    //        $order = new Order();
    //        $result = $order->detail_edit($request->toArray(),$request->file('foto_barang'));
    //        if($result['status']){
    //         return ResponseBuilder::result(true,"Order diubah",$result,200);
    //        }else{
    //          return ResponseBuilder::result(false,$result['message'],[],400);
    //        }
    //     }
    // }

}
