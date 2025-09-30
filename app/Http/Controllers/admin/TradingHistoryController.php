<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TradingHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TradingHistoryController extends Controller
{
    /**
     * Display a listing of trading histories
     */
    public function index()
    {
        $tradingHistories = TradingHistory::with('uploader')
            ->orderBy('upload_date', 'desc')
            ->paginate(15);

        return view('admin.trading_history.index', compact('tradingHistories'));
    }

    /**
     * Show the form for creating a new trading history
     */
    public function create()
    {
        return view('admin.trading_history.create');
    }

    /**
     * Store a newly uploaded trading history
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'pdf_file' => 'required|file|mimes:pdf|max:51200', // 50MB max
        ]);
    
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
    
        try {
            $file = $request->file('pdf_file');
    
            if (!$file) {
                return back()->with('error', 'No file uploaded.');
            }
    
            // Get size & mime BEFORE moving
            $fileSize = $file->getSize();
            $fileMime = $file->getMimeType();
    
            // Generate unique filename
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9.-]/', '_', $file->getClientOriginalName());
    
            $destinationPath = public_path('upload/pdf');
    
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
    
            // Move file
            $file->move($destinationPath, $fileName);
    
            // Create DB record
            TradingHistory::create([
                'title' => $request->title,
                'description' => $request->description,
                'file_path' => 'upload/pdf/' . $fileName,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $fileSize,
                'mime_type' => $fileMime,
                'is_active' => true,
                'uploaded_by' => Auth::id(),
                'upload_date' => now(),
            ]);
    
            return redirect()->route('admin.trading-history.index')
                ->with('success', 'Trading history PDF uploaded successfully!');
    
        } catch (\Exception $e) {
            \Log::error('Trading history upload failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to upload PDF. Error: ' . $e->getMessage());
        }
    }
    



    /**
     * Show the form for editing the specified trading history
     */
    public function edit(TradingHistory $tradingHistory)
    {
        return view('admin.trading_history.edit', compact('tradingHistory'));
    }

    /**
     * Update the specified trading history
     */
    public function update(Request $request, TradingHistory $tradingHistory)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $tradingHistory->update([
                'title' => $request->title,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('admin.trading-history.index')
                ->with('success', 'Trading history updated successfully!');

        } catch (\Exception $e) {
            \Log::error('Trading history update failed: ' . $e->getMessage());

            return back()->with('error', 'Failed to update trading history. Please try again.');
        }
    }

    /**
     * Remove the specified trading history
     */
    public function destroy(TradingHistory $tradingHistory)
    {
        try {
            // Delete file from storage
            Storage::disk('public')->delete('trading_history/' . $tradingHistory->file_path);

            // Delete record
            $tradingHistory->delete();

            return redirect()->route('admin.trading-history.index')
                ->with('success', 'Trading history deleted successfully!');

        } catch (\Exception $e) {
            \Log::error('Trading history deletion failed: ' . $e->getMessage());

            return back()->with('error', 'Failed to delete trading history. Please try again.');
        }
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(TradingHistory $tradingHistory)
    {
        try {
            $tradingHistory->update([
                'is_active' => !$tradingHistory->is_active
            ]);

            $status = $tradingHistory->is_active ? 'activated' : 'deactivated';

            return redirect()->route('admin.trading-history.index')
                ->with('success', "Trading history {$status} successfully!");

        } catch (\Exception $e) {
            \Log::error('Trading history status toggle failed: ' . $e->getMessage());

            return back()->with('error', 'Failed to update status. Please try again.');
        }
    }

    /**
     * Download the PDF file
     * 
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function downloadTradingHistory($id)
    {
        $history = \App\Models\TradingHistory::findOrFail($id);
        $filePath = public_path('upload/pdf/' . $history->file_path);
    
        if (!file_exists($filePath)) {
            return back()->with('error', 'File not found.');
        }
    
        return response()->download($filePath, $history->file_name);
    }


}
