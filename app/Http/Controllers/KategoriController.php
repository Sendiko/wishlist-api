<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KategoriController extends Controller
{
    public function index(){
        $data = Kategori::all();
        if($data){
            return response()->json([
                'status' => true,
                'message' => "Data berhasil ditemukan",
                'data' => $data
            ], 200);
        }else{
            return response()->json([
                'status' => false,
                'message' => "Data tidak ditemukan",
                'data' => $data
            ], 404);
        }
        
    }

    public function show($id){
        $data = Kategori::findOrFail($id);
        if($data){
            return response()->json([
                'status' => true,
                'message' => "Data berhasil ditemukan",
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Data berhasil ditemukan"
            ]);
        }
    }

    public function store(Request $request){
        $dataKategori = new Kategori;

        $rules = [
            'name' => 'required|string'
        ];

        $validator = Validator::make($request->all(), $rules);
        
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => "Gagal melakukan tambah kategori",
                'data' => $validator->errors()
            ]);
        }

        $dataKategori->name = $request->name;

        $post = $dataKategori->save();

        if($post){
            return response()->json([
                'status' => true,
                'message' => "Data berhasil ditambahkan",
                'data' => $post
            ]);
        }
    }

    public function update(Request $request, $id){
        $dataKategori = Kategori::find($id);

        if(empty($dataKategori)){
            return response()->json([
                'status' => false,
                'message' => "Kategori tidak ditemukan",
            ], 404);
        }

        $rules = [
            'name' => 'required|string'
        ];

        $validator = Validator::make($request->all(), $rules);
        
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => "Gagal melakukan update kategori",
                'data' => $validator->errors()
            ]);
        }

        $dataKategori->name = $request->name;

        $post = $dataKategori->save();

        if($post){
            return response()->json([
                'status' => true,
                'message' => "Data berhasil diperbarui",
                'data' => $post
            ]);
        }
    }

    public function destroy($id){
        $data = Kategori::find($id);
            if(empty($data)){
                return response()->json([
                    'status' => false,
                    'message' => "Data tidak ditemukan",
                    'data' => $data
                ], 404);
            }

            $post = $data->delete();

            return response()->json([
                'status' => 'true',
                'message' => 'Data berhasil dihapus'
            ], 200);
        }
    }