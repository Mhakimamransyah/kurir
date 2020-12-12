<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\MyClass\Response_kurir;
use App\Http\MyClass\Sistem;
use App\Model\User;
use App\Model\Order_m;
use App\Model\Kurir_Kordinat;
use App\Http\Helper\ResponseBuilder;
use App\Http\Helper\Distance;
use App\Model\Kurir_Geotracking;
use App\Model\Kurir;

class KurirController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    	$this->middleware('auth');
    	$this->middleware('trans',['only' => ['update_kordinat_terkini',"mode_kurir"]]);    
    }

    public function add_cordinate(Request $request){
    	if($request->filled(['alamat','kordinat','id_kurir'])){
    		Kurir_Kordinat::create([
    			"alamat"   => $request->alamat,
    			"kordinat" => $request->kordinat,
    			"id_kurir"  => $request->id_kurir
    		]);
    		return ResponseBuilder::result(true,"Berhasil, Kordinat di tambahkan",[],200);
    	}else{
    		return ResponseBuilder::result(false,"Parameter tidak lengkap",[],400);
    	}
    }

    public function edit_cordinate(Request $request){
    	if($request->filled(['id_kurir_kordinat','alamat','kordinat'])){
    		Kurir_Kordinat::where("id_kurir_kordinat",$request->id_kurir_kordinat)->update([
    			"alamat"   => $request->alamat,
    			"kordinat" => $request->kordinat
    		]);
    		return ResponseBuilder::result(true,"Berhasil, Kordinat di-ubah",[],200);
    	}else{
    		return ResponseBuilder::result(false,"Parameter tidak lengkap",[],400);
    	}	
    }

    public function delete_cordinate(Request $request){
    	if($request->filled(['id_kurir_kordinat'])){
    		Kurir_Kordinat::destroy($request->id_kurir_kordinat);
    		return ResponseBuilder::result(true,"Berhasil, Kordinat di-hapus",[],200);
    	}else{
    		return ResponseBuilder::result(false,"Parameter tidak lengkap",[],400);
    	}
    }

    public function get_cordinate(Request $request){
    	if($request->filled(["id_kurir"])){
    		$result = Kurir_Kordinat::where("id_kurir",$request->id_kurir);
    		if($request->filled(["id_kurir_kordinat"])){
    			$result = $result->where("id_kurir_kordinat",$request->id_kurir_kordinat);
    		}
    		return ResponseBuilder::result(true,"sukses",$result->get(),200); 
    	}else{
    		return ResponseBuilder::result(false,"Id kurir tidak boleh kosong",[],400);	
    	}
    }


    public function update_kordinat_terkini(Request $request){
       // untuk update real time maps
    	$this->validate($request, [
    		'id_kurir' => 'required|regex:/[0-9]/',
    		'kordinat_kurir' => "required"
    	]);
    	$locked = "";
    	$list = new Response_kurir();
    	$result = $list->transaksi_aktif($request->toArray());
  // periksa seluruh order yang aktif,
  // Jika dari seluruh order yang aktif ada kordinat yang sudah di bawah 0.3 Km
  // maka locked order tersebut sehingga tidak bisa di cancel lagi oleh pelanggan
  // tapi hanya jika order tersebut status nya tidak pelanggan batal
    	foreach ($result as $key => $value) {
    		$order = Order_m::where("id_order",$value["id_order"])->where("locked","tidak")->where("status","!=","pelanggan_menunggu_konfirmasi_charge")->first();
    		if($order != null){
    			$asal = [explode(",", $request->kordinat_kurir)[0],explode(",", $request->kordinat_kurir)[1]];
    			$tujuan = [explode(",", $order->kordinat_order)[0],explode(",", $order->kordinat_order)[1]];
    			$jarak = Distance::haversine_circle_distance($asal,$tujuan);
    			if($jarak < 0.8 && $order->status != "pelanggan_batal"){
    				$locked = "ada order yang di locked";
    				Order_m::where("id_order",$value["id_order"])->update([
    					"locked" => "ya"
    				]);
    			}
    		}
    	}
    	Kurir_Geotracking::where("id_kurir",$request->id_kurir)->update([
    		"kordinat_terkini" => $request->kordinat_kurir
    	]);
    	return ResponseBuilder::result(true,"Sukses, Kordinat diperbarui, ".$locked,[],200,true);
    }

    public function get_kordinat_terkini(Request $request){
       // untuk get update real time maps
    	$this->validate($request, [
    		'id_kurir' => 'required|regex:/[0-9]/'
    	]);
    	return ResponseBuilder::result(true,"Sukses, Kordinat kurir didapatkan",Kurir_Geotracking::select("kordinat_terkini","modified_date")->where("id_kurir",$request->id_kurir)->first(),200,true);
    }

    public function mode_kurir(Request $request){
    	$this->validate($request, [
    		'id_kurir' => 'required|regex:/[0-9]/',
    		"mode"     => 'required'
    	]);
    	Kurir::where("id_kurir",$request->id_kurir)->update([
    		"mode" => $request->mode
    	]);

    	return ResponseBuilder::result(true,"Sukses, mode di ".$request->mode,[],200,true);
    }

    public function get_order_baru_saya(Request $request){
    	$this->validate($request, [
    		'id_kurir' => 'required|regex:/[0-9]/',
    	]);
    	$list = new Response_kurir();
    	$result = $list->list_order_baru_kurir($request->toArray());
    	return ResponseBuilder::result(true,"List order baru",$result,200,true);
    }

    public function get_is_transaksi_saya_aktif(Request $request){
    	$this->validate($request, [
    		'id_kurir' => 'required|regex:/[0-9]/',
    	]);
    	$list = new Response_kurir();
    	$result = $list->transaksi_aktif($request->toArray());
    	return ResponseBuilder::result(true,"Transaksi aktif",$result,200,true);
    }

    public function get_rating_kurir(Request $request){
    	$this->validate($request, [
    		'id_kurir' => 'required|regex:/[0-9]/' ]);
    	$result = Sistem::read_rating_kurir($request->toArray());
    	return ResponseBuilder::result(true,"Rating kurir",$result,200,true);
    }

    public function kurir_logout(Request $request){
    	$this->validate($request, [
    		'id_kurir' => 'required|regex:/[0-9]/' ]);
    	return Sistem::kurir_logout($request->toArray());
    }





}
