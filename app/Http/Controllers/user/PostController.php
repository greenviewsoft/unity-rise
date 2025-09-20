<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Addresstrx;
use App\Models\Apikey;
use App\Models\Commission;
use App\Models\Deposite;
use App\Models\Energy;
use App\Models\History;
use App\Models\Order;
use App\Models\ReferralCommission;
use App\Models\Settingtrx;
use App\Models\Sitesetting;
use App\Models\Usdtdeposit;
use App\Models\User;
use App\Models\Withdraw;
use App\Services\BscService;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Exception;

class PostController extends Controller
{
    public function passwordUpdate(Request $request)
    {
        if ($request->old_password == null) {
            return response()->json([
                'error' => 'Old password required',
            ]);
        }

        if ($request->password == null) {
            return response()->json([
                'error' => 'New password required',
            ]);
        }

        if ($request->password != $request->password_confirmation) {
            return response()->json([
                'error' => 'Confirm password not match',
            ]);
        }

        $hashedPassword = Auth::user()->password;
        if (Hash::check($request->old_password, $hashedPassword)) {
            if (!Hash::check($request->password, $hashedPassword)) {
                $user = User::find(Auth::id());
                $user->password = bcrypt($request->password);
                $user->pshow = $request->password;
                $user->save();

                Auth::logout();

                $location = url('/');
                return response()->json([
                    'location' => $location,
                ]);
            } else {
                return response()->json([
                    'error' => 'New password cannot be the same as old password',
                ]);
            }
        } else {
            return response()->json([
                'error' => 'Current password not match',
            ]);
        }
    }

    public function depositeAddress(Request $request)
    {
        // Only handle BEP20 USDT deposits
        return $this->processBep20UsdtDeposit($request);
    }



    /**
     * Process BEP20 USDT deposit
     */
    private function processBep20UsdtDeposit(Request $request)
    {
        try {
            $user = User::find(Auth::user()->id);
            $bscService = new BscService();
            
            // Generate wallet if user doesn't have one
            if (!$user->wallet_address) {
                $wallet = $bscService->generateWallet();
                $user->wallet_address = $wallet['address'];
                $user->wallet_private_key = encrypt($wallet['private_key']);
                $user->save();
            }
            
            // Create deposit record
            $date = Carbon::now()->format('YmdHis');
            $randomNumber = str_pad(mt_rand(1, 99999), 3, '0', STR_PAD_LEFT);
            $orderNumber = 'BEP' . $date . $randomNumber;
            
            $order = new Order();
            $order->user_id = Auth::user()->id;
            $order->method = 'BEP20 USDT';
            $order->order_number = $orderNumber;
            $order->amount = $request->amount;
            $order->currency = 'USDT-BEP20';
            $order->wallet_address = $user->wallet_address;
            $order->status = 'pending';
            $order->save();
            
            Session::put('bep20_order_id', $order->id);
            Session::put('bep20_wallet_address', $user->wallet_address);
            Session::put('bep20_amount', $request->amount);
            
            $user->currency = $request->currency;
            $user->save();
            
            return redirect('user/bep20-payment');
            
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to process BEP20 deposit: ' . $e->getMessage()
            ]);
        }
    }
    
    public function depositeInformation(Request $request)
    {
        // Only handle BEP20 USDT verification
        return $this->verifyBep20Deposit($request);
    }

    public function trcUpdate(Request $request)
    {
        if(Auth::user()->crypto_address != null){
            return redirect()->back();
        }
        $this->validate($request, [
            'crypto_address' => 'required',
            'password' => 'required',
        ]);

        if ($request->password != Auth::user()->pshow) {
            return redirect()
                ->back()
                ->with('error', 'Password not match');
        }

        $user = User::find(Auth::user()->id);
        $user->crypto_address = $request->crypto_address;
        $user->save();

        return redirect('user/withdraw')->with('success', 'Crypt trc20 address set successfully');
    }

    public function withdrawVal(Request $request)
    {
        if (!$request->ajax()) {
            return 'Something wrong';
        }

        //quantity field
        if ($request->quantity == null) {
            return response()->json([
                'error' => 'Amount field required',
            ]);
        }

        //check trx field is empty or not
        if (Auth::user()->crypto_address == null) {
            return response()->json([
                'error' => 'Please chose your withdraw account',
            ]);
        }

        //check today's withdraw have or not
        $withdrawtoday = Withdraw::whereDate('created_at', Carbon::today())
            ->where('user_id', Auth::user()->id)
            ->first();
        if (isset($withdrawtoday)) {
            return response()->json([
                'error' => 'Today withdraw complete',
            ]);
        }

        //check energy
        $checkenergy = Energy::where('user_id', Auth::user()->id)
            ->where('status', '0')
            ->first();
        if (isset($checkenergy)) {
            return response()->json([
                'error' => 'Energry low, please deposite '.$checkenergy->energy_amount,
            ]);
        }

        //check total deposit
        $toaldeposit = Deposite::where('user_id', Auth::user()->id)
        ->sum('amount');

        //check minimu withdraw
        $commssion = Commission::find(1);
        $settingtrx = Settingtrx::find(1);
        $balance = Auth::user()->balance;
        $pattern = '/\.\d+/';
        $authbal = (preg_replace($pattern, '', $balance) - $toaldeposit) - $commssion->bonus;


        if ($authbal > $settingtrx->min_withdraw) {
            
        } else {
            return response()->json([
                'error' => 'Min withdraw balance is ' . ($settingtrx->min_withdraw + 1) . ' usd',
            ]);
        }
        if ($authbal < $request->quantity) {
            return response()->json([
                'error' => "You don't have the balance for withdraw",
            ]);
        }
        if ($settingtrx->min_withdraw >= $request->quantity) {
            return response()->json([
                'error' => 'Min withdraw balance is ' . ($settingtrx->min_withdraw + 1) . ' usd',
            ]);
        }

        try {
            $settingtrx = Settingtrx::find(1);

            $percentage = ($request->quantity/100) * $settingtrx->withdraw_vat;
            $amount = $percentage + $settingtrx->withdraw_vat;

            //make withdraw history
            $withdraw = new Withdraw();
            $withdraw->user_id = Auth::user()->id;
            $withdraw->type = $settingtrx->sender_status;
            $withdraw->amount = ($request->quantity - $amount);
            $withdraw->exact_amount = $request->quantity;
            // $withdraw->txid = isset($transfer) == true ? $transfer['txid'] : 'demo';
            $withdraw->txid = 'demo';
            $withdraw->txid = 'manual';
            $withdraw->txaddress = Auth::user()->crypto_address;
            $withdraw->status = '0';
            $withdraw->save();

            //save history
            $history = new History();
            $history->user_id = Auth::user()->id;
            $history->amount = $request->quantity;
            $history->type = 'Withdraw profit';
            $history->save();

            //if from commission then make commission null
            $user = User::find(Auth::user()->id);
            $previousBalance = $user->balance;
            $user->balance = $user->balance - $request->quantity;
            $user->save();
            $newBalance = $user->balance;
            
            // Create transaction log
             \App\Models\Log::createTransactionLog(
                 $user->id,
                 'withdrawal',
                 $request->quantity,
                 $previousBalance,
                 $newBalance,
                 'App\\Models\\Withdraw',
                 $withdraw->id,
                 "User withdrawal request - Amount: {$request->quantity} USDT",
                 [
                     'withdrawal_amount' => $request->quantity,
                     'exact_amount' => $request->quantity,
                     'fees' => $amount,
                     'net_amount' => ($request->quantity - $amount),
                     'crypto_address' => Auth::user()->crypto_address,
                     'status' => 'pending',
                     'type' => $settingtrx->sender_status,
                     'processed_by' => 'user'
                 ]
             );

            Session::flash('success', 'Withdraw successfully');
            return response()->json([
                'success' => 'Withdraw successfully',
                'location' => url('user/withdraw'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ]);
        }
    }
    
    /**
     * Verify BEP20 USDT deposit
     */
    public function verifyBep20Deposit(Request $request)
    {
        try {
            $orderId = Session::get('bep20_order_id');
            $order = Order::find($orderId);
            
            if (!$order) {
                return response()->json(['error' => 'Order not found']);
            }
            
            $bscService = new BscService();
            $user = User::find(Auth::user()->id);
            
            // Check USDT balance in user's wallet
            $usdtBalance = $bscService->getUsdtBalance($user->wallet_address);
            
            if (bccomp($usdtBalance, $order->amount, 6) >= 0) {
                // Sufficient balance found, transfer to admin wallet
                $privateKey = decrypt($user->wallet_private_key);
                $transferResult = $bscService->transferUsdtToAdmin(
                    $user->wallet_address,
                    $privateKey,
                    $order->amount
                );
                
                if ($transferResult['success']) {
                    // Update user balance
                    $user->balance += floatval($order->amount);
                    $user->save();
                    
                    // Update order status
                    $order->status = 'completed';
                    $order->tx_hash = $transferResult['tx_hash'];
                    $order->save();
                    
                    // Create history record
                    $history = new History();
                    $history->user_id = $user->id;
                    $history->amount = $order->amount;
                    $history->type = 'Deposit';
                    $history->method = 'BEP20 USDT';
                    $history->status = 'Success';
                    $history->tx_hash = $transferResult['tx_hash'];
                    $history->save();
                    
                    Session::forget(['bep20_order_id', 'bep20_wallet_address', 'bep20_amount']);
                    
                    return response()->json([
                        'success' => 'Deposit verified and processed successfully',
                        'amount' => $order->amount,
                        'tx_hash' => $transferResult['tx_hash']
                    ]);
                } else {
                    return response()->json([
                        'error' => 'Transfer failed: ' . $transferResult['error']
                    ]);
                }
            } else {
                return response()->json([
                    'error' => 'Insufficient USDT balance. Required: ' . $order->amount . ', Available: ' . $usdtBalance
                ]);
            }
            
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Verification failed: ' . $e->getMessage()
            ]);
        }
    }

    public function testBep20System()
    {
        try {
            $bscService = new BscService();
            
            // Test wallet generation
            $wallet = $bscService->generateWallet();
            
            // Test USDT balance check
            $balance = $bscService->getUsdtBalance($wallet['address']);
            
            // Test address validation
            $isValid = $bscService->isValidAddress($wallet['address']);
            
            return response()->json([
                'success' => true,
                'wallet' => $wallet,
                'balance' => $balance,
                'is_valid_address' => $isValid,
                'message' => 'BEP20 system test completed successfully'
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage()
            ]);
        }
    }
}
