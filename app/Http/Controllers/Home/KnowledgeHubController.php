<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Publication;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KnowledgeHubController extends Controller
{
    public function index(): View
    {
        $posts = Post::query()
            ->with('category')
            ->published()
            ->latest('published_at')
            ->paginate(6, ['*'], 'posts_page');

        $publications = Publication::query()
            ->with('category')
            ->published()
            ->latest('published_at')
            ->paginate(6, ['*'], 'publications_page');

        return view('public.resources', compact('posts', 'publications'));
    }

    public function showPost(Post $post): View
    {
        abort_unless($post->newQuery()->whereKey($post->getKey())->published()->exists(), 404);

        $post->load('category', 'author');

        return view('public.blog.show', compact('post'));
    }

    public function showPublication(Publication $publication): View
    {
        abort_unless($publication->newQuery()->whereKey($publication->getKey())->published()->exists(), 404);

        $publication->load('category', 'uploader');

        return view('public.reports.show', compact('publication'));
    }

    public function downloadPublication(Publication $publication): StreamedResponse
    {
        abort_unless($publication->newQuery()->whereKey($publication->getKey())->published()->exists(), 404);
        abort_unless(Storage::disk('private')->exists($publication->file_path), 404);

        $publication->increment('download_count');

        return Storage::disk('private')->download(
            $publication->file_path,
            $publication->title.'.pdf',
        );
    }
}
