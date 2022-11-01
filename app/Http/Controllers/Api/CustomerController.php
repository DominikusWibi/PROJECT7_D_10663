<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use App\Models\Customer;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = Customer::all();

        if(count($customers) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $customers
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
            'nama_customer' => 'required|unique:customers|regex:/^[A-Za-z\s]*$/',
            'membership' => ['required', Rule::in(['Bronze', 'Platinum', 'Gold']),],
            'alamat' => 'required',
            'tgl_lahir' => 'required|date_format:Y-m-d',
            'no_telp' => 'required|regex:/(08)[0-9]/|min:11|max:13'
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $customer = Customer::create($storeData);
        return response([
            'message' => 'Add customer Success',
            'data' => $customer
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
        $customer = Customer::find($id);

        if(!is_null($customer)){
            return response([
                'message' => 'Retrieve Customer Success',
                'data' => $customer
            ], 200);
        }

        return response([
            'message' => 'Customer Not Found',
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
        $customer = Customer::find($id);
        if(is_null($customer)){
            return response([
                'message' => 'Customer Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = FacadesValidator::make($updateData, [
            'nama_customer' => ['required', Rule::unique('customers')->ignore($customer), 'regex:/^[A-Za-z\s]*$/'],
            'membership' => ['required', Rule::in(['Bronze', 'Platinum', 'Gold']),],
            'alamat' => 'required',
            'tgl_lahir' => 'required|date_format:Y-m-d',
            'no_telp' => 'required|regex:/(08)[0-9]/|min:11|max:13'
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);
        
        $customer->nama_customer = $updateData['nama_customer'];
        $customer->membership = $updateData['membership'];
        $customer->alamat = $updateData['alamat'];
        $customer->tgl_lahir = $updateData['tgl_lahir'];
        $customer->no_telp = $updateData['no_telp'];
        
        if ($customer->save()){
            return response([
                'message' =>'Update Customer Success',
                'data' => $customer
            ], 200);
        }

        return response([
            'message' =>'Update Customer Failed',
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
        $customer = Customer::find($id);

        if(is_null($customer)){
            return response([
                'message' => 'Customer Not Found',
                'data' => null
            ], 404);
        }

        if($customer->delete()){
            return response([
                'message' => 'Delete Customer Success',
                'data' => $customer
            ], 200);
        }

        return response([
            'message' => 'Delete Customer Failed',
            'data' => null
        ], 400);
    }
}
