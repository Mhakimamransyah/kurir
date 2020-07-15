<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Model\User;
use App\Model\Pelanggan_patokan;
use App\Http\Helper\ResponseBuilder;
use App\Model\Pelanggan;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Exception;
use Illuminate\Support\Facades\File;

class PelangganController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Gunakan middleware disini
        $this->middleware('auth');
    }

    public function updateProfile(Request $request){
   
          $result = Pelanggan::where("id_pelanggan",$request->id_pelanggan);
          if($result->count() > 0){
          	// Jika ada di tabel pelanggan
          	try{
               Pelanggan::where("id_pelanggan",$request->id_pelanggan)->update(["nama_pelanggan"=> $request->nama,"nomor_hp_pelanggan"=>$request->no_hp]);
               return ResponseBuilder::result(true,"Profile diperbarui",$request->toArray(),200);     	
          	}catch(Exception $e){
               return ResponseBuilder::result(false,$e->getMessage(),[],400);
          	}
            
          }else{
          	// Jika tidak ada di tabel pelanggan
             if($request->filled(["id_user"])){
                
                try{
                   Pelanggan::create([
	                  "nama_pelanggan"  => $request->nama,
	                  "nomor_hp_pelanggan" => $request->no_hp,
	                  "id_user"         => $request->id_user
                  ]);

                  return ResponseBuilder::result(false,"Profile diperbarui",[],200);     	
                }catch(Exception $e){
                   return ResponseBuilder::result(false,$e->getMessage(),[],400);
                }
              
             }else{
                return ResponseBuilder::result(false,"id user tidak boleh kosong",[],400);     	
             }
          }
    }

    public function jenis_patokan(Request $request){
          return ResponseBuilder::result(true,"Daftar patokan",Pelanggan_patokan::jenis_patokan(),200); 
    }

    public function register_patokan(Request $request){
       $validator = \Validator::make($request->all(), [
            'alamat_patokan' => 'required',
            'kordinat_patokan' => 'required',
            'detail_patokan' => 'required|min:7',
            'id_jenis_patokan' => 'required|regex:/[0-9]/',
            'id_pelanggan' => 'required|regex:/[0-9]/',
            'foto_patokan' => 'required|image|max:10000' // maksium foto 10 mb
        ]);

        if ($validator->fails()) {
            return ResponseBuilder::result(false,$validator->errors(),[],400);
        }else{
            $nama_file = $request->id_pelanggan."-".str_random(20).".".$request->file('foto_patokan')->getClientOriginalExtension();
            $request->file('foto_patokan')->move('patokan',$nama_file);

            if($request->filled(['id_pelanggan_patokkan'])){
               // edit 
               $result = Pelanggan_patokan::where('id_pelanggan_patokkan',$request->id_pelanggan_patokkan);
               if($result->count() > 0){
                 

                 $file_path = 'patokan/'.$result->first()->foto_patokan; 
                 unlink($file_path);

                 $result->update([
                   "alamat_patokan" => $request->alamat_patokan,
                   "kordinat_patokan" => $request->kordinat_patokan,
                   "detail_patokan" => $request->detail_patokan,
                   "id_jenis_patokan" => $request->id_jenis_patokan,
                   "id_pelanggan" => $request->id_pelanggan,
                   "foto_patokan" => $nama_file
                 ]);
                 return ResponseBuilder::result(true,"Sukses, patokan di perbarui",[],200);

               }else{
                 return ResponseBuilder::result(false,"Id pelanggan patokan tidak ditemukan",[],400);
               }
            }else{
              // add new
               try{
                 Pelanggan_patokan::create([
                   "alamat_patokan" => $request->alamat_patokan,
                   "kordinat_patokan" => $request->kordinat_patokan,
                   "detail_patokan" => $request->detail_patokan,
                   "id_jenis_patokan" => $request->id_jenis_patokan,
                   "id_pelanggan" => $request->id_pelanggan,
                   "foto_patokan" => $nama_file
                 ]);
                 return ResponseBuilder::result(true,"Sukses, patokan di tambahkan",[],200);
              }catch(Exception $e){
                return ResponseBuilder::result(false,$e->getMessage(),[],400);
              }
            }
        }
    }

   

    public function hapus_patokan(Request $request){
        if($request->filled(["id_pelanggan_patokkan"])){
           $result = Pelanggan_patokan::where('id_pelanggan_patokkan',$request->id_pelanggan_patokkan);
           if($result->count() > 0){

             $file_path = 'patokan/'.$result->first()->foto_patokan; 
             unlink($file_path);     

             Pelanggan_patokan::destroy($request->id_pelanggan_patokkan);
             return ResponseBuilder::result(true,"Sukses, patokan di hapus",[],200);
           }else{
             return ResponseBuilder::result(false,"ID pelanggan Patokkan tidak ditemukan",[],400);  
           }
        }else{
          return ResponseBuilder::result(false,"ID pelanggan Patokkan tidak boleh kosong",[],400);
        }
    }

    public function view_patokan(Request $request){
      if($request->filled(["id_pelanggan"])){
           if($request->filled(["id_pelanggan_patokkan"])){
               // detail
                return ResponseBuilder::result(true,"Sukses, Detail",Pelanggan_patokan::Join('jenis_patokan','jenis_patokan.id_jenis_patokan','=','pelanggan_patokkan.id_jenis_patokan')->where('id_pelanggan_patokkan',$request->id_pelanggan_patokkan)->where("id_pelanggan",$request->id_pelanggan)->first(),200);
           }else{
              // listing
              return ResponseBuilder::result(true,"Sukses, Listing",Pelanggan_patokan::Join('jenis_patokan','jenis_patokan.id_jenis_patokan','=','pelanggan_patokkan.id_jenis_patokan')->where('id_pelanggan',$request->id_pelanggan)->get(),200);
           }
      }else{
          return ResponseBuilder::result(false,"ID pelanggan tidak boleh kosong",[],400);
      } 
    }    

    
}
