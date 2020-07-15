<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
class Order_m extends Model
{
    protected $table = 'order'; 
	protected $primaryKey = 'id_order';
	const CREATED_AT = 'created_date';
	const UPDATED_AT = 'modified_date';
	protected $fillable = [
		'kordinat_order', 'alamat_order', 'status', 'id_pelanggan', 'id_order_jenis','destination_failed'
	];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    	
    ];

    public function order_jenis(){
        // di pakek
        return $this->belongsTo('App\Model\Order_jenis','id_order_jenis');
    }

    public function pelanggan(){
       return $this->hasMany('App\Model\Pelanggan','id_order');  
    }

    public function kurir(){
        return $this->belongsToMany("App\Model\Kurir","order_kurir","id_order","id_kurir");
    }

    public function order_detail(){
       return $this->hasMany('App\Model\Order_detail','id_order');   
    }
}