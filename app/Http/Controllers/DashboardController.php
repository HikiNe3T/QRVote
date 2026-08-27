<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Event;

class DashboardController extends Controller
{
    public function index()
    {
        $events = Event::where('creator_id', Auth::id())
                        ->latest()
                        ->get();

        $events = Event::where('creator_id', Auth::id())
                        ->with('participants') // penting!
                        ->latest()
                        ->get();

        return view('dashboard', compact('events'));
    }

    public function show($id)
{
    $event = Event::with(['participants', 'candidates'])->findOrFail($id);

    return view('admin-event', compact('event'));
}
    
}