<?php 

namespace App\Http\Helper;
use Illuminate\Support\Facades\Mail;

class MailBuilder{

	public static function send_email($blade,$data,$to_email,$subject){
         Mail::send($blade, $data, function($message) use ($to_email,$subject)
         {
            $message->to($to_email, " ")->subject($subject);
         });
	}
}

?>

