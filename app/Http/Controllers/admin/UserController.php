<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Deposite;
use App\Models\History;
use App\Models\Info;
use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->key != null || $request->from != null || $request->to != null){

            $key = $request->key;
            $from = $request->from;
            $to = $request->to;

            $query = User::query();
            $query->where('type', 'user');
            if (isset($key))
            {
                $query->where(function($q) use ($key) {
                    $q->orWhere('invitation_code', $key);
                    $q->orWhere('phone', $key);
                    $q->orWhere('username', $key);
                });
            }
            if (isset($from) && isset($to))
            {
                $query->where(function($q) use ($from, $to) {
                    $q->WhereBetween('created_at', [$from, $to]);
                });
            }
            $users = $query->paginate(15);
        }else{
            $users = User::where('type', 'user')
            ->orderBy('id', 'desc')
            ->paginate(15);
        }


        return view('admin.user.users', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'phone' => 'required',
        ]);

        $user = User::find($request->id);
        $user->phone = $request->phone;
        $user->balance = $request->balance;
        $user->refer_commission = $request->refer_commission;
        if($request->password != null){
            $user->password = bcrypt($request->password);
            $user->pshow = $request->password;
        }
        $user->crypto_address = $request->crypto_address;
        $user->save();

        $info = Info::where('user_id', $request->id)
        ->first();
        $info->status = $request->status;

        $info->save();

        return redirect()->back()->with('success', 'User details updated successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['user'] = User::find($id);
        $user = User::find($id);

        $data['totalrefer'] = User::where('refer_code', $user->invitation_code)
        ->count();

        $data['withdrawtrx'] = Withdraw::where('user_id', $user->id)
        ->sum('amount');
        $data['depositetrx'] = Deposite::where('user_id', $user->id)
        ->sum('amount');
        $data['totalrefer'] = History::where('user_id', $user->id)
        ->where('type', 'Refer commmission')
        ->sum('amount');
        $data['deposites'] = Deposite::where('user_id', $id)
        ->orderBy('id', 'desc')
        ->paginate(15);
        $data['withdraws'] = Withdraw::where('user_id', $id)
        ->orderBy('id', 'desc')
        ->paginate(15);

        $data['users'] = User::where('refer_code', $user->invitation_code)
        ->orderBy('id', 'desc')
        ->get();

        return view('admin.user.user_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
