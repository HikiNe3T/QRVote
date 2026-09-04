<?php

namespace App\Services;

/**
 * Pure PHP implementation of the TOPSIS (Technique for Order
 * Preference by Similarity to Ideal Solution) algorithm.
 *
 * Input  : decision matrix (m alternatives x n criteria),
 *          weights (length n, summing to 1),
 *          benefit flags (length n, true = benefit / larger-is-better).
 * Output : closeness coefficient + ranking per alternative.
 */
class TopsisService
{
    /**
     * Run TOPSIS over a decision matrix.
     *
     * @param array  $matrix  Rows = alternatives, cols = criteria.
     *                        $matrix[i][j] = numeric value.
     * @param array  $weights Normalized weights, length = n criteria.
     * @param array  $benefit Boolean per criterion: true = benefit,
     *                        false = cost (smaller-is-better).
     * @return array {
     *     @var array  $closeness   Closeness coefficient per alternative (0..1).
     *     @var array  $rank        Rank per alternative (1 = best).
     *     @var array  $dPlus       Separation from ideal-positive.
     *     @var array  $dMinus      Separation from ideal-negative.
     *     @var array  $normalized  Normalized matrix.
     *     @var array  $weighted    Weighted normalized matrix.
     *     @var array  $idealPlus   Ideal-positive solution per criterion.
     *     @var array  $idealMinus  Ideal-negative solution per criterion.
     * }
     */
    public static function calculate(array $matrix, array $weights, array $benefit): array
    {
        $m = count($matrix);          // alternatives
        $n = count($weights);         // criteria

        if ($m === 0 || $n === 0) {
            return self::emptyResult($n);
        }

        // -----------------------------------------------------------
        // Step 1 — Vector normalization:  r_ij = x_ij / sqrt(sum x^2)
        // -----------------------------------------------------------
        $normalized = array_fill(0, $m, array_fill(0, $n, 0.0));

        for ($j = 0; $j < $n; $j++) {
            $sumSquares = 0.0;
            for ($i = 0; $i < $m; $i++) {
                $val = (float) ($matrix[$i][$j] ?? 0);
                $sumSquares += $val * $val;
            }
            $denom = sqrt($sumSquares);
            for ($i = 0; $i < $m; $i++) {
                $val = (float) ($matrix[$i][$j] ?? 0);
                $normalized[$i][$j] = $denom > 0 ? $val / $denom : 0.0;
            }
        }

        // -----------------------------------------------------------
        // Step 2 — Apply weights:  v_ij = w_j * r_ij
        // -----------------------------------------------------------
        $weighted = array_fill(0, $m, array_fill(0, $n, 0.0));

        for ($i = 0; $i < $m; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $weighted[$i][$j] = $normalized[$i][$j] * (float) $weights[$j];
            }
        }

        // -----------------------------------------------------------
        // Step 3 — Ideal positive (A+) & ideal negative (A-)
        // -----------------------------------------------------------
        $idealPlus  = array_fill(0, $n, 0.0);
        $idealMinus = array_fill(0, $n, 0.0);

        for ($j = 0; $j < $n; $j++) {
            $column = array_column($weighted, $j);

            if (empty($column)) {
                $idealPlus[$j]  = 0.0;
                $idealMinus[$j] = 0.0;
                continue;
            }

            $max = max($column);
            $min = min($column);

            if ($benefit[$j] ?? true) {
                $idealPlus[$j]  = $max;   // benefit: best = max
                $idealMinus[$j] = $min;   // worst  = min
            } else {
                $idealPlus[$j]  = $min;   // cost: best = min
                $idealMinus[$j] = $max;   // worst  = max
            }
        }

        // -----------------------------------------------------------
        // Step 4 — Separation measures D+ and D-
        // -----------------------------------------------------------
        $dPlus  = array_fill(0, $m, 0.0);
        $dMinus = array_fill(0, $m, 0.0);

        for ($i = 0; $i < $m; $i++) {
            $sumPlus  = 0.0;
            $sumMinus = 0.0;

            for ($j = 0; $j < $n; $j++) {
                $sumPlus  += pow($weighted[$i][$j] - $idealPlus[$j], 2);
                $sumMinus += pow($weighted[$i][$j] - $idealMinus[$j], 2);
            }

            $dPlus[$i]  = sqrt($sumPlus);
            $dMinus[$i] = sqrt($sumMinus);
        }

        // -----------------------------------------------------------
        // Step 5 — Closeness coefficient: C_i = D- / (D+ + D-)
        // -----------------------------------------------------------
        $closeness = array_fill(0, $m, 0.0);

        for ($i = 0; $i < $m; $i++) {
            $denom = $dPlus[$i] + $dMinus[$i];
            $closeness[$i] = $denom > 0 ? $dMinus[$i] / $denom : 0.0;
        }

        // -----------------------------------------------------------
        // Step 6 — Rank (1 = best, highest closeness)
        // -----------------------------------------------------------
        $rank = self::rankDescending($closeness);

        return [
            'closeness'  => $closeness,
            'rank'       => $rank,
            'dPlus'      => $dPlus,
            'dMinus'     => $dMinus,
            'normalized' => $normalized,
            'weighted'   => $weighted,
            'idealPlus'  => $idealPlus,
            'idealMinus' => $idealMinus,
        ];
    }

    /**
     * Assign ranks so that the highest value gets rank 1.
     * Ties receive the same rank (standard competition ranking).
     */
    private static function rankDescending(array $values): array
    {
        $m = count($values);
        $rank = array_fill(0, $m, 1);

        for ($i = 0; $i < $m; $i++) {
            for ($j = 0; $j < $m; $j++) {
                if ($values[$j] > $values[$i]) {
                    $rank[$i]++;
                }
            }
        }

        return $rank;
    }

    private static function emptyResult(int $n): array
    {
        return [
            'closeness'  => [],
            'rank'       => [],
            'dPlus'      => [],
            'dMinus'     => [],
            'normalized' => [],
            'weighted'   => [],
            'idealPlus'  => array_fill(0, $n, 0.0),
            'idealMinus' => array_fill(0, $n, 0.0),
        ];
    }
}