<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
class Order_destinasi extends Model
{
    protected $table = 'order_destinasi'; 
	protected $primaryKey = 'id_order_destinasi';
	const CREATED_AT = 'created_date';
	const UPDATED_AT = 'modified_date';
	protected $fillable = [
		"detail_destinasi","kordinat_destinasi","alamat_destinasi","nama_penerima","no_hp_penerima","kode_patokan"
	];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    	
    ];
}