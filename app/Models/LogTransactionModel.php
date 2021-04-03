<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogTransactionModel extends Model
{
    use HasFactory;
    protected $table 		= 'log_transaction';
    protected $primaryKey 	= 'id';
    public $timestamps 		= false;
    const CREATED_AT 		= 'timestamp';
}
