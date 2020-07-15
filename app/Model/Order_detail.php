<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
class Order_detail extends Model
{
    protected $table = 'order_detail'; 
	protected $primaryKey = 'id_order_detail';
	const CREATED_AT = 'created_date';
	const UPDATED_AT = 'modified_date';
	protected $fillable = [
		'jarak','tarif_charge_jarak','tarif_charge_beban','foto_selesai','id_order_destinasi','id_order_barang','id_order'
	];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    	
    ];

    public function order_barang(){
       // di pakai
       return $this->belongsTo("App\Model\order_barang","id_order_barang");
    }

    public function order_destinasi(){
        // di pakai
       return $this->belongsTo("App\Model\order_destinasi","id_order_destinasi");
    }
}