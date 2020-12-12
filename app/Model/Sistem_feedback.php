<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
class Sistem_feedback extends Model
{
    protected $table = 'sistem_feedback'; 
	protected $primaryKey = 'id_sistem_feedback';
	const CREATED_AT = 'created_date';
	const UPDATED_AT = 'modified_date';
	protected $fillable = [
		"review","tipe","id_pelanggan"
	];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    	
    ];
}