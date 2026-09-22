<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\View\View;

class BusinessVerificationController extends Controller
{
    public function show(Business $business): View
    {
        $this->authorize('view', $business);

        $business->load(['verificationReviews.admin', 'fees', 'certificate']);

        $verificationReviews = $business->verificationReviews;
        $fees = $business->fees;

        return view('business.verification', compact('business', 'verificationReviews', 'fees'));
    }
}
