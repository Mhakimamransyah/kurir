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
    'kordinat_order', 'alamat_order', 'status', 'expired_count','id_pelanggan', 'id_order_jenis','destination_failed'
  ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    	
    ];

    public function order_kurir(){
      return $this->hasMany('App\Model\Order_kurir','id_order');
    }

    public function order_jenis(){
        // di pakek
      return $this->belongsTo('App\Model\Order_jenis','id_order_jenis');
    }

    public function pelanggan(){
        // dipakai
     return $this->belongsTo('App\Model\Pelanggan','id_pelanggan');  
   }

   public function kurir(){
     return $this->belongsToMany("App\Model\Kurir","order_kurir","id_order","id_kurir"); 
   }

   public function order_detail(){
    // dipakek
     return $this->hasMany('App\Model\Order_detail','id_order');   
   }

   public function sistem_chat(){
    return $this->hasMany('App\Model\Sistem_chat','id_order');      
  }

}