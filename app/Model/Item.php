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
        return Item::select('m_items.id','m_items.nama')
        ->addSelect(DB::raw("GROUP_CONCAT(JSON_OBJECT('id',tbl_pajak.id,'nama',tbl_pajak.nama,'rate',concat(tbl_pajak.rate,'%'))) as pajak"))
        ->join('pajak',function($join){
            $join->on('pajak.item_id','=','m_items.id');
        })
        ->groupBy('m_items.id','m_items.nama')
        ->orderBy('m_items.created_at','desc')
        ->get();
        
    }
}
