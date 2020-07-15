<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
class Order_kurir extends Model
{
    protected $table = 'order_kurir'; 
	protected $primaryKey = 'id_order_kurir';
	const CREATED_AT = 'created_date';
	const UPDATED_AT = 'modified_date';
	protected $fillable = [
		'id_order','id_kurir','aksi'
	];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    	
    ];

    public function order(){
      return $this->hasMany('App\Model\Order_m',"id_order_kurir");   
    }


}