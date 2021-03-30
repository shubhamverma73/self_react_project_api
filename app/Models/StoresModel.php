<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoresModel extends Model
{
    use HasFactory;
    protected $table 		= 'retailer_list';
    protected $primaryKey 	= 'id';
    public $timestamps 		= false;
    const CREATED_AT 		= 'timestamp';
}
