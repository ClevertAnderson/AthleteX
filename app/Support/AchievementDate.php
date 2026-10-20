<?php

namespace App\Support;

use Carbon\Carbon;

/**
 * Works out the real "date achieved" of an achievement row (athlete OR coach)
 * from its `year` + `month_day` columns.
 *
 * Falls back to created_at if month_day can't be parsed, so sorting and
 * filtering never break on odd data.
 */
class AchievementDate
{
    public static function from($item): Carbon
    {
        $year     = trim((string) ($item->year ?? ''));
        $monthDay = trim((string) ($item->month_day ?? ''));
        $hasYear  = preg_match('/^\d{4}$/', $year) === 1;

        $date = null;

        if ($monthDay !== '') {
            try {
                $date = Carbon::parse($monthDay);
            } catch (\Throwable $e) {
                $date = null;
            }
        }

        if (! $date) {
            $created = $item->created_at ?? null;
            $date = $created instanceof Carbon
                ? $created
                : ($created ? Carbon::parse($created) : Carbon::createFromTimestamp(0));
        }

        // The `year` column is the source of truth for the year
        if ($hasYear) {
            $date = $date->copy()->year((int) $year);
        }

        return $date->copy()->startOfDay();
    }
}