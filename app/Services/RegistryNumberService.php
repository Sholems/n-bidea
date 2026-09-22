<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class RegistryNumberService
{
    public static function generate(): string
    {
        $year = date('Y');
        $prefix = "NBCCI-NG-{$year}-";

        $lastRecord = DB::table('businesses')
            ->where('registry_number', 'like', $prefix.'%')
            ->orderByDesc('registry_number')
            ->value('registry_number');

        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord, -6);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix.str_pad((string) $nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
