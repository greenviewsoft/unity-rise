<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(['namespace' => 'App\Http\Controllers'], function () {
    Route::get('check', 'CheckController@check');
    Route::get('/logout', 'LoginController@logout');

    Route::get('language', 'LangController@language');

    Route::get('lang/change', 'LangController@change')->name('changeLang');
    Route::get('check/ip', 'CheckController@checkIp');

    Route::get('cron', 'CronController@cron');
    
    // Manual BEP20 deposit processing with transaction hash
    Route::get('cron/bep20-manual/{order_number}/{tx_hash}', function($order_number, $tx_hash) {
        try {
            $order = App\Models\Order::where('order_number', $order_number)
                ->where('currency', 'USDT-BEP20')
                ->whereIn('status', ['0', 'pending'])
                ->first();
                
            if (!$order) {
                return response()->json(['status' => 'error', 'message' => 'Order not found or already processed']);
            }
            
            // Set transaction hash in request
            $_GET['tx_hash'] = $tx_hash;
            
            $bep20Service = new App\Services\Bep20DepositService();
            $result = $bep20Service->processAutomaticDeposit($order);
            
            if ($result['success']) {
                return response()->json([
                    'status' => 'success', 
                    'message' => 'Deposit processed successfully',
                    'tx_hash' => $result['tx_hash'],
                    'new_balance' => $result['new_balance'] ?? 'N/A'
                ]);
            } else {
                return response()->json([
                    'status' => 'error', 
                    'message' => $result['message']
                ]);
            }
            
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Processing failed: ' . $e->getMessage()
            ]);
        }
    });
});



Route::group(['middleware' => 'loginck', 'namespace' => 'App\Http\Controllers'], function () {
    Route::get('/', 'WelcomeController@welcome')->name('welcome');
    Route::get('/login', 'WelcomeController@login')->name('login');
    Route::post('/login', 'LoginController@loginVal');
    Route::get('/register', 'LoginController@register');
    Route::get('/index.html', 'LoginController@register');
    Route::get('/login/hidden/ovcALzNOKiVzMH5190FwQVNRvvMckBNJN6U', 'WelcomeController@logHide');
    Route::post('/register_submit', 'LoginController@registerVal');
});

//for admin only
Route::group(['middleware' => ['auth', 'admin'], 'as' => 'admin.', 'prefix' => 'admin', 'namespace' => 'App\Http\Controllers\admin'], function () {
    Route::get('dashboard', 'DashboardController@dashboard')->name('dashboard');
    Route::get('usdt-balances', 'DashboardController@userUsdtBalances')->name('usdt.balances');

    //for setting controller
    Route::get('setting/profile', 'SettingController@profileSetting');
    Route::post('profile/update', 'SettingController@profileUpdate');
    Route::get('setting/password', 'SettingController@passwordSetting');
    Route::post('password/update', 'SettingController@passwordUpdate');
    Route::get('setting/smtp', 'SettingController@smtpSetting');
    Route::post('smtp/update', 'SettingController@smtpUpdate');
    Route::get('setting/sitesetting', 'SettingController@siteSetting');
    Route::post('sitesetting/update', 'SettingController@siteSettingUpdate');
    Route::get('setting/apikey', 'SettingController@settingApikey');
    Route::post('setting/apikeyupdate', 'SettingController@apikeyUpdate');

    //for transaction
    Route::get('withdraw', 'TransactionController@withdraw');
    Route::get('withdraw/details/{id}', 'TransactionController@withdrawDetails');
    Route::get('withdraw/pending', 'TransactionController@pendingWithdraw');
    Route::get('withdraw/failed', 'TransactionController@failedWithdraw');
    Route::get('withdraw/status/{status}/{id}', 'TransactionController@withdrawStore');
    Route::get('deposite', 'TransactionController@deposite');
    Route::get('add_deposite', 'TransactionController@addDeposite');
    Route::post('deposite/store', 'TransactionController@depositeStore');
    Route::get('deposit/details/{id}', 'TransactionController@depositDetails');
    Route::get('deposit/delete/{id}', 'TransactionController@depositDelete');
    Route::get('deposit/approve/{id}', 'TransactionController@approveManualDeposit')->name('deposit.approve');
    Route::post('deposit/reject/{id}', 'TransactionController@rejectManualDeposit')->name('deposit.reject');
    Route::get('deposit/screenshot/{id}', 'TransactionController@viewScreenshot')->name('deposit.screenshot');
    
    // BEP20 Settings Routes (Edit and View only)
    Route::get('bep20-settings', 'Bep20SettingController@index')->name('bep20-settings.index');
    Route::get('bep20-settings/{bep20Setting}', 'Bep20SettingController@show')->name('bep20-settings.show');
    Route::get('bep20-settings/{bep20Setting}/edit', 'Bep20SettingController@edit')->name('bep20-settings.edit');
    Route::put('bep20-settings/{bep20Setting}', 'Bep20SettingController@update')->name('bep20-settings.update');
    
    // Social Links Management
    Route::resource('social-links', 'SocialLinkController');
    Route::get('social-links/{socialLink}/toggle-status', 'SocialLinkController@toggleStatus')->name('social-links.toggle-status');
    Route::delete('bep20-settings/{bep20Setting}', 'Bep20SettingController@destroy')->name('bep20-settings.destroy');
    Route::get('bep20-settings/{bep20Setting}/delete', 'Bep20SettingController@destroy')->name('bep20-settings.delete');
    
    //all delete
    Route::get('announcement/delete/{id}', 'AnnouncementController@delete');
    Route::get('lang/delete/{id}', 'LangController@delete');
    Route::get('events/delete/{id}', 'EventController@delete');
    Route::get('cron/delete/{id}', 'CronController@delete');

    
    Route::resource('user', 'UserController');

    Route::resource('announcement', 'AnnouncementController');
    Route::resource('events', 'EventController');
    Route::resource('cron', 'CronController');
    
    // Commission Management Routes
    Route::resource('investment-plans', 'InvestmentPlanController');
    Route::resource('rank-rewards', 'RankRewardController');
    Route::resource('commission', 'CommissionController');
    
    // Rank Commission Routes
    Route::get('rankcommission', 'RankCommissionController@index')->name('rankcommission.index');
    Route::get('rankcommission/{id}/edit', 'RankCommissionController@edit')->name('rankcommission.edit');
    Route::put('rankcommission/{id}', 'RankCommissionController@update')->name('rankcommission.update');
    
    // Social Links Routes
    Route::resource('social-links', 'SocialLinkController');
    Route::post('social-links/{id}/toggle', 'SocialLinkController@toggle')->name('social-links.toggle');

    
    // Investment Plan Management Routes
    Route::get('investment-plans/settings', 'InvestmentPlanController@settings')->name('investment-plans.settings');
    Route::post('investment-plans/activate/{id}', 'InvestmentPlanController@activate')->name('investment-plans.activate');
    Route::post('investment-plans/deactivate/{id}', 'InvestmentPlanController@deactivate')->name('investment-plans.deactivate');
    
    // Active Investment Management Routes
    Route::get('active-investments', 'AdminInvestmentController@index')->name('active-investments.index');
    Route::get('active-investments/{id}', 'AdminInvestmentController@show')->name('active-investments.show');
    Route::get('active-investments/{id}/complete', 'AdminInvestmentController@complete')->name('active-investments.complete');
    Route::get('active-investments/{id}/cancel', 'AdminInvestmentController@cancel')->name('active-investments.cancel');
   
    // Trading History Management Routes
    Route::resource('trading-history', 'TradingHistoryController');
    Route::post('trading-history/{tradingHistory}/toggle', 'TradingHistoryController@toggleStatus')->name('trading-history.toggle');
  
    // Profit Withdrawal Management Routes
    Route::resource('profit-withdrawal', 'ProfitWithdrawalController');
    
    // Delete routes for commission management
    Route::get('investment-plans/delete/{id}', 'InvestmentPlanController@delete');
    Route::get('rank-rewards/delete/{id}', 'RankRewardController@delete');
});

// Password Reset Routes (outside user group - no authentication required)
Route::group(['namespace' => 'App\Http\Controllers'], function () {
    Route::get('password/request', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');
});

//for user only
Route::group(['middleware' => ['auth', 'user', 'Language'], 'as' => 'user.', 'prefix' => 'user', 'namespace' => 'App\Http\Controllers\user'], function () {
    Route::get('dashboard', 'DashboardController@dashboard')->name('dashboard');
    Route::get('trading-history/download/{id}', 'PageController@downloadTradingHistory')
    ->name('trading-history.download');
    Route::get('history', 'PageController@history')->name('history');
    Route::get('waves', 'PageController@waves')->name('waves');
    Route::get('announcements', 'PageController@announcements')->name('announcements');
    Route::get('withdraw', 'PageController@withdraw')->name('withdraw');
    Route::get('trading-history', 'PageController@tradingHistory')->name('trading-history');
    Route::get('deposit', 'PageController@deposit')->name('deposit');
    Route::get('deposit-details', 'PageController@depositDetails');
    Route::get('manual-deposit', 'PageController@manualDeposit')->name('manual-deposit');
    Route::post('manual-deposit/submit', 'PostController@submitManualDeposit')->name('manual-deposit.submit');

    Route::get('order-grav-ruls', 'PageController@orderGrav');
    Route::get('order/grab', 'PageController@orderInfo');
    Route::get('grab/submit', 'PageController@grabSubmit');
    Route::get('order/load', 'PageController@orderLoad')->name('order.load');

    Route::get('invite', 'PageController@invite')->name('invite');
    Route::get('event', 'PageController@event')->name('event');
    Route::get('about', 'PageController@about')->name('about');
    Route::get('rule', 'PageController@rule')->name('rule');
    Route::get('promotion', 'PageController@promotion')->name('promotion');
    Route::get('news', 'PageController@news')->name('news');


    Route::get('userinfo', 'PageController@userinfo')->name('userinfo');
    Route::get('vip', 'PageController@vip')->name('vip');
    Route::get('team', 'PageController@team')->name('team');
    Route::get('account', 'PageController@account')->name('account');
    Route::get('record', 'PageController@record')->name('record');
    Route::get('wallet', 'PageController@wallet')->name('wallet');
    Route::get('news-details/{id}', 'PageController@newsDetails');
    Route::get('change-password', 'PageController@changePassword');
    Route::get('wallet-guide', 'PageController@walletGuide');
    Route::get('sessionex', 'PageController@sessionex');
    Route::get('payment-page', 'PageController@paymentPage');
    Route::get('bep20-payment', 'PageController@bep20Payment');
    Route::get('address-setup', 'PageController@addressSetup');
    Route::get('checkdepo', 'PageController@checkDepo');
    

  
    // Profile Image Upload Routes
    Route::post('profile/upload-photo', 'ProfileController@uploadPhoto')->name('profile.upload-photo');
    Route::delete('profile/remove-photo', 'ProfileController@removePhoto')->name('profile.remove-photo');

    //post controller 
    Route::get('password-update', 'PostController@passwordUpdate');
    Route::get('deposite-address', 'PostController@depositeAddress');
    Route::post('trcaddress-update', 'PostController@trcUpdate');
    Route::post('bep20address-update', 'PostController@bep20AddressUpdate');
    Route::post('withdraw/validate', 'PostController@withdrawVal');

    Route::get('deposite-information', 'PostController@depositeInformation');
    Route::post('verify-bep20-deposit', 'PostController@verifyBep20Deposit')->name('verify-bep20-deposit');
  //  Route::get('test-bep20', 'PostController@testBep20System');

    // Investment routes

    Route::get('investment', 'InvestmentController@index')->name('investment.index');
    Route::post('investment/invest', 'InvestmentController@invest')->name('investment.invest');
    Route::get('investment/history', 'InvestmentController@history')->name('investment.history');
    Route::get('investment/active', 'InvestmentController@active')->name('investment.active');
    Route::get('investment/profit-history', 'InvestmentController@profitHistory')->name('investment.profit-history');
    Route::get('investment/profit-history/export', 'InvestmentController@exportProfitHistory')->name('investment.profit-history.export');
    Route::post('investment/distribute-profit', 'InvestmentController@distributeDailyProfit')->name('investment.distribute');
    
    // Simple Rank Management Routes (Clean MVC Pattern)
    Route::get('rank/requirements', 'RankController@requirements')->name('rank.requirements');
    Route::get('rank/upgrade-center', 'RankController@upgradeCenter')->name('rank.upgrade-center');
    Route::post('rank/upgrade', 'RankController@upgrade')->name('rank.upgrade');
    Route::get('rank/history', 'RankController@history')->name('rank.history');
    Route::get('rank/data', 'RankController@getUserRankData')->name('rank.data');
  
});



Route::get('schedule-run', function (Request $request) {
    $startedAt = microtime(true);

    // Optional security: .env এ CRON_SECRET=your-long-random-token দিন
    $expected = config('app.cron_secret', env('CRON_SECRET'));
    $token    = (string) $request->query('token', '');
    if ($expected && !hash_equals($expected, $token)) {
        return response()->json([
            'ok'    => false,
            'error' => 'Unauthorized (invalid token)',
            'code'  => 403,
        ], 200); // HTTP 200 রাখছি যাতে cron "Failed" না দেখায়
    }

    $force = filter_var($request->query('force', false), FILTER_VALIDATE_BOOLEAN);
    $date  = $request->query('date');

    // চাইলে schedule:run এড়িয়েই দরকারি কমান্ডগুলো সরাসরি কল করি,
    // যাতে টাইমিং-গেটিং এ না পড়ে ও কোন কমান্ড ফেল করেছে—স্পষ্ট ধরা যায়।
    $commands = [
        [
            'label'   => 'Distribute profit',
            'name'    => 'investment:distribute-profit',
            'params'  => array_filter([
                '--force' => $force ? true : null,
                '--date'  => $date ?: null,
            ]),
        ],
        [
            'label'   => 'Process BEP20 deposits',
            'name'    => 'deposits:process-bep20',
            'params'  => [],
        ],
        [
            'label'   => 'Monitor BEP20 stuck deposits',
            'name'    => 'monitor:bep20-deposits',
            'params'  => ['--notify-stuck' => true],
        ],
    ];

    $ran   = [];
    $ok    = true;

    foreach ($commands as $c) {
        $t0 = microtime(true);
        try {
            $exit = Artisan::call($c['name'], $c['params']);
            $out  = trim(Artisan::output());
            $ran[] = [
                'label'       => $c['label'],
                'command'     => $c['name'],
                'params'      => $c['params'],
                'exit_code'   => $exit,
                'duration_ms' => (int) round((microtime(true) - $t0) * 1000),
                'output'      => $out,
            ];
            if ($exit !== 0) $ok = false;
        } catch (\Throwable $e) {
            $ok = false;
            $ran[] = [
                'label'       => $c['label'],
                'command'     => $c['name'],
                'params'      => $c['params'],
                'exit_code'   => 255,
                'duration_ms' => (int) round((microtime(true) - $t0) * 1000),
                'error'       => $e->getMessage(),
            ];
            Log::error('schedule-run error', [
                'cmd' => $c['name'],
                'err' => $e->getMessage(),
            ]);
        }
    }

    return response()->json([
        'ok'          => $ok,
        'server_time' => now()->toDateTimeString(),
        'meta'        => [
            'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
            'ip'          => $request->ip(),
            'ua'          => $request->userAgent(),
            'timezone'    => config('app.timezone'),
        ],
        'ran'  => $ran,
        'hint' => [
            'force' => 'Use ?force=1 to bypass weekday/time checks',
            'date'  => 'Use ?date=YYYY-MM-DD to run for a specific date',
        ],
    ], 200);
});


// Tempery
Route::get('run-profit', function () {
    Artisan::call('investment:distribute-profit', ['--force' => true]);
    return '✅ Profit distributed manually for today!';
});

// cache clear
Route::get('reboot', function () {
    Artisan::call('config:cache');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('key:generate');
    // composer dump-autoload
    dd('Done');
});
