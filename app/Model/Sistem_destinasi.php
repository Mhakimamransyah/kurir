<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
class Sistem_destinasi extends Model
{
    protected $table = 'sistem_destinasi'; 
	protected $primaryKey = 'id_sistem_destinasi';
	const CREATED_AT = 'created_date';
	const UPDATED_AT = 'modified_date';
	protected $fillable = [
		'alamat','kordinat',"id_user","verified"
	];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    	
    ];
}