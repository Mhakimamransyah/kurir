<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
class Kurir extends Model
{
    
    protected $table = 'kurir'; 
	protected $primaryKey = 'id_kurir';
	const CREATED_AT = 'created_date';
	const UPDATED_AT = 'modified_date';
	protected $fillable = [
		'nama_kurir', 'alamat_kurir', 'no_hp_kurir', 'foto_kurir', 'plat_nomor', 'mode','nomor_ktp','id_user'
	];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    	
    ];

    public function user(){
        return $this->belongsTo('App\Model\User');
    }

    public function kordinat(){
       return $this->hasMany('App\Model\Kurir_Kordinat','id_kurir');   
    }



}