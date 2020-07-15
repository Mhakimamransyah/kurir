<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
class Order_barang extends Model
{
    protected $table = 'order_barang'; 
	protected $primaryKey = 'id_order_barang';
	const CREATED_AT = 'created_date';
	const UPDATED_AT = 'modified_date';
	protected $fillable = [
		'nama_barang','jumlah_paket','foto_barang','catatan_kurir','estimasi_berat'
	];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    	
    ];
}