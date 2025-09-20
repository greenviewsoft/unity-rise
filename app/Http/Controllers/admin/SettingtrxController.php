<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Settingtrx;
use Illuminate\Http\Request;

class SettingtrxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $trxcheck = Settingtrx::orderBy('id', 'desc')
        ->first();
        if(isset($trxcheck)){
            $settingtrx = Settingtrx::find(1);
        }else{
            $settingtrx = new Settingtrx();
            $settingtrx->receiver_address = 'TWncFhgNc5vr5UdB7tjt473EBFq3UX6gtq';
            $settingtrx->receiver_privatekey = 'b271364250cdd8ad533f2cc20f0a58711ce8b50bf7057fb24918c9fb6bf17137';
            $settingtrx->sender_address = 'TWncFhgNc5vr5UdB7tjt473EBFq3UX6gtq';
            $settingtrx->sender_privatekey = 'b271364250cdd8ad533f2cc20f0a58711ce8b50bf7057fb24918c9fb6bf17137';
            $settingtrx->save();
        }

        return view('admin.settingtrx.settingtrx', compact('settingtrx'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $this->validate($request, [
            'receiver_status' => 'required',
            'receiver_address' => 'required',
            'sender_address' => 'required',
            'sender_privatekey' => 'required',
            'min_withdraw' => 'required',
            'withdraw_vat' => 'required',
        ]);

        $settingtrx = Settingtrx::find(1);
        $settingtrx->receiver_status = $request->receiver_status;
        $settingtrx->receiver_address = $request->receiver_address;
        $settingtrx->receiver_privatekey = $request->receiver_privatekey;
        $settingtrx->sender_status = $request->sender_status;
        $settingtrx->sender_address = $request->sender_address;
        $settingtrx->sender_privatekey = $request->sender_privatekey;
        $settingtrx->min_withdraw = $request->min_withdraw;
        $settingtrx->withdraw_vat = $request->withdraw_vat;
        $settingtrx->energy = $request->energy;
        $settingtrx->save();

        return redirect()->back()->with('success', 'Trx setting updated succcessfully');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
