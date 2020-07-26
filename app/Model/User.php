<?php
/*=============================================
=            Model User            =
=============================================*/


namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
	protected $table = 'user'; 
	protected $primaryKey = 'id_user';
	const CREATED_AT = 'created_date';
	const UPDATED_AT = 'modified_date';
	protected $fillable = [
		'email', 'token_fcm', 'jenis_registrasi','pass_code_forgot_password', 'password', 'api_token', 'id_role','is_activate','token_aktivasi'
	];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    	'password'
    ];

    public function pelanggan(){
        return $this->hasOne('App\Model\pelanggan','id_user');
    }

    public function kurir(){
        return $this->hasOne('App\Model\kurir','id_user');
    }
}



/*=====  End of Section comment block  ======*/