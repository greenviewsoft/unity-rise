<?php

namespace App\Http\Middleware;

use App\Models\Ipadmin;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->type == 'admin') {


            $ip = request()->ip();

            $ckip = Ipadmin::where('ip', $ip)
            ->first();
            if(!isset($ckip)){
                $newip = new Ipadmin();
                $newip->ip = $ip;
                $newip->save();
            }
            
            return $next($request);
        } elseif (Auth::check() && Auth::user()->type == 'user') {
            if (Auth::user()->info == 'off') {
                Auth::logout();
                return redirect('/');
            }
            return redirect('user/dashboard');
        } else {
            return redirect()->url('/');
        }
    }
}
