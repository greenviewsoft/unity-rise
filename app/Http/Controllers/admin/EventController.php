<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
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

            $query = Event::query();
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
            $events = $query->paginate(15);
        }else{
            $events = Event::orderBy('id', 'asc')
            ->paginate(15);
        }

        return view('admin.events.events_list', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.events.events_create');
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
            'title' => 'required',
            'file' => 'required',
            'type' => 'required',
        ]);

        $file = 'public/upload/'.time().'.'.$request->file->getClientOriginalExtension();
        $request->file->move(public_path('/upload'), $file);

        $events = new Event();
        $events->title = $request->title;
        $events->image = $file;
        $events->type = $request->type;
        $events->save();

        return redirect('admin/events')->with('success', 'Event created successfully');
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
        $event = Event::find($id);
        
        return view('admin.events.events_edit', compact('event'));
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
            'title' => 'required',
            'type' => 'required',
        ]);



        if($request->file != null){
            $file = 'public/upload/'.time().'.'.$request->file->getClientOriginalExtension();
            $request->file->move(public_path('/upload'), $file);
        }else{
            $oldf = Event::find($id);
            $file = $oldf->image;
        }

        $events = Event::find($id);
        $events->title = $request->title;
        $events->image = $file;
        $events->type = $request->type;
        $events->save();

        return redirect('admin/events')->with('success', 'Event updated successfully');
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
        $vip = Event::find($id);
        $vip->delete();
        return redirect()->back()->with('success', 'Event deleted successfully');
    }
}
