<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\ReferralCommissionLevel;
use App\Models\Deposite;
use App\Models\Energy;
use App\Models\History;
use App\Models\Order;
use App\Models\ReferralCommission;
use App\Models\SettingBep20;
use App\Models\Sitesetting;
use App\Models\Usdtdeposit;
use App\Models\User;
use App\Models\Withdraw;
use App\Models\Settingtrx;
use App\Services\BscService;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            $order->status = '0';
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
        // TRC20 address update deprecated
        return redirect('user/withdraw')->with('error', 'TRC20 deposits are no longer supported. Use BEP20 USDT instead.');
    }

    public function bep20AddressUpdate(Request $request)
    {
        $request->validate([
            'crypto_address' => 'required|string|min:10|max:100',
            'crypto_password' => 'required'
        ]);

        // Verify password
        if (!Hash::check($request->crypto_password, Auth::user()->password)) {
            return redirect()->back()->with('error', 'Invalid password');
        }

        // Update user's crypto address
        $user = User::find(Auth::user()->id);
        $user->crypto_address = $request->crypto_address;
        $user->save();

        return redirect('user/withdraw')->with('success', 'Crypto address updated successfully');
    }



    public function withdrawVal(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Something went wrong']);
        }

        // ✅ Step 1: Validate fields
        if (!$request->quantity) {
            return response()->json(['error' => 'Amount field required']);
        }

        if (!$request->crypto_password) {
            return response()->json(['error' => 'Withdrawal password is required']);
        }

        $user = Auth::user();

        // ✅ Step 2: Verify withdrawal (crypto) password
        if (!Hash::check($request->crypto_password, $user->crypto_password ?? '')) {
            return response()->json(['error' => 'Invalid withdrawal password']);
        }

        // ✅ Step 3: Check if user has a crypto address
        if (!$user->crypto_address) {
            return response()->json(['error' => 'Please choose your withdrawal account']);
        }

        // ✅ Step 4: Check total deposit
        $totalDeposit = Deposite::where('user_id', $user->id)->sum('amount');

        // ✅ Step 5: Load BEP20 withdrawal settings
        $settingBep20 = SettingBep20::find(1);
        if (!$settingBep20) {
            return response()->json(['error' => 'BEP20 withdrawal settings not configured']);
        }

        // ✅ Step 6: Check balance and limits
        $balance = $user->balance;
        $authBal = floor($balance - $totalDeposit);

        if ($authBal <= $settingBep20->min_withdraw) {
            return response()->json(['error' => 'Min withdraw balance is ' . $settingBep20->min_withdraw . ' USD']);
        }

        if ($authBal < $request->quantity) {
            return response()->json(['error' => "You don't have sufficient balance for withdraw"]);
        }

        if ($request->quantity < $settingBep20->min_withdraw) {
            return response()->json(['error' => 'Min withdraw amount is ' . $settingBep20->min_withdraw . ' USD']);
        }

        try {
            // ✅ Step 7: Gas limit validation
            if ($settingBep20->gas_limit < 21000) {
                return response()->json(['error' => 'Insufficient gas limit for withdrawal.']);
            }

            // ✅ Step 8: Calculate fee and net amount
            $withdrawFee = ($request->quantity * $settingBep20->withdraw_fee) / 100;
            $netAmount = $request->quantity - $withdrawFee;

            // ✅ Step 9: Create withdrawal record
            $withdraw = new Withdraw();
            $withdraw->user_id = $user->id;
            $withdraw->type = $settingBep20->sender_status ?? 'BEP20';
            $withdraw->amount = $netAmount;
            $withdraw->exact_amount = $request->quantity;
            $withdraw->txid = 'manual';
            $withdraw->txaddress = $user->crypto_address;
            $withdraw->status = '0';
            $withdraw->save();

            // ✅ Step 10: Record history
            $history = new History();
            $history->user_id = $user->id;
            $history->amount = $request->quantity;
            $history->type = 'Withdraw profit';
            $history->save();

            // ✅ Step 11: Deduct balance
            $previousBalance = $user->balance;
            $user->balance -= $request->quantity;
            $user->save();
            $newBalance = $user->balance;

            // ✅ Step 12: Log transaction
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
                    'fees' => $withdrawFee,
                    'net_amount' => $netAmount,
                    'crypto_address' => $user->crypto_address,
                    'status' => 'pending',
                    'type' => $settingBep20->sender_status ?? 'BEP20',
                    'processed_by' => 'user'
                ]
            );

            // ✅ Step 13: Return success response
            return response()->json([
                'success' => 'Withdraw submitted successfully!',
                'location' => url('user/withdraw'),
            ]);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
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

            $bep20Service = new \App\Services\Bep20DepositService();
            $result = $bep20Service->verifyManualDeposit($order);

            if ($result['success']) {
                Session::forget(['bep20_order_id', 'bep20_wallet_address', 'bep20_amount']);

                return response()->json([
                    'success' => $result['message'],
                    'amount' => $result['amount'],
                    'tx_hash' => $result['tx_hash'],
                    'new_balance' => $result['new_balance']
                ]);
            } else {
                return response()->json([
                    'error' => $result['message']
                ]);
            }

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Verification failed: ' . $e->getMessage()
            ]);
        }
    }


    // public function testBep20System()
    // {
    //     try {
    //         $bscService = new BscService();

    //         // Test wallet generation
    //         $wallet = $bscService->generateWallet();

    //         // Test USDT balance check
    //         $usdtBalance = $bscService->getUsdtBalance($wallet['address']);

    //         // Test BNB balance check
    //         $bnbBalance = $bscService->getBnbBalance($wallet['address']);

    //         // Test gas sufficiency check
    //         $hasSufficientGas = $bscService->hasSufficientGas($wallet['address']);

    //         // Test address validation
    //         $isValid = $bscService->isValidAddress($wallet['address']);

    //         // Test admin wallet configuration
    //         $adminWallet = env('BSC_ADMIN_WALLET_ADDRESS');
    //         $adminPrivateKey = env('BSC_ADMIN_PRIVATE_KEY');
    //         $adminConfigured = !empty($adminWallet) && !empty($adminPrivateKey);

    //         // Test admin wallet BNB balance if configured
    //         $adminBnbBalance = null;
    //         if ($adminConfigured) {
    //             $adminBnbBalance = $bscService->getBnbBalance($adminWallet);
    //         }

    //         return response()->json([
    //             'success' => true,
    //             'test_results' => [
    //                 'wallet_generation' => [
    //                     'address' => $wallet['address'],
    //                     'private_key_length' => strlen($wallet['private_key'])
    //                 ],
    //                 'balance_checks' => [
    //                     'usdt_balance' => $usdtBalance,
    //                     'bnb_balance' => $bnbBalance,
    //                     'has_sufficient_gas' => $hasSufficientGas
    //                 ],
    //                 'admin_wallet' => [
    //                     'configured' => $adminConfigured,
    //                     'address' => $adminWallet,
    //                     'bnb_balance' => $adminBnbBalance,
    //                     'can_send_gas' => $adminConfigured && $adminBnbBalance > 0.002
    //                 ],
    //                 'address_validation' => $isValid
    //             ],
    //             'gas_fee_system' => [
    //                 'status' => $adminConfigured ? 'Ready' : 'Not Configured',
    //                 'note' => $adminConfigured ?
    //                     'Admin wallet can automatically send BNB for gas fees' :
    //                     'Please configure BSC_ADMIN_WALLET_ADDRESS and BSC_ADMIN_PRIVATE_KEY in .env'
    //             ],
    //             'message' => 'BEP20 system with gas fee support test completed successfully'
    //         ]);

    //     } catch (Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Test failed: ' . $e->getMessage(),
    //             'error_details' => $e->getTraceAsString()
    //         ]);
    //     }
    // }

    public function submitManualDeposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'screenshot' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'transaction_hash' => 'nullable|string|max:255',
            'user_notes' => 'nullable|string|max:500'
        ]);

        try {
            // Handle screenshot upload
            $screenshotPath = null;
            if ($request->hasFile('screenshot')) {
                $screenshot = $request->file('screenshot');
                $filename = 'deposit_' . Auth::id() . '_' . time() . '.' . $screenshot->getClientOriginalExtension();

                // Create directory if it doesn't exist
                $uploadPath = public_path('uploads/deposits');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Move file to public/uploads/deposits
                $screenshot->move($uploadPath, $filename);
                $screenshotPath = 'uploads/deposits/' . $filename;
            }

            // Generate order number
            $orderNumber = 'MD' . time() . rand(1000, 9999);

            // Create manual deposit record
            $deposit = Deposite::create([
                'order_number' => $orderNumber,
                'currency' => 'USDT-BEP20',
                'user_id' => Auth::id(),
                'amount' => $request->amount,
                'txid' => 'pending_manual',
                'status' => 0, // Pending approval
                'deposit_type' => 'manual',
                'screenshot' => $screenshotPath,
                'transaction_hash' => $request->transaction_hash,
                'user_notes' => $request->user_notes,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return redirect()->back()->with('success', 'Your Deposit Successfully Submitted! Order Number: ' . $orderNumber);

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to submit deposit: ' . $e->getMessage());
        }
    }
}
