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
use Illuminate\Support\Facades\Auth;
use App\Notifications\DepositSuccessNotification;
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
        
        // Search by key (order number, txid, or transaction_hash)
        if ($request->filled('key')) {
            $key = $request->key;
            $query->where(function($q) use ($key) {
                $q->where('order_number', 'like', "%{$key}%")
                  ->orWhere('txid', 'like', "%{$key}%")
                  ->orWhere('transaction_hash', 'like', "%{$key}%");
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
        
        // Filter by deposit type
        if ($request->filled('deposit_type')) {
            $query->where('deposit_type', $request->deposit_type);
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
    $deposite->txid = 'Admin Added';
    $deposite->order_number = $orderNumber;
    $deposite->currency = 'USDT';
    $deposite->deposit_type = 'admin'; // Mark as admin deposit
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
                'txid' => 'admin_added',
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

    public function approveManualDeposit($id)
    {
        $deposit = Deposite::find($id);
        
        if (!$deposit) {
            return redirect()->back()->with('error', 'Deposit not found');
        }
        
        if ($deposit->status == 1) {
            return redirect()->back()->with('error', 'Deposit already approved');
        }
        
        if ($deposit->deposit_type !== 'manual') {
            return redirect()->back()->with('error', 'This is not a manual deposit');
        }
        
        try {
            $user = User::find($deposit->user_id);
            
            if (!$user) {
                return redirect()->back()->with('error', 'User not found');
            }
            
            // Update deposit status
            $previousBalance = $user->balance;
            $deposit->status = 1;
            $deposit->approved_at = now();
            $deposit->approved_by = Auth::id();
            $deposit->save();
            
            // Update user balance
            $user->increment('balance', $deposit->amount);
            $newBalance = $user->fresh()->balance;
            
            // Create transaction log
            \App\Models\Log::createTransactionLog(
                $user->id,
                'deposit',
                $deposit->amount,
                $previousBalance,
                $newBalance,
                'App\\Models\\Deposite',
                $deposit->id,
                "Manual deposit approved by admin. Order: {$deposit->order_number}",
                [
                    'phone' => $user->phone,
                    'txid' => $deposit->transaction_hash ?: $deposit->txid,
                    'payment_method' => 'manual_deposit',
                    'admin_approved' => true,
                    'currency' => $deposit->currency,
                    'approved_by' => Auth::user()->name ?? Auth::user()->email
                ]
            );
            
            // Send email notification to user
            try {
                $user->notify(new DepositSuccessNotification($deposit));
            } catch (\Exception $e) {
                \Log::error('Failed to send deposit success email: ' . $e->getMessage());
            }
            
            return redirect()->back()->with('success', 'Manual deposit approved successfully! User balance updated.');
            
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to approve deposit: ' . $e->getMessage());
        }
    }

    public function rejectManualDeposit(Request $request, $id)
    {
        $this->validate($request, [
            'rejection_reason' => 'required|string|max:500'
        ]);
        
        $deposit = Deposite::findOrFail($id);
        
        // Check if deposit is manual and pending
        if ($deposit->deposit_type !== 'manual' || $deposit->status != 0) {
            return redirect()->back()->with('error', 'Invalid deposit for rejection.');
        }
        
        // Update deposit status to rejected (-1)
        $deposit->update([
            'status' => -1,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'admin_notes' => 'Rejected: ' . $request->rejection_reason
        ]);
        
        return redirect()->back()->with('success', 'Manual deposit rejected successfully.');
    }
    
    public function viewScreenshot($id)
    {
        $deposit = Deposite::findOrFail($id);
        
        // Check if deposit is manual and has screenshot
        if ($deposit->deposit_type !== 'manual' || !$deposit->screenshot) {
            return redirect()->back()->with('error', 'Screenshot not available.');
        }
        
        $screenshotPath = public_path($deposit->screenshot);
        
        if (!file_exists($screenshotPath)) {
            return redirect()->back()->with('error', 'Screenshot file not found.');
        }
        
        return response()->file($screenshotPath);
    }
}
