<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetsModel extends Model
{
    use HasFactory;
    protected $table 		= 'targets';
    protected $primaryKey 	= 'id';
    public $timestamps 		= false;
    const CREATED_AT 		= 'timestamp';
}
