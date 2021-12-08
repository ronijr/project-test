<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Item;
use Validator,DB;
class ItemsController extends Controller
{
    private $response = [];

    public function list(Request $request)
    {
        $items = Item::all();
        $data  = [];
        foreach($items as $item)
        {
            $data[] = (object) [
                'id'    => $item->id,
                'nama'  => $item->nama,
                'pajak' => $item->pajak
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
        
        $rules = [
            'nama' => 'required|max:255',
        ];

        $attributes = [
            'nama'  => 'Nama',
        ];

        $message    = [
            'required' => ':attribute tidak boleh kosong',
            'max'      => ':attribute tidak boleh lebih dari 255 karakter'
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

            $this->response = [
                'code'      => 200,
                'message'   => 'Data berhasil disimpan',
                'data'      => $item->first()
            ];
            DB::commit();

        }catch(Exception $e) 
        {
            $this->response = [
                'code'      => 500,
                'message'   => 'Internal server error',
                'data'      => []
            ];
            DB::rollback();
        }

        return response()->json($this->response);
    }

    public function updated(Request $request)
    {
        $rules = [
            'id'   => 'required|numeric',
            'nama' => 'required|max:255',
        ];

        $attributes = [
            'nama'  => 'Nama',
            'id'    => 'ID Item'
        ];

        $message    = [
            'required'   => ':attribute tidak boleh kosong',
            'numeric'   => ':attribute harus angka',
            'name.max'  => ':attribute tidak boleh lebih dari 255 karakter'
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

            $this->response = [
                'code'      => 200,
                'message'   => 'Data berhasil disimpan dan diperbaharui',
                'data'      => $item->first()
            ];
            DB::commit();

        }catch(Exception $e) 
        {
            $this->response = [
                'code'      => 500,
                'message'   => 'Internal server error',
                'data'      => []
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
            $this->response = [
                'code'      => 200,
                'message'   => 'Data berhasil dihapus',
                'data'      => $item
            ];

            $item->delete();
            DB::commit();
        }catch(Exception $e)
        {
            $this->response = [
                'code'      => 500,
                'message'   => 'Internal server error',
                'data'      => []
            ];
            DB::rollback();
        }

        return response()->json($this->response);
    }
}
