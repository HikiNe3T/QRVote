<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ScanController extends Controller
{
    /**
     * Tampilkan halaman scan QR event.
     */
    public function scanEvent()
    {
        return view('scan-event');
    }

    public function processScan(Request $request)
    {
        $request->validate([
            'code_value' => 'required|string',
        ]);

        $codeValue = trim($request->input('code_value'));

        $eventId = $this->extractEventId($codeValue);

        if (!$eventId) {
            return back()->with('error', 'QR code tidak dikenali. Pastikan Anda memindai QR event yang benar.');
        }

        $event = DB::table('events')->where('id', $eventId)->first();

        if (!$event) {
            return back()->with('error', 'Event tidak ditemukan.');
        }

        // Cek tipe voting
        if ($event->voting_type === 'private') {
            // Redirect ke halaman input kode akses
            return redirect()->route('access.event', ['event_id' => $event->id]);
        }

        // Public: langsung ke halaman event
        return redirect()->to(route('event.show') . '?event_id=' . $event->id);
    }

    /**
     * Ekstrak event ID dari teks QR code.
     */
    private function extractEventId(string $text): ?string
    {
        $text = trim($text);

        // Kasus 1: teks adalah UUID langsung
        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $text)) {
            return $text;
        }

        // Kasus 2: URL dengan query param event_id
        if (filter_var($text, FILTER_VALIDATE_URL) || str_starts_with($text, '/')) {
            $parsed = parse_url($text);

            if (isset($parsed['query'])) {
                parse_str($parsed['query'], $params);
                if (!empty($params['event_id'])) {
                    return $params['event_id'];
                }
                if (!empty($params['id'])) {
                    return $params['id'];
                }
            }

            // Kasus 3: URL dengan UUID sebagai path segment terakhir
            $path = $parsed['path'] ?? '';
            $segments = explode('/', trim($path, '/'));
            foreach (array_reverse($segments) as $seg) {
                if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $seg)) {
                    return $seg;
                }
            }
        }

        // Kasus 4: cari UUID di dalam teks secara umum
        if (preg_match('/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/i', $text, $matches)) {
            return $matches[0];
        }

        return null;
    }

    /**
     * Tampilkan halaman input kode akses untuk event private.
     */
    public function accessEvent(Request $request)
    {
        $eventId = $request->query('event_id');

        if (!$eventId) {
            return redirect()->route('scan.event')->with('error', 'ID event tidak ditemukan.');
        }

        $event = DB::table('events')->where('id', $eventId)->first();

        if (!$event) {
            return redirect()->route('scan.event')->with('error', 'Event tidak ditemukan.');
        }

        return view('access-event', ['event' => $event]);
    }

    /**
     * Verifikasi kode akses untuk event private.
     */
    public function verifyAccessCode(Request $request)
    {
        $request->validate([
            'event_id' => 'required|string',
            'access_code' => 'required|string|size:6',
        ]);

        $eventId = $request->input('event_id');
        $accessCode = strtoupper(trim($request->input('access_code')));

        $participant = DB::table('participants')
            ->where('event_id', $eventId)
            ->where('access_code', $accessCode)
            ->first();

        if (!$participant) {
            return response()->json([
                'success' => false,
                'message' => 'Kode akses salah. Periksa kembali kode Anda.',
            ], 422);
        }

        if ($participant->has_voted) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memberikan suara pada event ini.',
            ], 422);
        }

        // Simpan participant_id di session untuk tracking
        session(['participant_id' => $participant->id, 'event_id' => $eventId]);

        return response()->json([
            'success' => true,
            'redirect' => route('event.show', ['event_id' => $eventId]),
        ]);
    }

    /**
     * Tampilkan halaman event dengan data dari database.
     */
    public function showEvent(Request $request)
    {
        $eventId = $request->query('event_id');

        if (!$eventId) {
            return redirect()->route('scan.event')->with('error', 'ID event tidak ditemukan.');
        }

        $event = DB::table('events')->where('id', $eventId)->first();

        if (!$event) {
            return redirect()->route('scan.event')->with('error', 'Event tidak ditemukan.');
        }

        // Ambil kandidat untuk event ini
        $candidates = DB::table('candidates')
            ->where('event_id', $eventId)
            ->orderBy('number', 'asc')
            ->get();

        // Hitung jumlah vote per kandidat
        $voteCounts = DB::table('votes')
            ->select('candidate_id', DB::raw('COUNT(*) as total'))
            ->where('event_id', $eventId)
            ->groupBy('candidate_id')
            ->pluck('total', 'candidate_id');

        // Format tanggal
        $startDate = \Carbon\Carbon::parse($event->start_date)->translatedFormat('d F Y');
        $endDate = \Carbon\Carbon::parse($event->end_date)->translatedFormat('d F Y');

        $dateRange = $startDate === $endDate
            ? $startDate
            : $startDate . ' - ' . $endDate;

        $timeRange = substr($event->start_time, 0, 5) . ' - ' . substr($event->end_time, 0, 5) . ' WIB';

        // Cek status event
        $now = now();
        $eventStart = \Carbon\Carbon::parse($event->start_date . ' ' . $event->start_time);
        $eventEnd = \Carbon\Carbon::parse($event->end_date . ' ' . $event->end_time);

        if ($now < $eventStart) {
            $status = 'upcoming';
            $statusLabel = 'Akan Datang';
        } elseif ($now > $eventEnd) {
            $status = 'ended';
            $statusLabel = 'Selesai';
        } else {
            $status = 'active';
            $statusLabel = 'Aktif';
        }

        return view('event', [
            'event' => $event,
            'candidates' => $candidates,
            'voteCounts' => $voteCounts,
            'dateRange' => $dateRange,
            'timeRange' => $timeRange,
            'status' => $status,
            'statusLabel' => $statusLabel,
        ]);
    }

    /* ===================== SCAN KANDIDAT ===================== */

    private function voterIdentifier(string $eventId): string
    {
        if (auth()->check()) {
            return 'user:' . auth()->id();
        }

        if ($pid = session('participant_id')) {
            return 'participant:' . $pid;
        }

        if (!session()->has('voter_token')) {
            session(['voter_token' => (string) Str::uuid()]);
        }

        return 'guest:' . session('voter_token');
    }

    private function resolveEventId(Request $request): ?string
    {
        return $request->query('event_id')
            ?? $request->input('event_id')
            ?? session('event_id');
    }

    private function eventWindow($event): array
    {
        $now   = now();
        $start = \Carbon\Carbon::parse($event->start_date . ' ' . $event->start_time);
        $end   = \Carbon\Carbon::parse($event->end_date . ' ' . $event->end_time);

        if ($now < $start)  return ['upcoming', 'Event belum dimulai.'];
        if ($now > $end)    return ['ended', 'Event sudah selesai.'];
        return ['active', null];
    }

    public function scanCandidate(Request $request)
    {
        $eventId = $this->resolveEventId($request);

        if (!$eventId) {
            return redirect()->route('scan.event')
                ->with('error', 'Silakan scan QR event terlebih dahulu.');
        }

        $event = DB::table('events')->where('id', $eventId)->first();
        if (!$event) {
            return redirect()->route('scan.event')->with('error', 'Event tidak ditemukan.');
        }

        [$status, $msg] = $this->eventWindow($event);
        if ($status !== 'active') {
            return redirect()->to(route('event.show') . '?event_id=' . $eventId)
                ->with('error', $msg);
        }

        session(['event_id' => $eventId]);

        $existing = DB::table('votes')
            ->where('event_id', $eventId)
            ->where('voter_identifier', $this->voterIdentifier($eventId))
            ->first();

        if ($existing) {
            return redirect()->to(route('event.show') . '?event_id=' . $eventId)
                ->with('error', 'Anda sudah memilih 1 kandidat pada event ini.');
        }

        return view('scan-candidate', ['event' => $event]);
    }

    public function processScanCandidate(Request $request)
    {
        $request->validate(['code_value' => 'required|string']);

        $eventId = $this->resolveEventId($request);
        if (!$eventId) {
            return redirect()->route('scan.event')
                ->with('error', 'Konteks event hilang. Scan QR event kembali.');
        }

        $event = DB::table('events')->where('id', $eventId)->first();
        if (!$event) {
            return redirect()->route('scan.event')->with('error', 'Event tidak ditemukan.');
        }

        [$status, $msg] = $this->eventWindow($event);
        if ($status !== 'active') {
            return redirect()->to(route('event.show') . '?event_id=' . $eventId)
                ->with('error', $msg);
        }

        $candidateId = $this->extractCandidateId(trim($request->input('code_value')));
        if (!$candidateId) {
            return back()->with('error', 'QR kandidat tidak dikenali.');
        }

        $candidate = DB::table('candidates')->where('id', $candidateId)->first();
        if (!$candidate) {
            return back()->with('error', 'Kandidat tidak ditemukan.');
        }

        if ($candidate->event_id !== $eventId) {
            return back()->with('error', 'QR kandidat ini bukan milik event yang Anda scan.');
        }

        return redirect()->to(route('rate.candidate')
            . '?candidate_id=' . $candidate->id
            . '&event_id=' . $candidate->event_id);
    }

    private function extractCandidateId(string $text): ?string
    {
        $text = trim($text);
        $uuid = '/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/i';

        if (preg_match('/^' . trim($uuid, '/i') . '$/i', $text)) {
            return $text;
        }

        if (filter_var($text, FILTER_VALIDATE_URL) || str_starts_with($text, '/')) {
            $parsed = parse_url($text);

            if (!empty($parsed['query'])) {
                parse_str($parsed['query'], $params);
                foreach (['candidate_id', 'candidate', 'id'] as $key) {
                    if (!empty($params[$key])) return $params[$key];
                }
            }

            foreach (array_reverse(explode('/', trim($parsed['path'] ?? '', '/'))) as $seg) {
                if (preg_match('/^' . trim($uuid, '/i') . '$/i', $seg)) return $seg;
            }
        }

        if (preg_match($uuid, $text, $m)) return $m[0];

        return null;
    }

    public function rateCandidate(Request $request)
    {
        $candidateId = $request->query('candidate_id');
        $eventId     = $request->query('event_id') ?? session('event_id');

        if (!$candidateId || !$eventId) {
            return redirect()->route('scan.event')->with('error', 'Data scan tidak lengkap.');
        }

        $event     = DB::table('events')->where('id', $eventId)->first();
        $candidate = DB::table('candidates')->where('id', $candidateId)->first();

        if (!$event || !$candidate || $candidate->event_id !== $event->id) {
            return redirect()->route('scan.event')->with('error', 'Data kandidat tidak valid.');
        }

        [$status, $msg] = $this->eventWindow($event);
        if ($status !== 'active') {
            return redirect()->to(route('event.show') . '?event_id=' . $event->id)
                ->with('error', $msg);
        }

        $alreadyVoted = DB::table('votes')
            ->where('event_id', $event->id)
            ->where('voter_identifier', $this->voterIdentifier($event->id))
            ->exists();

        if ($alreadyVoted) {
            return redirect()->to(route('event.show') . '?event_id=' . $event->id)
                ->with('error', 'Anda sudah memilih 1 kandidat pada event ini.');
        }

        $categories = ((int) $event->categories_enabled === 1)
            ? DB::table('categories')->where('event_id', $event->id)->orderBy('created_at')->get()
            : collect();

        return view('rate-candidate', [
            'event'      => $event,
            'candidate'  => $candidate,
            'categories' => $categories,
        ]);
    }

    public function submitRating(Request $request)
    {
        $request->validate([
            'event_id'     => 'required|string',
            'candidate_id' => 'required|string',
            'scores'       => 'array',
            'scores.*'     => 'integer|min:1|max:5',
        ]);

        $eventId     = $request->input('event_id');
        $candidateId = $request->input('candidate_id');

        $event     = DB::table('events')->where('id', $eventId)->first();
        $candidate = DB::table('candidates')->where('id', $candidateId)->first();

        if (!$event || !$candidate || $candidate->event_id !== $event->id) {
            return response()->json(['success' => false, 'message' => 'Data tidak valid.'], 422);
        }

        [$status, $msg] = $this->eventWindow($event);
        if ($status !== 'active') {
            return response()->json(['success' => false, 'message' => $msg], 422);
        }

        $voter = $this->voterIdentifier($event->id);

        if (DB::table('votes')->where('event_id', $event->id)->where('voter_identifier', $voter)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memilih 1 kandidat pada event ini.',
            ], 422);
        }

        $useCategories = (int) $event->categories_enabled === 1;
        $categories = $useCategories
            ? DB::table('categories')->where('event_id', $event->id)->get()
            : collect();

        $scores = $request->input('scores', []);

        if ($useCategories && $categories->isNotEmpty()) {
            foreach ($categories as $cat) {
                if (!isset($scores[$cat->id])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Semua kategori wajib dinilai.',
                    ], 422);
                }
            }
        }

        try {
            DB::transaction(function () use ($event, $candidate, $voter, $categories, $scores, $useCategories) {
                $voteId = (string) Str::uuid();

                DB::table('votes')->insert([
                    'id'               => $voteId,
                    'event_id'         => $event->id,
                    'candidate_id'     => $candidate->id,
                    'participant_id'   => session('participant_id'),
                    'voter_identifier' => $voter,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);

                if ($useCategories) {
                    foreach ($categories as $cat) {
                        DB::table('ratings')->insert([
                            'id'          => (string) Str::uuid(),
                            'vote_id'     => $voteId,
                            'category_id' => $cat->id,
                            'score'       => (int) $scores[$cat->id],
                            'created_at'  => now(),
                            'updated_at'  => now(),
                        ]);
                    }
                }

                if ($pid = session('participant_id')) {
                    DB::table('participants')->where('id', $pid)->update([
                        'has_voted'  => 1,
                        'updated_at' => now(),
                    ]);
                }
            });
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Suara Anda sudah tercatat sebelumnya.',
            ], 422);
        }

        return response()->json([
            'success'  => true,
            'message'  => 'Terima kasih! Suara Anda berhasil disimpan.',
            'redirect' => route('list.event'),
        ]);
    }

    /**
     * Tampilkan hasil voting event (tanpa TOPSIS).
     */
    public function eventResult(Request $request)
    {
        $eventId = $this->resolveEventId($request);

        if (!$eventId) {
            return redirect()->route('scan.event')->with('error', 'ID event tidak ditemukan.');
        }

        $event = DB::table('events')->where('id', $eventId)->first();

        if (!$event) {
            return redirect()->route('scan.event')->with('error', 'Event tidak ditemukan.');
        }

        // 1. Total suara masuk pada event ini
        $totalVotes = DB::table('votes')->where('event_id', $event->id)->count();

        // 2. Total peserta (jika event private, ambil dari tabel participants; jika public, gunakan total votes)
        if ($event->voting_type === 'private') {
            $totalParticipants = DB::table('participants')->where('event_id', $event->id)->count();
            $totalVoted = DB::table('participants')->where('event_id', $event->id)->where('has_voted', 1)->count();
        } else {
            $totalParticipants = $totalVotes;
            $totalVoted = $totalVotes;
        }

        // 3. Ambil perolehan kandidat beserta peringkat
        $candidates = DB::table('candidates as c')
            ->leftJoin('votes as v', 'v.candidate_id', '=', 'c.id')
            ->where('c.event_id', $event->id)
            ->groupBy('c.id', 'c.name', 'c.number', 'c.biodata', 'c.photo_url', 'c.class_or_meta')
            ->select(
                'c.id as candidate_id',
                'c.name',
                'c.number',
                'c.biodata',
                'c.photo_url',
                'c.class_or_meta',
                DB::raw('COUNT(v.id) as votes')
            )
            ->orderByDesc('votes')
            ->orderBy('c.number', 'asc')
            ->get();

        $results = collect();
        $rank = 1;

        foreach ($candidates as $cand) {
            $percentage = $totalVotes > 0 ? round(($cand->votes / $totalVotes) * 100, 1) : 0;

            $results->push((object)[
                'rank' => $rank++,
                'votes' => $cand->votes,
                'percentage' => $percentage,
                'candidate' => (object)[
                    'id' => $cand->candidate_id,
                    'name' => $cand->name,
                    'number' => $cand->number,
                    'biodata' => $cand->biodata,
                    'photo_url' => $cand->photo_url,
                    'class_or_meta' => $cand->class_or_meta,
                ]
            ]);
        }

        // 4. Pemenang utama (Rank 1 dengan suara > 0)
        $winner = null;
        if ($results->isNotEmpty() && $results->first()->votes > 0) {
            $winner = $results->first();
        }

        // 5. Pemenang Kategori
        //    Admin memilih satu kategori penentu (determining_category_id).
        //    Sistem mencari kandidat dengan total skor tertinggi pada kategori
        //    tersebut dari seluruh vote yang masuk.
        $categoryWinners = collect();

        if ((int) $event->pemenang_kategori_enabled === 1
            && (int) $event->categories_enabled === 1
            && !empty($event->determining_category_id)) {

            $determiningCategory = DB::table('categories')
                ->where('id', $event->determining_category_id)
                ->first();

            if ($determiningCategory) {
                $rows = DB::table('ratings as r')
                    ->join('votes as v', 'v.id', '=', 'r.vote_id')
                    ->join('candidates as c', 'c.id', '=', 'v.candidate_id')
                    ->where('v.event_id', $event->id)
                    ->where('r.category_id', $determiningCategory->id)
                    ->groupBy('c.id', 'c.name', 'c.number')
                    ->select(
                        'c.id as candidate_id',
                        'c.name as candidate_name',
                        'c.number as candidate_number',
                        DB::raw('SUM(r.score) as total_score')
                    )
                    ->orderByDesc('total_score')
                    ->orderBy('c.number', 'asc')
                    ->get();

                if ($rows->isNotEmpty()) {
                    $categoryWinners->push((object)[
                        'category_name' => $determiningCategory->name,
                        'candidate_name' => $rows->first()->candidate_name,
                        'candidate_number' => $rows->first()->candidate_number,
                        'points' => $rows->first()->total_score,
                    ]);
                }
            }
        }

        // 6. Juara Harapan
        //    Juara harapan = kandidat di peringkat 4 dst. yang memiliki suara > 0.
        //    Jika tidak ada kandidat di posisi harapan yang punya suara, atau
        //    jika kandidat di posisi harapan memiliki suara yang sama (seri),
        //    maka juara harapan tidak dapat ditentukan.
        $juaraHarapan = collect();
        $juaraHarapanEnabled = (int) $event->juara_harapan_enabled === 1;
        $juaraHarapanCount = (int) $event->juara_harapan_count;
        $juaraHarapanTied = false;

        if ($juaraHarapanEnabled && $juaraHarapanCount > 0) {
            // Kandidat di luar top 3 (rank > 3) yang punya suara > 0
            $harapanCandidates = $results->filter(function ($row) {
                return $row->rank > 3 && $row->votes > 0;
            })->values();

            if ($harapanCandidates->isNotEmpty()) {
                // Cek apakah ada seri di antara kandidat harapan
                $uniqueVotes = $harapanCandidates->pluck('votes')->unique();

                if ($uniqueVotes->count() === $harapanCandidates->count()) {
                    // Tidak ada seri — ambil sebanyak juara_harapan_count
                    $juaraHarapan = $harapanCandidates->take($juaraHarapanCount);
                } else {
                    // Ada seri — juara harapan tidak dapat ditentukan
                    $juaraHarapanTied = true;
                }
            } else {
                // Tidak ada kandidat harapan dengan suara > 0
                // Cek apakah kandidat di rank > 3 semuanya 0 suara (setara)
                $zeroVoteCandidates = $results->filter(function ($row) {
                    return $row->rank > 3 && $row->votes === 0;
                })->values();

                if ($zeroVoteCandidates->count() > 1) {
                    $juaraHarapanTied = true;
                }
            }
        }

        return view('event-result', compact(
            'event',
            'results',
            'winner',
            'totalVotes',
            'totalParticipants',
            'totalVoted',
            'categoryWinners',
            'juaraHarapan',
            'juaraHarapanEnabled',
            'juaraHarapanCount',
            'juaraHarapanTied'
        ));
    }
}