<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
class Kurir_Kordinat extends Model
{
    protected $table = 'kurir_kordinat'; 
	protected $primaryKey = 'id_kurir_kordinat';
	const CREATED_AT = 'created_date';
	const UPDATED_AT = 'modified_date';
	protected $fillable = [
		'alamat','kordinat','id_kurir'
	];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    	
    ];

    public function kurir(){
        return $this->belongsTo('App\Model\Kurir');
    }
}