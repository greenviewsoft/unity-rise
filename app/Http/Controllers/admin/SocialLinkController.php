<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\SocialLink;
use Illuminate\Http\Request;

class SocialLinkController extends Controller
{
    /**
     * Display a listing of social links
     */
    public function index()
    {
        $socialLinks = SocialLink::orderBy('sort_order')->get();
        return view('admin.social-links.index', compact('socialLinks'));
    }
    
    /**
     * Show the form for creating a new social link
     */
    public function create()
    {
        return view('admin.social-links.create');
    }
    
    /**
     * Store a newly created social link
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
            'url' => 'required|url',
            'color' => 'required|string|max:7',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);
        
        SocialLink::create([
            'name' => $request->name,
            'icon' => $request->icon,
            'url' => $request->url,
            'color' => $request->color,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active') ? 1 : 0
        ]);
        
        return redirect()->route('admin.social-links.index')
                        ->with('success', 'Social link created successfully!');
    }
    
    /**
     * Show the form for editing the specified social link
     */
    public function edit($id)
    {
        $socialLink = SocialLink::findOrFail($id);
        return view('admin.social-links.edit', compact('socialLink'));
    }
    
    /**
     * Update the specified social link
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
            'url' => 'required|url',
            'color' => 'required|string|max:7',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);
        
        $socialLink = SocialLink::findOrFail($id);
        
        $socialLink->update([
            'name' => $request->name,
            'icon' => $request->icon,
            'url' => $request->url,
            'color' => $request->color,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active') ? 1 : 0
        ]);
        
        return redirect()->route('admin.social-links.index')
                        ->with('success', 'Social link updated successfully!');
    }
    
    /**
     * Remove the specified social link
     */
    public function destroy($id)
    {
        $socialLink = SocialLink::findOrFail($id);
        $socialLink->delete();
        
        return redirect()->route('admin.social-links.index')
                        ->with('success', 'Social link deleted successfully!');
    }
    
    /**
     * Toggle active status
     */
    public function toggle($id)
    {
        $socialLink = SocialLink::findOrFail($id);
        $socialLink->update(['is_active' => !$socialLink->is_active]);
        
        return redirect()->back()
                        ->with('success', 'Social link status updated!');
    }
}