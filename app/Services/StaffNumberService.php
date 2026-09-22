<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class StaffNumberService
{
    public static function generate(): string
    {
        $year = date('Y');
        $prefix = "NBCCI-STF-{$year}-";

        $lastRecord = DB::table('staff_members')
            ->where('staff_number', 'like', $prefix.'%')
            ->orderByDesc('staff_number')
            ->value('staff_number');

        $nextNumber = $lastRecord ? ((int) substr($lastRecord, -6)) + 1 : 1;

        return $prefix.str_pad((string) $nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
