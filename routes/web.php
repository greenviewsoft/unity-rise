<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

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
    Route::get('refergift/delete/{id}', 'RefergiftController@delete');

    //all delete
    Route::get('announcement/delete/{id}', 'AnnouncementController@delete');
    Route::get('product/delete/{id}', 'ProductController@delete');
    Route::get('lang/delete/{id}', 'LangController@delete');
    Route::get('vip/delete/{id}', 'VipController@delete');
    Route::get('product/delete/{id}', 'ProductController@delete');
    Route::get('energy/delete/{id}', 'EnergyController@delete');
    Route::get('events/delete/{id}', 'EventController@delete');
    Route::get('cron/delete/{id}', 'CronController@delete');

    //for quote profite contorller
    // Route::get('quote_profite', 'QuoteprofitController@quoteProtie');
    // Route::get('quote/edit/{id}', 'QuoteprofitController@quoteEdit');
    // Route::post('quote/update', 'QuoteprofitController@quoteUpdate');
    // Route::get('profite/edit/{id}', 'QuoteprofitController@profiteEdit');
    // Route::post('profite/update', 'QuoteprofitController@profiteUpdate');
    // Route::get('withdraw/create', 'QuoteprofitController@withdrawCreate');
    // Route::post('profite/store', 'QuoteprofitController@profitStore');
    // Route::get('quote/create', 'QuoteprofitController@addQutoe');
    // Route::post('quote/store', 'QuoteprofitController@quoteStore');
    // Route::get('address', 'QuoteprofitController@address');
    // Route::get('address/details/{id}', 'QuoteprofitController@addressDetails');
    

    Route::resource('user', 'UserController');

    Route::resource('announcement', 'AnnouncementController');
    Route::resource('settingtrx', 'SettingtrxController');
    Route::resource('vip', 'VipController');
    Route::resource('product', 'ProductController');
    Route::resource('energy', 'EnergyController');
    Route::resource('events', 'EventController');
    Route::resource('refergift', 'RefergiftController');
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

    Route::get('lobby', 'PageController@lobby')->name('lobby');
    Route::get('amazon', 'PageController@amazon')->name('amazon');
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
    

    //post controller 
    Route::get('password-update', 'PostController@passwordUpdate');
    Route::get('deposite-address', 'PostController@depositeAddress');
    Route::post('trcaddress-update', 'PostController@trcUpdate');
    Route::post('withdraw/validate', 'PostController@withdrawVal');

    Route::get('deposite-information', 'PostController@depositeInformation');
    Route::post('verify-bep20-deposit', 'PostController@verifyBep20Deposit')->name('verify-bep20-deposit');
    Route::get('test-bep20', 'PostController@testBep20System');

    // Investment routes
    Route::get('investment', 'InvestmentController@index')->name('investment.index');
    Route::post('investment/create', 'InvestmentController@invest')->name('investment.create');
    Route::get('investment/history', 'InvestmentController@history')->name('investment.history');
    Route::get('investment/active', 'InvestmentController@active')->name('investment.active');
    Route::post('investment/distribute-profit', 'InvestmentController@distributeDailyProfit')->name('investment.distribute');
    
    // Simple Rank Management Routes (Clean MVC Pattern)
    Route::get('rank/requirements', 'RankController@requirements')->name('rank.requirements');
    Route::get('rank/upgrade-center', 'RankController@upgradeCenter')->name('rank.upgrade-center');
    Route::post('rank/upgrade', 'RankController@upgrade')->name('rank.upgrade');
    Route::get('rank/history', 'RankController@history')->name('rank.history');
    Route::get('rank/data', 'RankController@getUserRankData')->name('rank.data');
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
