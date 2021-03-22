<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Citylists extends Model
{
    use HasFactory;

    protected $table 		= 'm_citylist';
    protected $primaryKey 	= 'id';
    public $timestamps 		= false;
    const CREATED_AT 		= 'timestamp';
}
