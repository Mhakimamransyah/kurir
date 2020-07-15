<?php 

namespace App\Http\Helper;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ResponseBuilder{

	public static function result($status,$message,$data = [],$http_code,$transaction=false){
        if($transaction == true){
           DB::commit();
        }
		return response()->json([
			"message" => $message,
			"status"  => $status,
			"data"    => $data
		],$http_code);

	}
}

?>

