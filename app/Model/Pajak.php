<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Pajak extends Model
{
    protected $table      = "pajak";
    protected $primaryKey = "id";
    protected $guarded    = [];
    public $timestamps    = true;

    
}
