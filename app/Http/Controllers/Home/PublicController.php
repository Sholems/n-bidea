<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Post;
use App\Models\Publication;
use App\Models\Sector;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function index(): View
    {
        $platformStats = [
            'verifiedBusinesses' => Business::query()->whereIn('status', ['approved', 'verified'])->count(),
            'activeSectors' => Sector::query()->where('status', 'active')->count(),
        ];

        $latestPosts = Post::query()
            ->with('category')
            ->published()
            ->latest('published_at')
            ->limit(3)
            ->get();

        $latestPublications = Publication::query()
            ->with('category')
            ->published()
            ->latest('published_at')
            ->limit(2)
            ->get();

        return view('home', compact('platformStats', 'latestPosts', 'latestPublications'));
    }
}
