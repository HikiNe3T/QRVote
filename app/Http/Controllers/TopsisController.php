<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\TopsisService;

class TopsisController extends Controller
{
    /**
     * Tampilkan hasil perhitungan TOPSIS untuk sebuah event.
     * Kriteria yang dipakai:
     *   1. Jumlah suara (votes)        — benefit, bobot = topsis_vote_weight
     *   2..n. Rata-rata nilai per kategori — benefit, bobot = weight kategori
     */
    public function result(Request $request)
    {
        $eventId = $request->query('event_id');

        if (!$eventId) {
            return redirect()->route('scan.event')->with('error', 'ID event tidak ditemukan.');
        }

        $event = DB::table('events')->where('id', $eventId)->first();

        if (!$event) {
            return redirect()->route('scan.event')->with('error', 'Event tidak ditemukan.');
        }

        // Ambil kandidat
        $candidates = DB::table('candidates')
            ->where('event_id', $event->id)
            ->orderBy('number', 'asc')
            ->get();

        // Ambil kategori (jika aktif)
        $useCategories = (int) $event->categories_enabled === 1;
        $categories = $useCategories
            ? DB::table('categories')->where('event_id', $event->id)->orderBy('created_at')->get()
            : collect();

        // Bobot suara dari kolom topsis_vote_weight (default 30 jika NULL)
        $voteWeight = $event->topsis_vote_weight !== null
            ? (float) $event->topsis_vote_weight
            : 30.0;

        // ====================================================
        // BUILD CRITERIA DEFINITION
        // ====================================================
        $criteria = []; // [ ['key' => ..., 'label' => ..., 'weight' => ..., 'benefit' => true] ]
        $weights  = [];
        $benefit  = [];

        $criteria[] = [
            'key'    => 'votes',
            'label'  => 'Jumlah Suara',
            'weight' => $voteWeight,
            'benefit'=> true,
        ];

        foreach ($categories as $cat) {
            $criteria[] = [
                'key'    => 'cat_' . $cat->id,
                'label'  => 'Nilai: ' . $cat->name,
                'weight' => (float) $cat->weight,
                'benefit'=> true,
            ];
        }

        // Normalisasi bobot agar total = 1
        $totalWeight = array_sum(array_column($criteria, 'weight'));

        if ($totalWeight <= 0) {
            // fallback: bobot merata
            $n = count($criteria);
            foreach ($criteria as &$c) {
                $c['weight'] = 1.0 / $n;
            }
            unset($c);
        } else {
            foreach ($criteria as &$c) {
                $c['weight'] = $c['weight'] / $totalWeight;
            }
            unset($c);
        }

        foreach ($criteria as $c) {
            $weights[] = $c['weight'];
            $benefit[] = $c['benefit'];
        }

        // ====================================================
        // BUILD DECISION MATRIX
        // matrix[i][j] = nilai kandidat-i pada kriteria-j
        // ====================================================
        // 1. Hitung jumlah suara per kandidat
        $voteCounts = DB::table('votes')
            ->select('candidate_id', DB::raw('COUNT(*) as total'))
            ->where('event_id', $event->id)
            ->groupBy('candidate_id')
            ->pluck('total', 'candidate_id');

        // 2. Hitung rata-rata skor per kandidat per kategori
        //    (AVG of ratings.score grouped by candidate + category)
        $avgScores = [];
        if ($categories->isNotEmpty()) {
            $rows = DB::table('ratings as r')
                ->join('votes as v', 'v.id', '=', 'r.vote_id')
                ->where('v.event_id', $event->id)
                ->select(
                    'v.candidate_id',
                    'r.category_id',
                    DB::raw('AVG(r.score) as avg_score')
                )
                ->groupBy('v.candidate_id', 'r.category_id')
                ->get();

            foreach ($rows as $row) {
                $avgScores[$row->candidate_id][$row->category_id] = (float) $row->avg_score;
            }
        }

        // 3. Susun matrix
        $matrix = [];
        $candidateMeta = [];

        foreach ($candidates as $idx => $cand) {
            $row = [];
            foreach ($criteria as $c) {
                if ($c['key'] === 'votes') {
                    $row[] = (float) ($voteCounts[$cand->id] ?? 0);
                } elseif (str_starts_with($c['key'], 'cat_')) {
                    $catId = substr($c['key'], 4);
                    $row[] = $avgScores[$cand->id][$catId] ?? 0.0;
                } else {
                    $row[] = 0.0;
                }
            }
            $matrix[] = $row;

            $candidateMeta[] = [
                'id'            => $cand->id,
                'name'          => $cand->name,
                'number'        => $cand->number,
                'biodata'       => $cand->biodata,
                'photo_url'     => $cand->photo_url,
                'class_or_meta' => $cand->class_or_meta,
            ];
        }

        // ====================================================
        // RUN TOPSIS
        // ====================================================
        $result = TopsisService::calculate($matrix, $weights, $benefit);

        // ====================================================
        // SUSUN OUTPUT UNTUK VIEW
        // ====================================================
        $rankings = [];

        for ($i = 0; $i < count($candidateMeta); $i++) {
            $rankings[] = [
                'candidate'  => $candidateMeta[$i],
                'values'     => $matrix[$i],
                'closeness'  => $result['closeness'][$i] ?? 0,
                'rank'       => $result['rank'][$i] ?? 0,
                'dPlus'      => $result['dPlus'][$i] ?? 0,
                'dMinus'     => $result['dMinus'][$i] ?? 0,
                'normalized' => $result['normalized'][$i] ?? [],
                'weighted'   => $result['weighted'][$i] ?? [],
            ];
        }

        // Urutkan by rank asc (rank 1 di atas)
        usort($rankings, fn($a, $b) => $a['rank'] <=> $b['rank']);

        $winner = count($rankings) > 0 ? $rankings[0] : null;

        // Format tanggal
        $startDate = \Carbon\Carbon::parse($event->start_date)->translatedFormat('d F Y');
        $endDate = \Carbon\Carbon::parse($event->end_date)->translatedFormat('d F Y');
        $dateRange = $startDate === $endDate ? $startDate : $startDate . ' - ' . $endDate;

        $totalVotes = DB::table('votes')->where('event_id', $event->id)->count();

        return view('event-topsis', [
            'event'      => $event,
            'criteria'   => $criteria,
            'rankings'   => $rankings,
            'winner'     => $winner,
            'idealPlus'  => $result['idealPlus'],
            'idealMinus' => $result['idealMinus'],
            'dateRange'  => $dateRange,
            'totalVotes' => $totalVotes,
            'voteWeight' => $voteWeight,
        ]);
    }
}