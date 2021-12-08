<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table      = "m_items";
    protected $primaryKey = "id";
    protected $guarded    = [];
    public $timestamps    = true;
}
