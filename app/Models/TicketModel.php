<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketModel extends Model
{
    use HasFactory;
    protected $table 		= 'ticket';
    protected $primaryKey 	= 'id';
    public $timestamps 		= false;
    const CREATED_AT 		= 'timestamp';

    public function getTypeAttribute($value)
    {
        return strtoupper($value); //Accessors is called when we get value from table
    }

    public function getUserTypeAttribute($value) {
    	return strtoupper($value);
    }
}
