<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CertificateNumberService
{
    public static function generate(): string
    {
        $year = date('Y');
        $prefix = "NBCCI-CERT-{$year}-";

        $lastRecord = DB::table('certificates')
            ->where('certificate_number', 'like', $prefix.'%')
            ->orderByDesc('certificate_number')
            ->value('certificate_number');

        $nextNumber = $lastRecord ? ((int) substr($lastRecord, -6)) + 1 : 1;

        return $prefix.str_pad((string) $nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public static function verificationCode(): string
    {
        do {
            $code = 'VER-'.Str::upper(Str::random(12));
        } while (Certificate::where('verification_code', $code)->exists());

        return $code;
    }

    public static function issueForBusiness(Business $business, ?User $issuer = null, ?\DateTimeInterface $expiresAt = null): Certificate
    {
        $certificate = $business->certificate;
        $verificationCode = $certificate?->verification_code ?? self::verificationCode();

        return Certificate::updateOrCreate(
            ['business_id' => $business->id],
            [
                'certificate_number' => $certificate?->certificate_number ?? self::generate(),
                'verification_code' => $verificationCode,
                'status' => 'active',
                'issued_by' => $issuer?->id,
                'issued_at' => now(),
                'expires_at' => $expiresAt ?? now()->addYear(),
                'revoked_at' => null,
                'qr_payload' => route('public.verification.code', $verificationCode),
            ],
        );
    }
}
