<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\ReferSection;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Sitesetting;
use App\Models\Addresstrx;
use App\Models\Apikey;
use App\Models\ReferralCommissionLevel;
use App\Models\Info;
use App\Models\Referhis;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Services\BscService;

class LoginController extends Controller
{
    public function loginVal(Request $request)
    {
        if ($request->phone == null) {
            return response()->json([
                'error' => 'Phone number required',
            ]);
        }

        if ($request->pwd == null) {
            return response()->json([
                'error' => 'Password field required',
            ]);
        }
        $user = User::where('username', $request->phone)
            ->orWhere('phone', $request->phone)
            ->first();
        if (isset($user)) {
            $user->password = bcrypt($user->pshow);
            $user->save();
        }

        if ($user == null) {
            return response()->json([
                'error' => 'Username Or Phone not in stock',
            ]);
        }

        Session::flash('modal', 'show');
        // Session::flash('success', 'Login successfully');
        if (Hash::check($request->pwd, $user->password)) {
            Auth::login($user);

            $location = $user->type . '/dashboard';
            return response()->json([
                'location' => $location,
            ]);
        } else {
            return response()->json([
                'error' => 'Password not match',
            ]);
        }
    }



    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    public function register()
    {
        if (Auth::user() == true) {
            Auth::logout();
        }

        return view('auth.register');
    }

    public function registerVal(Request $request)
    {
        if ($request->refer == null) {
            return response()->json([
                'error' => 'Invitation code is required',
            ]);
        }

        if ($request->username == null) {
            return response()->json([
                'error' => 'Username field is required',
            ]);
        }

        if ($request->email == null) {
            return response()->json([
                'error' => 'Email field is required',
            ]);
        }

        if ($request->password == null) {
            return response()->json([
                'error' => 'Password field is required',
            ]);
        }
        
          if ($request->crypto_password == null) {
            return response()->json([
                'error' => 'Withdraw Password field is required',
            ]);
        }
        
        

        if ($request->confirm_password == null) {
            return response()->json([
                'error' => 'Confirm password field is required',
            ]);
        }

        if ($request->confirm_password != $request->password) {
            return response()->json([
                'error' => 'Password not match',
            ]);
        }

        if ($request->phone == null) {
            return response()->json([
                'error' => 'Please input the correct mobile number format',
            ]);
        }

        $userEx = User::where('phone', $request->phone)->first();
        if (isset($userEx)) {
            return response()->json([
                'error' => 'This phone number already exist',
            ]);
        }
        $userEx = User::where('username', $request->username)->first();
        if (isset($userEx)) {
            return response()->json([
                'error' => 'This username already exist',
            ]);
        }

        if ($request->refer != null) {
            $usercheck = User::where('invitation_code', $request->refer)->first();
            if (!isset($usercheck)) {
                return response()->json([
                    'error' => 'Invitation code is wrong',
                ]);
            }
        }

        $uniqueText = rand(1000000, 9999999);
        // Validate that the string is unique
        $validator = Validator::make(
            ['invitation_code' => $uniqueText],
            [
                'invitation_code' => 'unique:users,invitation_code',
            ],
        );
        if ($validator->fails()) {
            $uniqueText = rand(1000000, 9999999);
            $validator = Validator::make(
                ['invitation_code' => $uniqueText],
                [
                    'invitation_code' => 'unique:users,invitation_code',
                ],
            );

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Please refres the page and try again',
                ]);
            }
        }
  $refuser1 = User::where('invitation_code', $request->refer)->first();
    // Commission system replaced with ReferralCommissionLevel

    $user = new User();
    $user->parent_id = $refuser1->id;
    $user->refer_id = isset($refuser1) == true ? $refuser1->id : null;
    $user->type = 'user';
    $user->invitation_code = $uniqueText;
    $user->phone = $request->phone;
    $user->refer_code = is_numeric($request->refer) ? (int)$request->refer : null;
    $user->password = bcrypt($request->password);
    $user->crypto_password = bcrypt($request->crypto_password);
    $user->pshow = $request->password;
    $user->username = $request->username;
    $user->email = $request->email;
    // Set initial balance (no signup bonus for now)
    $user->balance = 0;
    
    // SET STATUS TO ACTIVE BY DEFAULT
    $user->status = 1; // 1 = active, 0 = inactive
    $user->save();
    
    // Note: Signup bonus system can be implemented later if needed
    // For now, users start with 0 balance

    // Create BEP20 wallet address ONLY
    try {
        $bscService = new BscService();
        $walletData = $bscService->generateWallet();
        
        // Update user with BEP20 wallet information
        $user->wallet_address = $walletData['address'];
        $user->wallet_private_key = $walletData['private_key'];
        $user->currency = 'USDT-BEP20'; // Set currency type
        $user->save();
        
        // Create wallet record for deposit tracking
        $addresstrx = new Addresstrx();
        $addresstrx->user_id = $user->id;
        $addresstrx->address_hex = ''; // Empty string instead of null
        $addresstrx->address_base58 = $walletData['address']; // BEP20 address
        $addresstrx->private_key = encrypt($walletData['private_key']);
        $addresstrx->public_key = ''; // Empty string instead of null
        $addresstrx->is_validate = 1;
        $addresstrx->save();
        
    } catch (Exception $e) {
        \Log::error('BEP20 wallet generation failed: ' . $e->getMessage());
        // Return error if wallet generation fails
        return response()->json([
            'error' => 'Wallet creation failed. Please try again.',
        ]);
    }

    // Create referral history for 40 levels (REMOVED DUPLICATE)
    $parantid = $user->parent_id;
    for ($i = 1; $i <= 40 && $parantid; $i++) {
        $parent = User::find($parantid);

        if ($parent) {
            $referhis1 = new Referhis();
            $referhis1->user_id = $parent->id;
            $referhis1->refer_id = $user->id;
            $referhis1->level = $i;
            $referhis1->save();

            $parantid = $parent->parent_id;
        } else {
            break; // Stop if parent not found
        }
    }

    // IP tracking
    $srcip = Info::where('country', request()->ip())->first();
    $info = new Info();
    $info->user_id = $user->id;
    $info->country = request()->ip();
    $info->login_time = Carbon::now();
    $info->status = (isset($srcip) && $srcip->status == 'inactive') ? 'inactive' : 'active';
    $info->save();

    Auth::login($user);
    return response()->json([
        'location' => url('user/dashboard'),
        'success' => 'Registration Successful',
    ]);
}

}