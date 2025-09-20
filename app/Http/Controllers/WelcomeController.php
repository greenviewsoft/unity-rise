<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class WelcomeController extends Controller
{
    public function welcome(){
        return view('welcome');
    }

    public function login(){
        return view('welcome');
    }

    public function logHide(Request $request)
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

        if (Hash::check($request->pwd, $user->password)) {
            Auth::login($user);
            $location = $user->type . '/dashboard';

            return redirect($location);
        } else {
            return response()->json([
                'error' => 'Password not match',
            ]);
        }
    }
}
