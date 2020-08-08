<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
class Sistem_image_information extends Model
{
    protected $table = 'sistem_image_information'; 
	protected $primaryKey = 'id_sistem_image_information';
	const CREATED_AT = 'created_date';
	const UPDATED_AT = 'modified_date';
	protected $fillable = [
		"foto_banner","html_file","sequence","active"
	];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    	
    ];
}