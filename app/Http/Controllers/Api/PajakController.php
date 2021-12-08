<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB, Validator;
use App\Model\Pajak;
class PajakController extends Controller
{
    private $response = [];

    public function created(Request $request)
    {
        
        $rules = [
            'nama' => 'required|max:255|string',
            'rate' => 'required|numeric',
            'item' => 'required|numeric'
        ];

        $attributes = [
            'nama'  => 'Nama',
            'rate'  => 'Rate'
        ];

        $message    = [
            'required' => ':attribute tidak boleh kosong',
            'numeric'  => ':attribute harus angka',
            'name.max' => ':attribute tidak boleh lebih dari 255 karakter',
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
            $pajak          = new Pajak;
            $pajak->nama    = $request->nama;
            $pajak->rate    = $request->rate;
            $pajak->item_id = $request->item;
            $pajak->save();

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
        $rules = [
            'id'   => 'required|numeric',
            'nama' => 'required|max:255|string',
            'rate' => 'required|numeric',
            'item' => 'required|numeric'
        ];

        $attributes = [
            'nama'  => 'Nama',
            'rate'  => 'Rate',
            'id'    => 'ID Pajak'
        ];

        $message    = [
            'required' => ':attribute tidak boleh kosong',
            'numeric'  => ':attribute harus angka',
            'name.max' => ':attribute tidak boleh lebih dari 255 karakter',
            'string'   => ':attribute harus mengandung karakter',
        ];

        $validate = Validator::make($request->all(), $rules,$message, $attributes);

        if($validate->fails())
        {
            return response()->json($validate->errors());
        }

        $pajak = Pajak::find($request->id);
        if(empty($pajak))
        {
            $this->response = [
                'code'      => 404,
                'message'   => 'Pajak tidak ditemukan'
            ];

            return response()->json($this->response);
        }

        DB::beginTransaction();
        try
        {
            $pajak->nama    = $request->nama;
            $pajak->rate    = $request->rate;
            $pajak->item_id = $request->item;
            $pajak->save();

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
            'id'    => 'ID Pajak'
        ];

        $message    = [
            'required' => ':attribute tidak boleh kosong',
            'numeric'  => ':attribute harus angka',
            'name.max' => ':attribute tidak boleh lebih dari 255 karakter'
        ];

        $validate = Validator::make($request->all(), $rules,$message, $attributes);

        if($validate->fails())
        {
            return response()->json($validate->errors());
        }

        $pajak = Pajak::find($request->id);
        if(empty($pajak))
        {
            $this->response = [
                'code'      => 404,
                'message'   => 'Pajak tidak ditemukan'
            ];

            return response()->json($this->response);
        }

        DB::beginTransaction();
        try 
        {
            $this->response = [
                'code'      => 200,
                'message'   => 'Data berhasil dihapus',
            ];

            $pajak->delete();
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
