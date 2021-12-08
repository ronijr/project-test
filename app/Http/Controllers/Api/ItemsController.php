<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Item;
use App\Model\Pajak;
use Validator,DB;
class ItemsController extends Controller
{
    private $response = [];

    public function list(Request $request)
    {
        $items = Item::get();
        $data  = [];

        foreach($items as $item)
        {

            $data[] = (object) [
                'id'    => $item->id,
                'nama'  => $item->nama,
                'pajak' => json_decode('['.$item->pajak.']')
            ];
        }

        $this->response = [
            'code'    => 200,
            'message' => 'success',
            'data'    => $data
        ];

        return response()->json($this->response);
    }

    public function created(Request $request)
    {
        if(!is_array($request->pajak))
        {
            $this->response = [
                'code'      => 403,
                'message'   => 'Pajak harus berupa array object',
            ];
            return response()->json($this->response);
        }

        if(count($request->pajak) < 2)
        {
            $this->response = [
                'code'      => 403,
                'message'   => 'Pajak minimal 2',
            ];
            return response()->json($this->response);
        }

        $rules = [
            'nama' => 'required|max:255|string',
            'pajak.*.nama' => 'required|max:255|string',
            'pajak.*.rate' => 'required|numeric'
        ];

        $attributes = [
            'nama'  => 'Nama',
            'pajak.*.nama' => 'Nama Pajak',
            'pajak.*.rate' => 'Rate Pajak'
        ];

        $message    = [
            'required' => ':attribute tidak boleh kosong',
            'max'      => ':attribute tidak boleh lebih dari 255 karakter',
            'string'   => ':attribute harus mengandung karakter',
        ];

        $validate = Validator::make($request->all(), $rules,$message, $attributes);

        if($validate->fails())
        {
            return response()->json($validate->errors());
        }
        DB::beginTransaction();
        try
        {
            $item          = new Item;
            $item->nama    = $request->nama;
            $item->save();

            foreach($request->pajak as $pajak_item)
            {
                $pajak          = new Pajak;
                $pajak->item_id = $item->id;
                $pajak->nama    = $pajak_item['nama'];
                $pajak->rate    = $pajak_item['rate'];
                $pajak->save();
            }

            $this->response = [
                'code'      => 200,
                'message'   => 'Data berhasil disimpan',
            ];
            DB::commit();

        }catch(Exception $e) 
        {
            $this->response = [
                'code'      => 500,
                'message'   => 'Internal server error',
            ];
            DB::rollback();
        }

        return response()->json($this->response);
    }

    public function updated(Request $request)
    {
        if(!is_array($request->pajak))
        {
            $this->response = [
                'code'      => 403,
                'message'   => 'Pajak harus berupa array object',
            ];
            return response()->json($this->response);
        }

        if(count($request->pajak) < 2)
        {
            $this->response = [
                'code'      => 403,
                'message'   => 'Pajak minimal 2',
            ];
            return response()->json($this->response);
        }

        $rules = [
            'id'           => 'required|numeric',
            'nama'         => 'required|max:255|string',
            'pajak.*.nama' => 'required|max:255|string',
            'pajak.*.rate' => 'required|numeric',
            'pajak.*.id'   => 'required|numeric'
        ];

        $attributes = [
            'nama'         => 'Nama',
            'id'           => 'ID Item',
            'pajak.*.nama' => 'Nama Pajak',
            'pajak.*.rate' => 'Rate Pajak',
            'pajak.*.id'   => 'ID Pajak'
        ];

        $message    = [
            'required'   => ':attribute tidak boleh kosong',
            'numeric'    => ':attribute harus angka',
            'name.max'   => ':attribute tidak boleh lebih dari 255 karakter',
            'string'     => ':attribute harus mengandung karakter',
        ];

        $validate = Validator::make($request->all(), $rules,$message, $attributes);

        if($validate->fails())
        {
            return response()->json($validate->errors());
        }

        $item = Item::find($request->id);
        if(empty($item))
        {
            $this->response = [
                'code'      => 404,
                'message'   => 'Item tidak ditemukan'
            ];

            return response()->json($this->response);
        }

        DB::beginTransaction();
        try
        {
            $item->nama    = $request->nama;
            $item->save();

            foreach($request->pajak as $pajak_item)
            {
                $pajak          = Pajak::updateOrCreate(['item_id'=>$item->id,'id'=>$pajak_item['id']]);
                $pajak->item_id = $item->id;
                $pajak->nama    = $pajak_item['nama'];
                $pajak->rate    = $pajak_item['rate'];
                $pajak->save();
            }

            $this->response = [
                'code'      => 200,
                'message'   => 'Data berhasil disimpan dan diperbaharui',
            ];
            DB::commit();

        }catch(Exception $e) 
        {
            $this->response = [
                'code'      => 500,
                'message'   => 'Internal server error',
            ];
            DB::rollback();
        }

        return response()->json($this->response);
    }

    public function deleted(Request $request)
    {
        $rules = [
            'id'   => 'required|numeric',
        ];

        $attributes = [
            'id'    => 'ID Items'
        ];

        $message    = [
            'required' => ':attribute tidak boleh kosong',
            'numeric'  => ':attribute harus angka',
        ];

        $validate = Validator::make($request->all(), $rules,$message, $attributes);

        if($validate->fails())
        {
            return response()->json($validate->errors());
        }

        $item  = Item::find($request->id);
        $pajak = Pajak::where("item_id",$request->id)->get();
        if(empty($item))
        {
            $this->response = [
                'code'      => 404,
                'message'   => 'Item tidak ditemukan'
            ];

            return response()->json($this->response);
        }

        DB::beginTransaction();
        try 
        {
           
            $item->delete();
            foreach($pajak as $pajak_item)
            {
                $pajak_item->delete();
            }

            $this->response = [
                'code'      => 200,
                'message'   => 'Data berhasil dihapus',
            ];
            DB::commit();
        }catch(Exception $e)
        {
            $this->response = [
                'code'      => 500,
                'message'   => 'Internal server error',
            ];
            DB::rollback();
        }

        return response()->json($this->response);
    }
}
