<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Addresstrx;
use App\Models\Apikey;
use App\Models\Deposite;
use App\Models\Order;
use App\Models\Settingtrx;
use App\Models\Sitesetting;
use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Exception;
use GuzzleHttp\Client;

class TransactionController extends Controller
{
    public function withdraw(Request $request){


        if($request->key != null || $request->from != null || $request->to != null || $request->status != null){

            $key = $request->key;
            $from = $request->from;
            $to = $request->to;
            $status = $request->status;

            $query = Withdraw::query();
            if (isset($key))
            {
                $query->where(function($q) use ($key) {
                    $q->orWhere('txid', $key);
                });
            }
            if (isset($from) && isset($to))
            {
                $query->where(function($q) use ($from, $to) {
                    $q->WhereBetween('created_at', [$from, $to]);
                });
            }
            if (isset($status))
            {
                $query->where(function($q) use ($status) {
                    $q->orWhere('type', $status);
                });
            }
            $withdraws = $query->paginate(15);
        }else{
            $withdraws = Withdraw::orderBy('id', 'desc')
            ->paginate(15);
        }


        return view('admin.transaction.withdraw', compact('withdraws'));
    }

    public function pendingWithdraw(){
        $withdraws = Withdraw::orderBy('id', 'desc')
        ->where('status', '0')
        ->paginate(15);
        return view('admin.transaction.withdraw', compact('withdraws'));
    }

    public function failedWithdraw(){
        $withdraws = Withdraw::orderBy('id', 'desc')
        ->where('status', '2')
        ->paginate(15);
        return view('admin.transaction.withdraw', compact('withdraws'));
    }

    public function withdrawDetails($id){
        $withdraw = Withdraw::find($id);
        $user = User::find($withdraw->user_id);

        $client = new Client();
        $response = $client->get('https://apilist.tronscanapi.com/api/transaction-info?hash='.$withdraw->txid);
        $responseBody = $response->getBody()->getContents();
        $status = $response->getStatusCode();
        $data = json_decode($responseBody);

        return view('admin.transaction.withdraw_details', compact('withdraw', 'user', 'data'));
    }

    public function withdrawStore($status, $id){

        $withdraw = Withdraw::find($id);

        if($status == '2'){
            $withdraw->status = '2';
            $withdraw->save();

            return redirect()->back()->with('error', 'Witdraw rejected');
        }
        $withdraw->status = '1';
        $withdraw->save();
        return redirect()->back()->with('success', 'Witdraw successful');

        $settingtrx = Settingtrx::find(1);
        $apikey = Apikey::find(1);
        $sitesetting = Sitesetting::find(1);

        try {
            //now transfer the balance to main admin account
            $apikey = $apikey->apikey;
            $sender_address = $settingtrx->sender_address;
            $sender_privatekey = $settingtrx->sender_privatekey;
            $receiver_address = $withdraw->txaddress;
            $amount = $withdraw->amount;

            // //transfer balance to user if live
            if($sitesetting->development == 'false'){
                $client = new Client();
                $response = $client->post('https://tronapi.pro/api/usdt-transfer?address='.$receiver_address.'&amount='.$amount.'&apikey='.$apikey.'&sender_privatekey='.$sender_privatekey);
                $responseBody = $response->getBody()->getContents();
                $status = $response->getStatusCode();
                $data = json_decode($responseBody);
                if(isset($data->data)){
                    $txid = $data->data->txid;
                }else{
                    return redirect()->back()->with('error', $data->error);
                }

            }else{
                $txid = 'manual';
            }

            $withdraw->txid = $txid;
            $withdraw->status = '1';
            $withdraw->save();

            return redirect()->back()->with('success', 'Witdraw successful');

        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }


    }



    public function deposite(Request $request){
        $query = Deposite::query();
        
        // Search by key (order number or txid)
        if ($request->filled('key')) {
            $key = $request->key;
            $query->where(function($q) use ($key) {
                $q->where('order_number', 'like', "%{$key}%")
                  ->orWhere('txid', 'like', "%{$key}%");
            });
        }
        
        // Filter by date range
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('created_at', [
                $request->from . ' 00:00:00',
                $request->to . ' 23:59:59'
            ]);
        } elseif ($request->filled('from')) {
            $query->where('created_at', '>=', $request->from . ' 00:00:00');
        } elseif ($request->filled('to')) {
            $query->where('created_at', '<=', $request->to . ' 23:59:59');
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by currency
        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
        }
        
        $deposites = $query->orderBy('id', 'desc')->paginate(15);
        
        return view('admin.transaction.deposite', compact('deposites'));
    }

    public function addDeposite(){
        return view('admin.transaction.add_deposite');
    }
    
public function depositeStore(Request $request){
    $this->validate($request, [
        'phone' => 'required',
        'amount' => 'required',
    ]);

    $user = User::where('phone', $request->phone)->first();

    if(!isset($user)){
        return redirect()->back()->with('error', 'This account not found');
    }

    // Generate unique order number
    $orderNumber = 'DEP' . date('Ymd') . rand(1000, 9999);

    $deposite = new Deposite();
    $deposite->user_id = $user->id;
    $deposite->amount = $request->amount;
    $deposite->txid = 'demo';
    $deposite->order_number = $orderNumber; // Add this line
    $deposite->status = '1';
    $deposite->save();
        // Update user balance and create transaction log
        $previousBalance = $user->balance;
        $user->increment('balance', $request->amount);
        $newBalance = $user->fresh()->balance;
        
        // Create transaction log
        \App\Models\Log::createTransactionLog(
            $user->id,
            'deposit',
            $request->amount,
            $previousBalance,
            $newBalance,
            'App\\Models\\Deposite',
            $deposite->id,
            "Manual admin deposit for user: {$user->phone}",
            [
                'phone' => $user->phone,
                'txid' => 'demo',
                'payment_method' => 'manual_admin',
                'admin_action' => true,
                 'currency' => 'USDT'
            ]
        );

        return redirect()->back()->with('success', 'Deposite created successfully');
    }


    public function depositDetails($id){
        $deposite = Deposite::find($id);
        
        if (!$deposite) {
            return redirect()->back()->with('error', 'Deposit not found');
        }
        
        $order = null;
        $addresstrx = null;
        $user = null;
        
        // Get order if exists
        if ($deposite->order_number) {
            $order = Order::where('order_number', $deposite->order_number)->first();
            
            // Get address if order exists and has txid
            if ($order && $order->txid) {
                $addresstrx = Addresstrx::where('id', $order->txid)->first();
            }
        }
        
        // Get user if exists
        if ($deposite->user_id) {
            $user = User::find($deposite->user_id);
        }

        return view('admin.transaction.deposit_details', compact('deposite', 'order', 'addresstrx', 'user'));
    }

    public function depositDelete($id){
        $deposite = Deposite::find($id);
        
        if (!$deposite) {
            return redirect()->back()->with('error', 'Deposit not found');
        }
        
        $order = null;
        $addresstrx = null;
        
        // Get order if exists
        if ($deposite->order_number) {
            $order = Order::where('order_number', $deposite->order_number)->first();
            
            // Get address if order exists and has txid
            if ($order && $order->txid) {
                $addresstrx = Addresstrx::where('id', $order->txid)->first();
            }
        }

        // Delete in reverse order to avoid foreign key constraints
        if ($addresstrx) {
            $addresstrx->delete();
        }
        if ($order) {
            $order->delete();
        }
        if ($deposite) {
            $deposite->delete();
        }

        return redirect()->back()->with('success', 'Deposit deleted successfully');
    }
}
