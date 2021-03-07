<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rso_user extends Model
{
    use HasFactory;

    protected $table 		= 'rso_user';
    protected $primaryKey 	= 'id';
    public $timestamps 		= false;
    const CREATED_AT 		= 'timestamp';

    /*public function setNameAttribute($value) {
    	return $this->attributes('name') = ucfirst($value); //This function is called mutator where we can change value behaviour
    }*/
}
