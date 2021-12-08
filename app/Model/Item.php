<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use DB;
class Item extends Model
{
    protected $table      = "m_items";
    protected $primaryKey = "id";
    protected $guarded    = [];
    public $timestamps    = true;

    public function pajak()
    {
        return $this->hasMany(Pajak::class,"item_id","id")
        ->select(
            'id',
            'nama',
             DB::raw("concat(rate,'%') as rate")
        );
    }

    public static function get()
    {
        return Item::select('id','nama')
        ->addSelect(DB::raw('GROUP_CONCAT((select * from tbl_pajak where item_id = tbl_m_items.id)) as pajak'))
        ->get();
    }
}
