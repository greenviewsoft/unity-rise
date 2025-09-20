<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;

class AnnouncementController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $announcements = Announcement::orderBy('id', 'desc')
        ->get();

        return view('admin.announcement.announcement_list', compact('announcements'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.announcement.announcement_create');
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
            'announcement' => 'required',
            'type' => 'required',
            'livetext' => 'required',
        ]);

        if($request->image != null){
            $image = 'public/upload/'.time().'.'.$request->image->getClientOriginalExtension();
            $request->image->move(public_path('/upload'), $image);
        }else{
            $image = null;
        }


        $announcement = new Announcement();
        $announcement->livetext = $request->livetext;
        $announcement->announcement = $request->announcement;
        $announcement->type = $request->type;
        $announcement->image = $image;
        $announcement->save();

        return redirect('admin/announcement')->with('success', 'Announcement created successfully');
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
        $announcement = Announcement::find($id);

        return view('admin.announcement.announcement', compact('announcement'));
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
            'announcement' => 'required',
            'livetext' => 'required',
            'type' => 'required',
        ]);


        $announcement = Announcement::find($id);
        if($request->image != null){
            $image = 'public/upload/'.time().'.'.$request->image->getClientOriginalExtension();
            $request->image->move(public_path('/upload'), $image);
        }else{
            $image = $announcement->image;
        }


        $announcement = Announcement::find($id);
        $announcement->livetext = $request->livetext;
        $announcement->announcement = $request->announcement;
        $announcement->type = $request->type;
        $announcement->image = $image;
        $announcement->save();

        return redirect('admin/announcement')->with('success', 'Announcement updated successfully');
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
        $announcement = Announcement::find($id);
        $announcement->delete();
        return redirect('admin/announcement')->with('success', 'Announcement deleted successfully');
    }
}
