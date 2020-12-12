<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;

class Pelanggan_patokan extends Model
{
    protected $table = 'pelanggan_patokkan'; 
	protected $primaryKey = 'id_pelanggan_patokkan';
	const CREATED_AT = 'created_date';
	const UPDATED_AT = 'modified_date';
	protected $fillable = [
		'kode_patokan','alamat_patokan',"nama_penerima_patokan","no_hp_penerima_patokan",'kordinat_patokan','foto_patokan','detail_patokan','id_jenis_patokan','id_pelanggan'
	];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
  

    public function pelanggan(){
        return $this->belongsTo('App\Model\Pelanggan');
    }

    public static function jenis_patokan($id_jenis_patokan = null){
        if($id_jenis_patokan == null){
           return DB::table('jenis_patokan')->select("*")->get();
        }else{
           return DB::table('jenis_patokan')->select("*")->where("id_jenis_patokan",$id_jenis_patokan)->first()->jenis_patokan;
        }
    }

}