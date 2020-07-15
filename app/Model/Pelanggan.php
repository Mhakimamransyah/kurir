<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
class Pelanggan extends Model
{
    protected $table = 'pelanggan'; 
	protected $primaryKey = 'id_pelanggan';
	const CREATED_AT = 'created_date';
	const UPDATED_AT = 'modified_date';
	
	protected $fillable = [
		"nomor_hp_pelanggan","nama_pelanggan","id_user"
	];

	public function user(){
        return $this->belongsTo('App\Model\User');
    }

    public function patokan(){
       return $this->hasMany('App\Model\Pelanggan_patokan');
    }

}