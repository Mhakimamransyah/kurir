<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Model\User;
use App\Model\Pelanggan;
use App\Http\Helper\ResponseBuilder;
use App\Http\Helper\MailBuilder;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
     
    private $session_pelanggan;
    private $session_kurir;
    private $session_admin;

    public function __construct()
    {
        $this->session_pelanggan = [
            "id_pelanggan" => "",
            "email" => "",
            "api_token" => "",
            "is_activate" => "",
            "id_role" => "",
            "no_hp"   => "",
            "nama"    => ""
        ];
    }

    public function list_user(Request $request){
        $result = [];
        if($request->id == null){
           $result = User::all()->toArray();
           return ResponseBuilder::result(true,"sukses, List all user",$result,200);
        }else{
           $result = User::where("id_user",$request->id)->first();
           return ResponseBuilder::result(true,"sukses, List with user id ".$request->id,$result,200);
        }
    }

    public function create_google_sign_in(Request $request){
      // only for pelanggan
        
        if($request->filled(["email","token_fcm","nama"])){
            
            $email = $request->input('email');
            $token_fcm = $request->input('token_fcm');
            $nama = $request->input('nama');
            $user = User::where('email',$email);
                    
            // hanya id role 2 yakni ; pelanggan
            if($user->count() == 0){
               
               // baru pertama kali sign in

               $regUser = User::create([
                 "email"     => $email,
                 "token_fcm" => $token_fcm,
                 "jenis_registrasi" => 'Gmail',
                 "id_role"   => 2,
                 "api_token" => str_random(50),
                 "is_activate" => 'yes',
                 "token_aktivasi" => NULL
               ]);

               $regPelanggan = Pelanggan::create([
                  "nama_pelanggan"    => $nama,
                  "id_user"           => $regUser->id_user
               ]);

               $result = User::where("id_user",$regUser->id_user)->first();
        
               $this->session_pelanggan= [
                  "id_user"      => $regUser->id_user,
                  "id_pelanggan" => $result->pelanggan->id_pelanggan,
                  "email" => $result->email,
                  "api_token" => $result->api_token,
                  "is_activate" => $result->is_activate,
                  "id_role" => $result->id_role,
                  "no_hp"   => $result->pelanggan->nomor_hp_pelanggan,
                  "nama"    => $result->pelanggan->nama_pelanggan
               ];
               
               MailBuilder::send_email('email.welcome',[],$result->email,"Terima kasih telah mendaftar di kurir");

               return ResponseBuilder::result(true,"sukses",$this->session_pelanggan,200);    

            }else if($user->first()->jenis_registrasi == 'Gmail' && $user->first()->id_role == 2){
              
               // telah sign in sebelumnya menggunakan akun gmail

               // periksa apakah ada datanya di tabel pelanggan, jika tidak ada tambahkan
              if(Pelanggan::where("id_user",$user->first()->id_user)->count() == 0){
                 // di tabel pelanggan tidak ada
                  Pelanggan::create([
                     "nama_pelanggan" => $nama,
                     "id_user"        => $user->first()->id_user
                  ]);
              }
              

              // perbarui ulang token fcm dan token api ketika login lagi
              User::where("email",$email)->update(["token_fcm"=>$token_fcm,"api_token"=>str_random(50)]);

              $result = User::where("id_user",$user->first()->id_user)->first();

               $this->session_pelanggan= [
                  "id_user"      => $user->first()->id_user,
                  "id_pelanggan" => $result->pelanggan->id_pelanggan,
                  "email" => $result->email,
                  "api_token" => $result->api_token,
                  "is_activate" => $result->is_activate,
                  "id_role" => $result->id_role,
                  "no_hp"   => $result->pelanggan->nomor_hp_pelanggan,
                  "nama"    => $result->pelanggan->nama_pelanggan
               ];
              
              return ResponseBuilder::result(true,"sukses",$this->session_pelanggan,200);


            }else if($user->first()->jenis_registrasi == 'Manual' && $user->first()->id_role == 2){
              // telah sign in sebelumnya menggunakan akun manual
               return ResponseBuilder::result(false,"Akun telah terdaftar secara manual, silahkan login dengan email dan password",[],400);
            }else{
               return ResponseBuilder::result(false,"gagal, kondisi tidak terpenuh",[],400);    
            }
        }else{
          return ResponseBuilder::result(false,"gagal, parameter tidak lengkap".$request->id,[],400);  
        }
    }

    public function doLoginUser(Request $request){
       // hanya untuk login manual
        $validator = \Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6|regex:/[0-9]/|regex:/[a-zA-z]/',
            'token_fcm' => 'required',
            'role'   => 'required|regex:/[0-9]/' // 2 untuk pelanggan, 3 untuk kurir
        ]);

        if ($validator->fails()) {
            // format tidak valid
            return ResponseBuilder::result(false,$validator->errors(),[],400);      
        }else{
            $result = User::where("email",$request->input('email'))->where("id_role",$request->input('role'));
            if($result->count() > 0){
                
                if($result->first()->jenis_registrasi == "Manual"){

                   $password_hash = $result->first()->password;
              
                   if(Hash::check($request->input('password'),$password_hash)){
                      
                      if($result->first()->is_activate == "yes"){
                          
                         User::where("id_user",$result->first()->id_user)->update(['api_token'=>str_random(50),'token_fcm'=>$request->input('token_fcm')]);

                         $response_data = [];
                         if($request->input('role') == 2){
                          
                          // pelanggan
                            $data = User::leftJoin('pelanggan','pelanggan.id_user','=','user.id_user')->select(["*"])->where("user.id_user",$result->first()->id_user)->first();

                           $this->session_pelanggan= [
                              "id_user" => $result->first()->id_user,
                              "id_pelanggan" => $data->id_pelanggan,
                              "email" => $data->email,
                              "api_token" => $data->api_token,
                              "is_activate" => $data->is_activate,
                              "id_role" => $data->id_role,
                              "no_hp"   => $data->nomor_hp_pelanggan,
                              "nama"    => $data->nama_pelanggan
                           ];
                           $response_data = $this->session_pelanggan;

                         }else if($request->input('role') == 3){
                          //kurir
                             $data = User::leftJoin('kurir','kurir.id_user','=','user.id_user')->select(["*"])->where("user.id_user",$result->first()->id_user)->first();

                             $kurir_login_data = [
                                "id_user" => $result->first()->id_user,
                                "id_kurir" => $data->id_kurir,
                                "email" => $data->email,
                                "api_token" => $data->api_token,
                                "is_activate" => $data->is_activate,
                                "id_role" => $data->id_role,
                                "no_hp"   => $data->no_hp_kurir,
                                "nama"    => $data->nama_kurir,
                                "alamat"  => $data->alamat_kurir,
                                "foto"    => $data->foto_kurir,
                                "plat_nomor" => $data->plat_nomor,
                                "mode" => $data->mode,
                                "nomor_ktp" => $data->nomor_ktp,
                             ];
                             $response_data = $kurir_login_data;
                         }
                         
                        return ResponseBuilder::result(true,"Login berhasil",$response_data,200);

                      }else{
                         return ResponseBuilder::result(false,"Akun anda belum di aktivasi, periksa email untuk aktivasi akun anda",[],400);  
                      }
                   }else{
                      return ResponseBuilder::result(false,"Password anda salah",[],400);
                   }
                }else{
                  return ResponseBuilder::result(false,"Akun anda terdaftar dengan google sign in, silahkan login dengan google sign in",[],400);
                }
                
            }else{
                  return ResponseBuilder::result(false,"Email anda tidak ditemukan",[],400);
            }
        }

    }


    public function verify_account(Request $request){
       if($request->filled(["token","code"])){
           $token   =  $request->token;
           $id_user =  explode("@", $request->code);
           $result  = User::where("token_aktivasi",$token)->where("id_user",$id_user[0]);

           if($result->count() == 1){

              $email   =  $result->get()->first()->email;
              $id_role = $result->get()->first()->id_role;

              $result->update(['is_activate'=>'yes','token_aktivasi'=>""]);

              if($id_role == 2){
                // welcome message untuk akun pelanggan
                 MailBuilder::send_email('email.welcome',[],$email,"Terima kasih telah mendaftar di kurir");
                 // Blade view here sukses
                 return view("email.afterverification");
              }else if($id_role == 3){
                // welcome message untuk akun kurir

              }       
           }else{
            // blade view here, activate error
              return "Error on verification";
           }
       }else{
          // blade view here, activation error
         return "Error on verification";
       }
    }

    public function create_manual(Request $request){

         $validator = \Validator::make($request->all(), [
            'email' => 'required|email|unique:user',
            'confirm_password' => 'required|min:6|regex:/[0-9]/|regex:/[a-zA-z]/',
            'password' => 'required|min:6|regex:/[0-9]/|regex:/[a-zA-z]/|same:confirm_password',
            'role' => 'required|regex:/[0-9]/'
         ]);

         if ($validator->fails()) {
            // format tidak valid
            return ResponseBuilder::result(false,$validator->errors(),[],400);      
         }else{
            // format valid 
            
            $email    = $request->input('email');
            $password = Hash::make($request->input('password'));
            $id_role = $request->input('role');

            $data = [ 
              "email"            => $email, // hapus karakter spesial
              "password"         => $password, // hapus karakter spesial
              "jenis_registrasi" => "Manual",
              "id_role"          => $id_role,
              "is_activate"      => 'no', // manual registration need activate via email
              "token_aktivasi"   => str_random(50)
            ]; 
            
            try{
                $registrasi = User::create($data);
                if($registrasi){
                  
                  // sending email here
                  $mail_data = [
                     "link" => url("user/verifymyaccount?token=".$data['token_aktivasi']."&code=".$registrasi->id_user."@".str_random(15))
                  ];

                  MailBuilder::send_email('email.verification',$mail_data,$data['email'],"Verifikasi akun kurir anda");

                  return ResponseBuilder::result(true,"data berhasil ditambahkan",$request->except("confirm_password"),200);   
            }}catch(Exception $e){
                   return ResponseBuilder::result(false,$e->getMessage(),[],400);
            }
         }
    }

    public function send_forgot_password(Request $request){
      
       $this->validate($request,[
          "email" => "required"
       ]);

       // periksa apakah email ada dan teregistrasi secara manual
       $user = User::where("email",$request->email);
       if($user->exists()){
         $jenis_registrasi = $user->first()->jenis_registrasi;
         if($jenis_registrasi == "Manual"){

            $pass_code = rand(10000,99999);
            $user->update([
              "pass_code_forgot_password" => $pass_code
            ]);
            MailBuilder::send_email('email.forgot_password',["code"=>$pass_code],$user->first()->email,"Forgot Password");
             return ResponseBuilder::result(true,"Pass code dikirimkan",[],200); 

         }else if($jenis_registrasi == "Gmail"){
          // terdaftar menggunakan google sign in 
            return ResponseBuilder::result(false,"Anda terdaftar menggunakan google sign in",[],400);
         }
       }else{
         return ResponseBuilder::result(false,"Email tidak di temukan",[],400);
       }
    }

    public function update_forgot_password(Request $request){
       
       $this->validate($request,[
          "email" => "required",
          "pass_code" => "required|regex:/[0-9]/",
          'password' => 'required|min:6|regex:/[0-9]/|regex:/[a-zA-z]/|same:confirm_password',
          'confirm_password' => 'required|min:6|regex:/[0-9]/|regex:/[a-zA-z]/'
       ]);
       
       // periksa apakah email terdaftar
       $user = User::where("email",$request->email);
       if($user->exists()){
            // periksa jenis registrasi
         $jenis_registrasi = $user->first()->jenis_registrasi;
         if($jenis_registrasi == "Manual"){
            // periksa apakah pass code cocok
            $pass_code = $user->first()->pass_code_forgot_password;
            if($pass_code == $request->pass_code){
               $result = tap($user)->update([
                 "password" => Hash::make($request->input('password')),
                 "pass_code_forgot_password" => null
               ]);
               // send email here
               MailBuilder::send_email('email.reset_password',
                ["date"=>date_format($result->first()->modified_date,"d-m-Y H:i:s")],
                $request->email,
                "Password Diperbarui");
               return ResponseBuilder::result(true,"Password berhasil di perbarui",[],200);
            }else{
               return ResponseBuilder::result(false,"Pass code tidak cocok",[],400);
            }
         }else if($jenis_registrasi == "Gmail"){
          // terdaftar menggunakan google sign in 
            return ResponseBuilder::result(false,"Anda terdaftar menggunakan google sign in",[],400);
         }
       }else{
         return ResponseBuilder::result(false,"Email tidak di temukan",[],400);
       }

    }

    public function update_password(Request $request){

      $this->validate($request,[
        "email" => "required",
        'password_lama' => 'required|min:6|regex:/[0-9]/|regex:/[a-zA-z]/',
        'password_baru' => 'required|min:6|regex:/[0-9]/|regex:/[a-zA-z]/|same:confirm_password_baru',
        'confirm_password_baru' => 'required|min:6|regex:/[0-9]/|regex:/[a-zA-z]/'
      ]);

       // periksa apakah email terdaftar
       $user = User::where("email",$request->email);
       // var_dump($user->first()->password);
       // var_dump($request->input('password_lama'));
       // exit();
       if($user->exists()){
          // periksa kecocokan dengan password lama
          if(Hash::check($request->input('password_lama'),$user->first()->password)){
            // password lama cocok
            $new_password = Hash::make($request->input('password_baru'));
            $result = tap($user)->update([
                 "password" => Hash::make($request->input('password_lama'))
            ]);
            MailBuilder::send_email('email.reset_password',
                ["date"=>date_format($result->first()->modified_date,"d-m-Y H:i:s")],
                $request->email,
                "Password Diperbarui");
            return ResponseBuilder::result(true,"Password berhasil di perbarui",[],200);
          }else{
            // password lama tidak cocok
            return ResponseBuilder::result(false,"Password lama anda tidak cocok",[],400);
          }
       }else{
         return ResponseBuilder::result(false,"Email tidak di temukan",[],400);
       }

    }

}
