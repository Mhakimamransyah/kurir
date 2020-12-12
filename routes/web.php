<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
	return $router->app->version();
});

$router->get('/key',function(){
	return str_random(32);
});


// Email pratinjau
$router->group(['prefix'=>'email'],function() use ($router){
	$router->get('/verifikasi',function(){
		return view('email.verification',["link"=>"test"]);
	});
	$router->get('/welcome',function(){
		return view('email.welcome',["link"=>"test"]);
	});
	$router->get('/landing_verification',function(){
		return view('email.afterverification',["link"=>"test"]);
	});
	$router->get('/forgot_password',function(){
		return view('email.forgot_password',["code"=>8281]);
	});
	$router->get('/after_reset_password',function(){
		return view('email.reset_password',["date"=>""]);
	});
});


// router untuk akses user
$router->group(['prefix'=>'user'],function() use ($router){
	$router->get('/',function(){
		return "<h1>ini router khusus user</h1>";
	});
	$router->get('/verifymyaccount','UserController@verify_account');
	$router->post('/register','UserController@create_manual');
	$router->post('/google_sign_in','UserController@create_google_sign_in');
	$router->post('/login','UserController@doLoginUser');
	$router->post('/forgot','UserController@send_forgot_password');
	$router->post('/update_forgot_password','UserController@update_forgot_password');
	$router->post('/update_password','UserController@update_password');
});

// router untuk akses pelanggan
$router->group(['prefix'=>'pelanggan'],function() use ($router){
	$router->get('/',function(){
		return "<h1>ini router khusus pelanggan</h1>";
	});
	$router->put('/update_profil',"PelangganController@updateProfile");
	$router->get('/jenis_patokan',"PelangganController@jenis_patokan");
	$router->post('/register_patokan',"PelangganController@register_patokan");
	$router->delete('/delete_patokan',"PelangganController@hapus_patokan");
	$router->get('/lihat_patokan',"PelangganController@view_patokan");
});

// router untuk akses kurir
$router->group(['prefix'=>'kurir'],function() use ($router){
	$router->get('/',function(){
		return "<h1>ini router khusus kurir</h1>";
	});
	$router->get('/get','UserController@list_user');
	$router->post('/tambah_kordinat','KurirController@add_cordinate');
	$router->put('/ubah_kordinat','KurirController@edit_cordinate');
	$router->delete('/hapus_kordinat','KurirController@delete_cordinate');
	$router->get('/lihat_kordinat','KurirController@get_cordinate');
	$router->post('/update_real_time_kordinat','KurirController@update_kordinat_terkini');
	$router->get('/get_real_time_kordinat','KurirController@get_kordinat_terkini');
	$router->post('/mode','KurirController@mode_kurir');
	$router->get('/order_baru','KurirController@get_order_baru_saya');
	$router->get('/transaksi_aktif','KurirController@get_is_transaksi_saya_aktif');
	$router->get('/rating','KurirController@get_rating_kurir');
	$router->post('/logout','KurirController@kurir_logout');
});

// router untuk hal-hal yg berhubungan dengan order
$router->group(['prefix'=>'order'],function() use ($router){
	$router->post('/baru','OrderController@order_baru');
	$router->post('/detail','OrderController@detail_baru');
	$router->get('/jenis','OrderController@get_ketentuan_tarif');
	$router->get('/preview','OrderController@preview_order_tarif');
	$router->get('/patokan','OrderController@get_destinasi_by_kode_patokan');
	$router->get('/sesi','OrderController@get_status_order');
	$router->post('/charge','OrderController@kurir_charge_order'); // di luar sesi gunakan FCM
	$router->post('/deal_charge','OrderController@pelanggan_deal_charge'); // di luar sesi gunakan FCM
	$router->post('/pelanggan_cancel_order','OrderController@pelanggan_cancel_order'); // di luar sesi gunakan FCM
	$router->post('/kurir_deal','OrderController@kurir_deal_order');
	$router->post('/kurir_tolak','OrderController@kurir_tolak_order');
	$router->get('/lihat','OrderController@lihat_order');
	$router->get('/maps','OrderController@lihat_maps');
	

	$router->post('/selesai_destinasi','OrderController@kurir_selesaikan_destinasi'); // di luar sesi gunakan FCM
	$router->post('/rating','OrderController@pelanggan_rating_kurir');
});

// router untuk sistem
$router->group(['prefix'=>'sistem','middleware' => 'auth'],function() use ($router){
	$router->get('/',function(){
		return "<h1>Router khusus sistem</h1>";
	});
	$router->get('/pesanan_pengiriman','SistemController@order_pengiriman_aktif');
	$router->get('/landing','SistemController@sistem_landing');
	$router->get('/page','SistemController@sistem_page');
	$router->get('/destinasi','SistemController@sistem_destinasi');
	$router->post('/notifikasi','SistemController@sistem_notif');
	$router->post('/feedback','SistemController@sistem_feedback');
	$router->post('/chat','SistemController@sistem_send_chat');
	$router->post('/read-chat','SistemController@sistem_read_chat');
});


