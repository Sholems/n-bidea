<?php

namespace App\Http\Controllers\Certificate;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function show(Certificate $certificate): View
    {
        $this->authorize('view', $certificate->business);

        $certificate->load('business.sector');

        return view('certificates.show', compact('certificate'));
    }
}
