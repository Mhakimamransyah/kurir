<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
class Order_jenis extends Model
{
    protected $table = 'order_jenis'; 
	protected $primaryKey = 'id_order_jenis';
	const CREATED_AT = 'created_date';
	const UPDATED_AT = 'modified_date';
	protected $fillable = [
		'jenis','tarif','deskripsi_jenis_order'
	];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    	
    ];

    
}