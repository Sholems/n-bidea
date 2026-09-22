<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Models\ContentCategory;
use App\Models\Post;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SuperAdminPostController extends Controller
{
    public function index(): View
    {
        $this->authorize('is-super-admin');

        $posts = Post::with('category', 'author')->latest()->paginate(20);

        return view('super-admin.posts.index', compact('posts'));
    }

    public function create(): View
    {
        $this->authorize('is-super-admin');

        $categories = ContentCategory::where('status', 'active')->orderBy('name')->get();

        return view('super-admin.posts.create', compact('categories'));
    }

    public function store(PostRequest $request): RedirectResponse
    {
        $this->authorize('is-super-admin');

        $post = Post::create($request->validated() + [
            'author_id' => auth()->id(),
        ]);

        AuditService::logAction(
            action: 'post.created',
            description: "Post '{$post->title}' created by super admin",
            auditable: $post,
        );

        return redirect()->route('super-admin.posts.index')
            ->with('success', 'Post saved successfully.');
    }

    public function show(Post $post): View
    {
        $this->authorize('is-super-admin');

        return view('super-admin.posts.show', compact('post'));
    }

    public function edit(Post $post): View
    {
        $this->authorize('is-super-admin');

        $categories = ContentCategory::where('status', 'active')->orderBy('name')->get();

        return view('super-admin.posts.edit', compact('post', 'categories'));
    }

    public function update(PostRequest $request, Post $post): RedirectResponse
    {
        $this->authorize('is-super-admin');

        $post->update($request->validated());

        AuditService::logAction(
            action: 'post.updated',
            description: "Post '{$post->title}' updated by super admin",
            auditable: $post,
        );

        return redirect()->route('super-admin.posts.index')
            ->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('is-super-admin');

        $title = $post->title;
        $post->delete();

        AuditService::logAction(
            action: 'post.deleted',
            description: "Post '{$title}' deleted by super admin",
        );

        return redirect()->route('super-admin.posts.index')
            ->with('success', 'Post deleted successfully.');
    }
}
