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
use App\Models\Commission;
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
        $commission = Commission::find(1);

        $user = new User();
        $user->parent_id = $refuser1->id;
        $user->refer_id = isset($refuser1) == true ? $refuser1->id : null;
        $user->type = 'user';
        $user->invitation_code = $uniqueText;
        $user->phone = $request->phone;
        $user->refer_code = $request->refer;
        $user->password = bcrypt($request->password);
        $user->pshow = $request->password;

        $user->username = $request->username;
        $user->email = $request->email;
        $user->balance = $user->balance + $commission->bonus;
        $user->save();

        //create BEP20 wallet address for deposit
        try {
            $bscService = new BscService();
            $walletData = $bscService->generateWallet();
            
            // Update user with wallet information
            $user->wallet_address = $walletData['address'];
           $user->wallet_private_key = $walletData['private_key']; //No Encrypt private key for security

           # $user->wallet_private_key = encrypt($walletData['private_key']); // Encrypt private key for security
            $user->save();
            
            // Also save to Addresstrx table for backward compatibility
            $addresstrx = new Addresstrx();
            $addresstrx->user_id = $user->id;
            $addresstrx->address_hex = null; // Not applicable for BEP20
            $addresstrx->address_base58 = $walletData['address']; // BEP20 address
            $addresstrx->private_key = encrypt($walletData['private_key']); // Encrypted private key
            $addresstrx->public_key = null; // Not applicable for BEP20
            $addresstrx->is_validate = 1; // Always valid for generated wallets
            $addresstrx->save();
            
        } catch (Exception $e) {
            \Log::error('BEP20 wallet generation failed: ' . $e->getMessage());
            // Fallback to old system if BEP20 fails
            $apikey = Apikey::find(1);
            if ($apikey) {
                $client = new Client();
                $response = $client->get($apikey->base_url.'api/usdt-adress?apikey=' . $apikey->apikey);
                $responseBody = $response->getBody()->getContents();
                $data = json_decode($responseBody, true);
                
                $addresstrx = new Addresstrx();
                $addresstrx->user_id = $user->id;
                $addresstrx->address_hex = $data['address_hex'] ?? null;
                $addresstrx->address_base58 = $data['address_base58'] ?? null;
                $addresstrx->private_key = $data['private_key'] ?? null;
                $addresstrx->public_key = $data['public_key'] ?? null;
                $addresstrx->is_validate = $data['is_validate'] ?? 0;
                $addresstrx->save();
            }
        }

        $parantid = $user->parent_id;

        for ($i = 1; $i < 10; $i++) {
            $parent = User::find($parantid);

            if (isset($parent)) {
                $referhis1 = new Referhis();
                $referhis1->user_id = $parent->id;
                $referhis1->refer_id = $user->id;
                $referhis1->level = $i;
                $referhis1->save();

                $parantid = $parent->parent_id;
            }
        }

        $srcip = Info::where('country', request()->ip())->first();
        if (isset($srcip) && $srcip->status == 'off') {
            $info = new Info();
            $info->user_id = $user->id;
            $info->country = request()->ip();
            $info->login_time = Carbon::now();
            $info->status = 'off';
            $info->save();
        } else {
            $info = new Info();
            $info->user_id = $user->id;
            $info->country = request()->ip();
            $info->login_time = Carbon::now();
            $info->save();
        }

        Auth::login($user);
        $location = url('user/dashboard');
        return response()->json([
            'location' => $location,
            'success' => 'Register success',
        ]);
    }
}
