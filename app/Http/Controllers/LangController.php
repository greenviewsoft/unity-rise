<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use App\Models\Langtran;
use Stichoza\GoogleTranslate\GoogleTranslate;

class LangController extends Controller
{

    public function language(){

        $langtrans = Langtran::all();
        return view('extra.lang', compact('langtrans'));



        $trans =  [
            //footer
            'home'=> 'Home',
            'history'=> 'Histroy',
            'support'=> 'Support',
            'menu'=> 'Menu',
        
            //sidebar
            'welcome_back'=> 'Welcome Back',
            'account_details'=> 'Account Details',
            'logout'=> 'Log Out',
            'deposit'=> 'Deposit',
            'withdraw'=> 'Withdraw',
        
            'personal_info'=> 'Personal info',
            'vip'=> 'VIP',
            'team_report'=> 'Team Report',
            'event'=> 'Event',
            'account_details'=> 'Account details',
        
            'order_history'=> 'Order Histroy',
            'transaction'=> 'Transaction',
            'wallet'=> 'Wallet',
            'dark_mode'=> 'Dark Mode',
            'message'=> 'Messages',
        
            'download_app'=> 'Downlode App',
            'invite_friends'=> 'Invite Friends',
            'clear_cache'=> 'Clear cache',
            'logout'=> 'Log Out',
        
        
            // dashboard.blade.php
            'hello'=> 'Hello',
            'total_assets'=> 'My total assets',
            'today_profit'=> "Today's Profits",
            'promo_bonus'=> 'Promotion Bonus',
            'acc_profit'=> 'Accumulated Profits',
        
            'deposit'=> 'Deposit',
            'withdraw'=> 'Withdraw',
            'invite_friends'=> 'Invite friends',
            'event'=> 'Event',
            'about_us'=> 'About us',
        
            'ruels_desc'=> 'Rule description',
            'promotion'=> 'Promotion',
            'news'=> 'News',
            'task_lobby'=> 'Task lobby',
            'view_all'=> 'View All',
        
            'wealth_management'=> 'Wealth management function successful.',
            'stable_income'=> 'Long -term investment, stable income',
            'sales_commission'=> 'E-commerce sales commission',
            'profit_withdraw'=> 'Profit Withdrawal',
        
        
            //history.blade.php
            'history'=> 'Histroy',
            'my_asset'=> 'My total assets',
            'sales_ranking'=> 'The data is provided by Sales-Ranking Assistant',
            'official'=> 'official',
            'load_more'=> 'Load More',
        
            //waves.blade.php
            'task_lobby'=> 'Task Lobby',
            'hello'=> 'Hello',
            'total_asset'=> 'My total assets',
            'today_profit'=> 'Today Profits',
            'required_amount'=> 'Required amount',
        
            'income'=> 'Income',
        
        
            //deposit.blade.php
            'deposit_method'=> 'Deposit Method',
            'currency'=> 'Currency',
            'amount'=> 'Amount',
            'next_step'=> 'Next Step',
            'vr_currency'=> 'Virtual currency deposit',
        
            //payment_page.blade.php
            'address'=> 'Address',
            'copy_link' => 'Copy Link',
            'trx_warning'=> 'Please do not top up other non-TRX assets. After recharging, please click "Submit successfully"!',
            'click_submit'=> 'If the account has not been received for a long time, please click "Submit successfully" again.',
            'submit'=> 'submit',
        
        
            //deposite-details.blade.php
            'pro_deposit'=> 'Processing Deposit',
            'dep_method'=> 'Deposit method',
            'order_no'=> 'Order number',
            'dep_amount'=> 'Deposit amount',
            'currency'=> 'Currency',
        
            'conv_rate'=> 'Conversion rate',
            'after_exchange'=> 'Amount after exchange',
            'actually_receive'=> 'Actually receive',
            'back_home'=> 'Back to home page',
            'connect_support'=> 'Contact customer service',
        
            //withdraw.blade.php
            'withdraw_to'=> 'Withdraw to',
            'select_wallet'=> 'Select another wallet',
            'enter_cash_withdraw'=> 'Enter the amount for the cash withdrawal',
            'balance'=> 'Balance',
            'gen_withdraw_fees'=> 'General Cash Withdraw Fees',
        
            'add_withdraw_fees'=> 'Additional Withdraw Fees',
            'withdraw_notice'=> 'Withdrawal notice',
            'reminder'=> "Reminder: Due to the continuous increase in TRON handling fees, platform maintenance fees and manual
            withdrawal fees, in order to ensure the smooth operation of the platform and the timely arrival of
            each
            user's withdrawal, 2U and 8% of each amount will be deducted for each withdrawal Administrative
            costs,
            please be aware!",
            'cash_withdraw'=> 'Cash Withdrawal',
        
            //address-setup.blade.php
            'withdraw_address'=> 'Withdraw Address',
            'withdraw_account'=> 'Withdraw Account',
            'usdt_address'=> 'USDT TRC20 ADDRESS',
            'enter_address'=> 'Please Enter address',
            'login_password'=> 'LOGIN PASSWORD',
        
            'enter_password'=> 'Please Enter password',
            'submit'=> 'SUBMIT',
        
            //userinfo.blade.php
            'welcome_back'=> 'Welcome Back',
            'payapp'=> 'PayApp',
            'account_holder'=> 'Verified Account Holder',
            'information'=> 'Information',
            'details'=> 'Details',
        
            'login_password'=> 'Change Login Password',
            'change_withdrawpass'=> 'Change Withdrawal Password',
            'contact_support'=> 'Contact Support',
        
            //change-password.blade.php
            'cng_login_password'=> 'Change Login Password',
            'org_login_password'=> 'Original login password',
            'mew_login_password'=> 'New login password',
            'conf_login_password'=> 'Confirm New login password',
            'confrim'=> 'Confrim',
        
            //vip.blade.php
            'current_level'=> 'Current level',
            'min_balance'=> 'Minimum balance',
            'com_rate'=> 'Commission rate',
            'open_marks'=> 'Open markets',
            'required_amount'=> 'Required amount',
        
            'to_reach_next'=> 'To reach next VIP level',
            'balance_upgrade'=> 'Balance to upgrade',
            'deposit'=> 'Deposit',
            'summary'=> 'Summary',
        
            //team.blade.php
            'team_report'=> 'Team Report',
            'team_details'=> 'Team members details',
            'active_people'=> 'Active people',
            'number_team'=> 'Number of team members',
            'number_new_member'=> 'Number of new members',
        
            'total_deposit'=> 'Total USDT Deposite',
            'total_commission'=> 'Total USDT Commission',
        
            //record.blade.php
            'tr_history'=> 'Transaction history',
            'deposit_amount'=> 'Deposit Amount',
            'you_receive'=> 'You received',
        
            //announcements.blade.php
            'announcements'=> 'Announcements',
        
        ];
        foreach ($trans as $key => $value) {
            Langtran::create([
                'key' => $key,
                'lang' => $value,
            ]);
        }
    }


    public function change(Request $request)
    {
        App::setLocale($request->lang);
        session()->put('locale', $request->lang);

  
        return redirect()->back();
    }
}
