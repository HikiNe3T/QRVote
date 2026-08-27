<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Participant;
use App\Models\Candidate;

class VoteController extends Controller
{
    /*
    |-----------------------------------------
    | HALAMAN VOTE
    |-----------------------------------------
    */
    public function index($eventId)
    {
        $event = Event::findOrFail($eventId);

        // kalau private → harus punya session
        if ($event->voting_type === 'private') {

            if (!session('participant_id')) {
                return redirect()->route('input.code', $eventId);
            }
        }

        $candidates = Candidate::where('event_id', $eventId)->get();

        return view('vote', compact('event', 'candidates'));
    }

    /*
    |-----------------------------------------
    | SUBMIT VOTE
    |-----------------------------------------
    */
    public function submit(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);

        // PRIVATE CHECK
        if ($event->voting_type === 'private') {

            $participantId = session('participant_id');
            $participant = Participant::find($participantId);

            if (!$participant) {
                return redirect()->route('input.code', $eventId);
            }

            if ($participant->has_voted) {
                return back()->with('error', 'Kamu sudah vote');
            }
        }

        // VALIDASI PILIHAN
        $request->validate([
            'candidate_id' => 'required'
        ]);

        /*
        |-----------------------------------------
        | SIMPAN VOTE
        |-----------------------------------------
        */

        // contoh simpel (nanti bisa kamu upgrade ke TOPSIS)
        $candidate = Candidate::find($request->candidate_id);
        $candidate->increment('votes');

        /*
        |-----------------------------------------
        | LOCK (PRIVATE)
        |-----------------------------------------
        */
        if ($event->voting_type === 'private') {
            $participant->update([
                'has_voted' => 1
            ]);
        }

        return back()->with('success', 'Vote berhasil!');
    }
}