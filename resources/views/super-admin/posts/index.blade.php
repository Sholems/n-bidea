@extends('layouts.dashboard')

@section('title', 'Manage Posts')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Manage Posts</h1>
    <a href="{{ route('super-admin.posts.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Add Post
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($posts->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-newspaper fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">No Posts Found</h5>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Published</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($posts as $post)
                            <tr>
                                <td class="fw-semibold">{{ $post->title }}</td>
                                <td>{{ $post->category?->name ?? '-' }}</td>
                                <td><span class="badge bg-{{ $post->status === 'published' ? 'success' : 'secondary' }}">{{ ucfirst($post->status) }}</span></td>
                                <td>{{ $post->published_at?->format('d M Y') ?? '-' }}</td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('super-admin.posts.show', $post) }}" class="btn btn-outline-primary"><i class="bi bi-eye"></i></a>
                                        <a href="{{ route('super-admin.posts.edit', $post) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                        <form action="{{ route('super-admin.posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Delete this post?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @if($posts->hasPages())
        <div class="card-footer bg-white">{{ $posts->links() }}</div>
    @endif
</div>
@endsection
