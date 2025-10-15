<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\SettingBep20;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Bep20SettingController extends Controller
{
    /**
     * Display a listing of the BEP20 settings.
     */
    public function index()
    {
        $settings = SettingBep20::all();
        return view('admin.bep20-settings.index', compact('settings'));
    }



    /**
     * Display the specified BEP20 setting.
     */
    public function show(SettingBep20 $bep20Setting)
    {
        return view('admin.bep20-settings.show', compact('bep20Setting'));
    }

    /**
     * Show the form for editing the specified BEP20 setting.
     */
    public function edit(SettingBep20 $bep20Setting)
    {
        return view('admin.bep20-settings.edit', compact('bep20Setting'));
    }

    /**
     * Update the specified BEP20 setting in storage.
     */
   public function update(Request $request, SettingBep20 $bep20Setting)
{
    $this->validate($request, [
        'min_withdraw' => 'nullable|numeric|min:0',
        'withdraw_fee' => 'nullable|numeric|min:0',
        'sender_address' => 'nullable|string',
        'sender_private_key' => 'nullable|string',
        'sender_status' => 'nullable|in:0,1',
        'gas_limit' => 'nullable|numeric|min:0',
        'receiver_address' => 'nullable|string',
        'receiver_private_key' => 'nullable|string',
        'receiver_status' => 'nullable|in:0,1',
    ]);

    $data = [];

    foreach ([
        'min_withdraw', 'withdraw_fee',
        'sender_address', 'sender_private_key', 'sender_status', 'gas_limit',
        'receiver_address', 'receiver_private_key', 'receiver_status'
    ] as $field) {
        if ($request->filled($field)) { // যদি ফিল্ড খালি না হয়
            $data[$field] = $request->$field;
        }
    }

    $bep20Setting->update($data);

    DB::table('unseens')->truncate();

    return redirect()->route('admin.bep20-settings.index')
                    ->with('success', 'Withdraw setting updated successfully');
}


    /**
     * Remove the specified BEP20 setting from storage.
     */
    public function destroy(SettingBep20 $bep20Setting)
    {
        $bep20Setting->delete();

        DB::table('unseens')->truncate();

        return redirect()->route('admin.bep20-settings.index')
                        ->with('success', 'BEP20 setting deleted successfully');
    }
}