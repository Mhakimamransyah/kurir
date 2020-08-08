<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
class Role extends Model
{
    protected $table = 'role'; 
	protected $primaryKey = 'id_role';
	const CREATED_AT = 'created_date';
	const UPDATED_AT = 'modified_date';
	protected $fillable = [
		'jenis'
	];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    	
    ];

    public function user(){
       return $this->hasMany('App\Model\User','id_user');  
    }
}