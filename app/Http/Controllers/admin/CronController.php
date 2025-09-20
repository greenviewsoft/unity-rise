<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Cronhit;
use Illuminate\Http\Request;

class CronController extends Controller
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

            $query = Cronhit::query();
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
            $cronhits = $query->paginate(15);
        }else{
            $cronhits = Cronhit::orderBy('id', 'asc')
            ->paginate(15);
        }

        return view('admin.cron.cron_list', compact('cronhits'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.cron.cron_create');
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
            'name' => 'required',
            'url' => 'required',
        ]);


        $cron = new Cronhit();
        $cron->name = $request->name;
        $cron->url = $request->url;
        $cron->save();

        return redirect('admin/cron')->with('success', 'Cron created successfully');

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
        $cron = Cronhit::find($id);
        return view('admin.cron.cron_edit', compact('cron'));
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
            'name' => 'required',
            'url' => 'required',
        ]);


        $cron = Cronhit::find($id);
        $cron->name = $request->name;
        $cron->url = $request->url;
        $cron->save();

        return redirect('admin/cron')->with('success', 'Cron updated successfully');
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
        $cron = Cronhit::find($id);
        $cron->delete();

        return redirect('admin/cron')->with('success', 'Cron deleted successfully');
    }
}
