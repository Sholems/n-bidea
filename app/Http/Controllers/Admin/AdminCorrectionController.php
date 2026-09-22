<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\View\View;

class AdminCorrectionController extends Controller
{
    public function show(Business $business): View
    {
        $this->authorize('view', $business);

        $business->load([
            'verificationReviews' => fn ($query) => $query->where('decision', 'correction_required')->with('admin'),
            'documents',
        ]);

        $corrections = $business->verificationReviews;

        return view('admin.correction.show', compact('business', 'corrections'));
    }
}
