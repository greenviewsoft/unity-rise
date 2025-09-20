<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Deposite;
use App\Models\User;
use App\Models\Withdraw;
use App\Services\BscService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard(){
        $data['totaldeposite'] = Deposite::sum('amount');
        $data['totalwithdraw'] = Withdraw::sum('amount');
        $data['totaluser'] = User::where('type', 'user')
        ->count();


        $data['todaydeposite'] = Deposite::whereDate('created_at', Carbon::today())
        ->sum('amount');
        $data['todaywithdraw'] = Withdraw::whereDate('created_at', Carbon::today())
        ->sum('amount');
        $data['todayuser'] = User::where('type', 'user')
        ->whereDate('created_at', Carbon::today())
        ->count();

        $data['pendwithdraw'] = Withdraw::where('status', '0')
        ->sum('amount');
        $data['totalwithdraw'] = Withdraw::sum('amount');

        return view('admin.dashboard', $data);
    }

    public function userUsdtBalances()
    {
        $users = User::where('type', 'user')
            ->whereNotNull('wallet_address')
            ->select('id', 'name', 'email', 'wallet_address', 'balance')
            ->paginate(20);

        $bscService = new BscService();
        $totalUsdtBalance = 0;

        foreach ($users as $user) {
            try {
                $user->usdt_balance = $bscService->getUsdtBalance($user->wallet_address);
                $totalUsdtBalance += $user->usdt_balance;
            } catch (\Exception $e) {
                $user->usdt_balance = 0;
            }
        }

        return view('admin.usdt-balances', compact('users', 'totalUsdtBalance'));
    }
}
