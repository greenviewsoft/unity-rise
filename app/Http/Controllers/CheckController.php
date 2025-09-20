<?php

namespace App\Http\Controllers;


use Illuminate\Console\Command;
use App\Models\Addresstrx;

use App\Models\Apikey;
use App\Models\Commission;
use App\Models\Deposite;
use App\Models\Energy;
use App\Models\Grab;
use App\Models\History;
use App\Models\Ipadmin;
use App\Models\Order;
use App\Models\Refercommission;
use App\Models\Settingtrx;
use App\Models\Spinamount;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Auth;
use phpseclib\Math\BigInteger;
use phpseclib\Net\Base58;

class CheckController extends Controller
{
    public function check()
    {
        return  $ip = request()->ip();
    }



    public function checkIp(){
        return Ipadmin::orderBy('id', 'desc')
        ->get();
    }
}
