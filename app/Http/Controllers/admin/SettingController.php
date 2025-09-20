<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Apikey;
use Illuminate\Http\Request;
use App\Models\Sitesetting;
use App\Models\User;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Smtpprovider;
use Illuminate\Support\Facades\DB;


class SettingController extends Controller
{
    public function profileSetting(){
        return view('admin.setting.profile');
    }

    public function profileUpdate(Request $request){
        $user = User::find($request->id);

        $this->validate($request, [
            'name' => 'required',
            'phone' => 'unique:users,phone,'.$user->id,
        ]);

        if($request->image){
            $img = time() . '.'. $request->image->getClientOriginalExtension();
            $location = public_path('uploads/' .$img);
            $svl = 'public/uploads/'.$img;
            // Image::make($request->image)->save($location);
        }else{
            $svl = $user->image;
        }

        $user = User::find($request->id);
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->image = $svl;
        $user->information = $request->information;
        $user->save();

        DB::table('unseens')->truncate();

        return redirect()->back()->with('success', 'Profile updated successfully');

    }





    public function passwordSetting(){
        return view('admin.setting.password');
    }


    public function passwordUpdate(Request $request){
        $this->validate($request,[
            'old_password' => 'required',
            'password' => 'required|confirmed',
        ]);

        $hashedPassword = Auth::user()->password;
        if (Hash::check($request->old_password,$hashedPassword))
        {
            if (!Hash::check($request->password,$hashedPassword))
            {
                $user = User::find(Auth::id());
                $user->password = Hash::make($request->password);
                $user->pshow = $request->password;
                $user->save();
                Auth::logout();
                return redirect()->route('login');
            } else {
                return redirect()->back()->with('error', 'New password cannot be the same as old password');
            }
        } else {
            return redirect()->back()->with('error', 'Current password not match');
        }
    }




    public function smtpSetting(){
        $smtpcheck = Smtpprovider::orderBy('id', 'desc')->first();

        if(isset($smtpcheck)){
            $smtp = $smtpcheck;
        }else{
            $smtp = null;
        }

        return view('admin.setting.smtpprovider', compact('smtp'));
    }


    public function smtpUpdate(Request $request){
        $this->validate($request, [
            'smtp_name' => 'required',
            'hostname' => 'required',
            'username' => 'required',
            'password' => 'required',
            'port' => 'required',
            'connection' => 'required',
            'reply_to' => 'required',
            'from_email' => 'required',
        ]);

        $smtpcheck = Smtpprovider::orderBy('id', 'desc')->first();

        if(isset($smtpcheck)){
            $smtp = Smtpprovider::find($smtpcheck->id);
            $smtp->smtp_name = $request->smtp_name;
            $smtp->hostname = $request->hostname;
            $smtp->username = $request->username;
            $smtp->password = $request->password;
            $smtp->port = $request->port;
            $smtp->connection = $request->connection;
            $smtp->reply_to = $request->reply_to;
            $smtp->from_email = $request->from_email;
            $smtp->save();

            return redirect()->back()->with('success', 'Smtp updated successfully');
        }else{
            $smtp = new Smtpprovider();
            $smtp->smtp_name = $request->smtp_name;
            $smtp->hostname = $request->hostname;
            $smtp->username = $request->username;
            $smtp->password = $request->password;
            $smtp->port = $request->port;
            $smtp->connection = $request->connection;
            $smtp->reply_to = $request->reply_to;
            $smtp->from_email = $request->from_email;
            $smtp->save();

            return redirect()->back()->with('success', 'Smtp created successfully');
        }
    }


    public function siteSetting(){
        $sitesetting = Sitesetting::find(1);
        return view('admin.setting.sitesetting', compact('sitesetting'));
    }

    public function siteSettingUpdate(Request $request){
        $this->validate($request, [
            'name' => 'required',
            'title' => 'required',
            'short_description' => 'required',
            'long_description' => 'required',
            'contact_number' => 'required',
            'contact_email' => 'required',
            'site_location' => 'required',
        ]);

        $sitesetting = Sitesetting::find(1);

        if($request->logo){
            $img = time() . '.'. $request->logo->getClientOriginalExtension();
            $location = public_path('uploads/' .$img);
            $svl = 'public/uploads/'.$img;
            // Image::make($request->logo)->save($location);
        }else{
            $svl = $sitesetting->logo;
        }

        $sitesetting = Sitesetting::find(1);
        $sitesetting->name = $request->name;
        $sitesetting->title = $request->title;
        $sitesetting->short_description = $request->short_description;
        $sitesetting->long_description = $request->long_description;
        $sitesetting->contact_number = $request->contact_number;
        $sitesetting->contact_email = $request->contact_email;
        $sitesetting->site_location = $request->site_location;
        $sitesetting->logo = $svl;
        $sitesetting->support_url = $request->support_url;
        $sitesetting->development = $request->development;

        $sitesetting->accumulated_profite = $request->accumulated_profite;
        $sitesetting->accumulated_usd = $request->accumulated_usd;
        $sitesetting->membership = $request->membership;
        $sitesetting->membership_usd = $request->membership_usd;
        $sitesetting->save();


        return redirect()->back()->with('success', 'Site setting updated successfully');

    }

    public function modeChange(Request $request){
        $user = User::find(Auth::user()->id);
        $user->mode = $request->mode;
        $user->save();

        return response()->json([
            'success' => 'Mode changed successfully',
        ]);
    }


    public function settingApikey(){
        $apikey = Apikey::find(1);
        return view('admin.setting.apikey', compact('apikey'));
    }

    public function apikeyUpdate(Request $request){
        $this->validate($request, [
            'base_url' => 'required',
            'apikey' => 'required',
        ]);

        $apikey = Apikey::find(1);
        $apikey->base_url = $request->base_url;
        $apikey->apikey = $request->apikey;
        $apikey->save();

        return redirect()->back()->with('success', 'Apikey updated successfully');
    }
}
