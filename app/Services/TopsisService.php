<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class TopsisService
{
    /**
     * Hitung ranking kandidat dengan metode TOPSIS.
     *
     * Alternatif  = kandidat
     * Kriteria    = categories (semua benefit / makin besar makin baik)
     * Nilai x_ij  = rata-rata skor yang diberikan seluruh voter
     *               untuk kandidat i pada kategori j
     *
     * @return array<int, array{candidate_id:string, name:string, number:int,
     *               scores:array<string,float>, dplus:float, dminus:float,
     *               preference:float, rank:int}>
     */
    public function rank(string $eventId): array
    {
        $categories = DB::table('categories')
            ->where('event_id', $eventId)
            ->orderBy('created_at')
            ->get();

        $candidates = DB::table('candidates')
            ->where('event_id', $eventId)
            ->orderBy('number')
            ->get();

        if ($categories->isEmpty() || $candidates->isEmpty()) {
            return [];
        }

        // 1) Matriks keputusan: rata-rata skor per kandidat per kategori
        $rows = DB::table('votes as v')
            ->join('ratings as r', 'r.vote_id', '=', 'v.id')
            ->where('v.event_id', $eventId)
            ->groupBy('v.candidate_id', 'r.category_id')
            ->select('v.candidate_id', 'r.category_id', DB::raw('AVG(r.score) as avg_score'))
            ->get();

        $matrix = [];
        foreach ($candidates as $c) {
            foreach ($categories as $cat) {
                $matrix[$c->id][$cat->id] = 0.0;
            }
        }
        foreach ($rows as $row) {
            if (isset($matrix[$row->candidate_id][$row->category_id])) {
                $matrix[$row->candidate_id][$row->category_id] = (float) $row->avg_score;
            }
        }

        // 2) Bobot ternormalisasi (total = 1). Kalau semua bobot 0 -> bobot sama.
        $totalWeight = 0.0;
        foreach ($categories as $cat) {
            $totalWeight += (float) $cat->weight;
        }
        $weights = [];
        foreach ($categories as $cat) {
            $weights[$cat->id] = $totalWeight > 0
                ? ((float) $cat->weight / $totalWeight)
                : (1 / max(1, $categories->count()));
        }

        // 3) Normalisasi vektor: r_ij = x_ij / sqrt(sum(x_ij^2))
        $divisor = [];
        foreach ($categories as $cat) {
            $sum = 0.0;
            foreach ($candidates as $c) {
                $sum += pow($matrix[$c->id][$cat->id], 2);
            }
            $divisor[$cat->id] = sqrt($sum);
        }

        // 4) Matriks terbobot y_ij = w_j * r_ij
        $weighted = [];
        foreach ($candidates as $c) {
            foreach ($categories as $cat) {
                $r = $divisor[$cat->id] > 0
                    ? $matrix[$c->id][$cat->id] / $divisor[$cat->id]
                    : 0.0;
                $weighted[$c->id][$cat->id] = $weights[$cat->id] * $r;
            }
        }

        // 5) Solusi ideal positif (A+) & negatif (A-) — semua kriteria benefit
        $aPlus = $aMinus = [];
        foreach ($categories as $cat) {
            $col = [];
            foreach ($candidates as $c) {
                $col[] = $weighted[$c->id][$cat->id];
            }
            $aPlus[$cat->id]  = max($col);
            $aMinus[$cat->id] = min($col);
        }

        // 6) Jarak Euclidean + nilai preferensi
        $result = [];
        foreach ($candidates as $c) {
            $dPlus = $dMinus = 0.0;
            foreach ($categories as $cat) {
                $dPlus  += pow($weighted[$c->id][$cat->id] - $aPlus[$cat->id], 2);
                $dMinus += pow($weighted[$c->id][$cat->id] - $aMinus[$cat->id], 2);
            }
            $dPlus  = sqrt($dPlus);
            $dMinus = sqrt($dMinus);

            $result[] = [
                'candidate_id' => $c->id,
                'name'         => $c->name,
                'number'       => (int) $c->number,
                'scores'       => $matrix[$c->id],
                'dplus'        => round($dPlus, 6),
                'dminus'       => round($dMinus, 6),
                'preference'   => ($dPlus + $dMinus) > 0
                    ? round($dMinus / ($dPlus + $dMinus), 6)
                    : 0.0,
            ];
        }

        // 7) Ranking
        usort($result, fn ($a, $b) => $b['preference'] <=> $a['preference']);
        foreach ($result as $i => &$r) {
            $r['rank'] = $i + 1;
        }

        return $result;
    }

    /** Pemenang per kategori (skor rata-rata tertinggi), disimpan ke category_winners. */
    public function saveCategoryWinners(string $eventId): void
    {
        $categories = DB::table('categories')->where('event_id', $eventId)->get();

        foreach ($categories as $cat) {
            $best = DB::table('votes as v')
                ->join('ratings as r', 'r.vote_id', '=', 'v.id')
                ->where('v.event_id', $eventId)
                ->where('r.category_id', $cat->id)
                ->groupBy('v.candidate_id')
                ->select('v.candidate_id', DB::raw('AVG(r.score) as avg_score'))
                ->orderByDesc('avg_score')
                ->first();

            if (!$best) continue;

            DB::table('category_winners')
                ->where('event_id', $eventId)
                ->where('category_id', $cat->id)
                ->delete();

            DB::table('category_winners')->insert([
                'id'           => (string) \Illuminate\Support\Str::uuid(),
                'event_id'     => $eventId,
                'category_id'  => $cat->id,
                'candidate_id' => $best->candidate_id,
                'total_points' => round((float) $best->avg_score, 2),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }
}
