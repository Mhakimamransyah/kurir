<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
class Sistem_chat extends Model
{
    protected $table = 'sistem_chat'; 
	protected $primaryKey = 'id_sistem_chat';
	const CREATED_AT = 'created_date';
	const UPDATED_AT = 'modified_date';
	protected $fillable = [
		'pesan','id_user_pengirim','terbaca','id_order'
	];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    	
    ];

     public function order(){
        return $this->belongsTo('App\Model\Order_m','id_order');
    }
}