<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ProfileController extends Controller
{
    /**
     * Upload profile image
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = Auth::user();
        
        // Create uploads/profile directory if it doesn't exist
        $uploadPath = public_path('uploads/profile');
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        // Delete old photo if exists
        if ($user->photo && File::exists(public_path('uploads/profile/' . $user->photo))) {
            File::delete(public_path('uploads/profile/' . $user->photo));
        }

        // Upload new photo
        $photo = $request->file('photo');
        $filename = time() . '_' . $user->id . '.' . $photo->getClientOriginalExtension();
        $photo->move($uploadPath, $filename);

        // Update user photo in database
        $user->update(['photo' => $filename]);

        return response()->json([
            'success' => true,
            'message' => 'Profile photo updated successfully!',
            'photo_url' => asset('uploads/profile/' . $filename)
        ]);
    }

    /**
     * Remove profile photo
     */
    public function removePhoto()
    {
        $user = Auth::user();
        
        // Delete photo file if exists
        if ($user->photo && File::exists(public_path('uploads/profile/' . $user->photo))) {
            File::delete(public_path('uploads/profile/' . $user->photo));
        }

        // Remove photo from database
        $user->update(['photo' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Profile photo removed successfully!'
        ]);
    }
}
