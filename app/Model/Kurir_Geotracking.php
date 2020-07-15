<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
class Kurir_Geotracking extends Model
{
    protected $table = 'kurir_geotracking'; 
	protected $primaryKey = 'id_kurir_geotracking';
	const CREATED_AT = 'created_date';
	const UPDATED_AT = 'modified_date';
	protected $fillable = [
		'kordinat_terkini','id_kurir'
	];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    	
    ];

    public function kurir(){
        
    }
}