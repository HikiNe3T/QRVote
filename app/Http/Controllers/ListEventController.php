<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ListEventController extends Controller
{
    /**
 * Tampilkan daftar event yang pernah dimasuki / diikuti oleh user/guest.
 */
public function listEvent(Request $request)
{
    $voterId = null;

    if (auth()->check()) {
        $voterId = 'user:' . auth()->id();
    } elseif ($pid = session('participant_id')) {
        $voterId = 'participant:' . $pid;
    } elseif (session()->has('voter_token')) {
        $voterId = 'guest:' . session('voter_token');
    }

    // Ambil event_id dari tabel votes yang pernah diikuti voter
    $votedEventIds = [];
    if ($voterId) {
        $votedEventIds = DB::table('votes')
            ->where('voter_identifier', $voterId)
            ->pluck('event_id')
            ->toArray();
    }

    // Jika ada session event_id aktif terakhir, gabungkan juga
    if (session('event_id')) {
        $votedEventIds[] = session('event_id');
    }

    $votedEventIds = array_unique(array_filter($votedEventIds));

    // Ambil data event dari database
    $events = DB::table('events')
        ->whereIn('id', $votedEventIds)
        ->orderBy('created_at', 'desc')
        ->get();

    // Map status event (aktif/selesai)
    $events = $events->map(function ($event) {
        $now   = now();
        $start = \Carbon\Carbon::parse($event->start_date . ' ' . $event->start_time);
        $end   = \Carbon\Carbon::parse($event->end_date . ' ' . $event->end_time);

        if ($now < $start) {
            $event->status_label = 'Akan Datang';
            $event->status_class = 'badge--warning';
        } elseif ($now > $end) {
            $event->status_label = 'Selesai';
            $event->status_class = 'badge--error';
        } else {
            $event->status_label = 'Aktif';
            $event->status_class = 'badge--success';
        }

        $event->formatted_date = \Carbon\Carbon::parse($event->start_date)->translatedFormat('d F Y');
        return $event;
    });

    return view('list-event', compact('events'));
}
}
