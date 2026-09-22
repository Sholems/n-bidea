<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Certificate;
use App\Models\StaffMember;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationController extends Controller
{
    public function index(): View
    {
        return view('public.verify');
    }

    public function search(Request $request): View
    {
        $validated = $request->validate([
            'query' => ['required', 'string', 'max:255'],
        ]);

        return $this->result($validated['query']);
    }

    public function show(string $verificationCode): View
    {
        return $this->result($verificationCode);
    }

    private function result(string $query): View
    {
        $normalizedQuery = trim($query);

        $staffMember = StaffMember::query()
            ->with('business')
            ->where('staff_number', $normalizedQuery)
            ->first();

        if ($staffMember) {
            return view('public.staff-verification-result', [
                'staffMember' => $staffMember,
                'isValid' => $staffMember->isValid(),
            ]);
        }

        $certificate = Certificate::query()
            ->with('business.sector')
            ->matchingVerificationQuery($normalizedQuery)
            ->first();
        $business = $certificate?->business;

        if (! $business) {
            $business = Business::query()
                ->with('sector')
                ->where('registry_number', $normalizedQuery)
                ->whereIn('status', ['approved', 'verified'])
                ->first();
        }

        return view('public.verification-result', [
            'certificate' => $certificate,
            'business' => $business,
            'isValid' => $certificate?->isValid() ?? $business?->is_verified ?? false,
        ]);
    }
}
