<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();

        if(count($products) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $products
            ], 200);

            return response([
                'message' => 'Empty',
                'data' => null
            ], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $storeData = $request->all();
        $validate  = FacadesValidator::make($storeData, [
            'nama_barang' => 'required|max:60|unique:products',
            'kode' => 'required',
            'harga' => 'required|numeric',
            'jumlah' => 'required|numeric'
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $product = Product::create($storeData);
        return response([
            'message' => 'Add product Success',
            'data' => $product
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);

        if(!is_null($product)){
            return response([
                'message' => 'Retrieve Product Success',
                'data' => $product
            ], 200);
        }

        return response([
            'message' => 'Product Not Found',
            'data' => null
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if(is_null($product)){
            return response([
                'message' => 'Product Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = FacadesValidator::make($updateData, [
            'nama_barang' => ['required', 'max:60', Rule::unique('products')->ignore($product)],
            'kode' => 'required',
            'harga' => 'required|numeric',
            'jumlah' => 'required|numeric'
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);
        
        $product->nama_barang = $updateData['nama_barang'];
        $product->kode = $updateData['kode'];
        $product->harga = $updateData['harga'];
        $product->jumlah = $updateData['jumlah'];
        
        if ($product->save()){
            return response([
                'message' =>'Update Product Success',
                'data' => $product
            ], 200);
        }

        return response([
            'message' =>'Update Product Failed',
            'data' => null
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if(is_null($product)){
            return response([
                'message' => 'Product Not Found',
                'data' => null
            ], 404);
        }

        if($product->delete()){
            return response([
                'message' => 'Delete Product Success',
                'data' => $product
            ], 200);
        }

        return response([
            'message' => 'Delete Product Failed',
            'data' => null
        ], 400);
    }
}
