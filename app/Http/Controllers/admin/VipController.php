<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Vip;
use Illuminate\Http\Request;

class VipController extends Controller
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

            $query = Vip::query();
            if (isset($key))
            {
                $query->where(function($q) use ($key) {
                    $q->orWhere('requred_from', $key);
                    $q->orWhere('requred_to', $key);
                });
            }
            if (isset($from) && isset($to))
            {
                $query->where(function($q) use ($from, $to) {
                    $q->WhereBetween('created_at', [$from, $to]);
                });
            }
            $vips = $query->paginate(15);
        }else{
            $vips = Vip::orderBy('id', 'asc')
            ->paginate(15);
        }


        return view('admin.vip.vip_list', compact('vips'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.vip.vip_create');
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
            'requred_from' => 'required',
            'requred_to' => 'required',
            'income_from' => 'required',
            'income_to' => 'required',
            'image' => 'required',
        ]);

        
        $image = 'public/upload/'.time().'.'.$request->image->getClientOriginalExtension();
        $request->image->move(public_path('/upload'), $image);


        $vip = new Vip();
        $vip->requred_from = $request->requred_from;
        $vip->requred_to = $request->requred_to;
        $vip->income_from = $request->income_from;
        $vip->income_to = $request->income_to;
        $vip->image = $image;
        $vip->save();

        return redirect('admin/vip')->with('success', 'Vip created successfully');
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
        $vip = Vip::find($id);

        return view('admin.vip.vip_edit', compact('vip'));
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
        $this->validate($request, [
            'requred_from' => 'required',
            'requred_to' => 'required',
            'income_from' => 'required',
            'income_to' => 'required',
        ]);

        
        if($request->image != null){
            $image = 'public/upload/'.time().'.'.$request->image->getClientOriginalExtension();
            $request->image->move(public_path('/upload'), $image);
        }else{
            $oldf = Vip::find($id);
            $image = $oldf->image;
        }



        $vip = Vip::find($id);
        $vip->requred_from = $request->requred_from;
        $vip->requred_to = $request->requred_to;
        $vip->income_from = $request->income_from;
        $vip->income_to = $request->income_to;
        $vip->image = $image;
        $vip->save();

        return redirect('admin/vip')->with('success', 'Vip updated successfully');
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

    public function delete($id){
        $vip = Vip::find($id);
        $vip->delete();
        return redirect()->back()->with('success', 'Vip deleted successfully');
    }
}
