<div class="card">
    <div class="card-body">
        <form action="{{ $action }}" method="POST">
            @csrf
            @if($method !== 'POST')
                @method($method)
            @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $post?->title) }}" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                    <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $post?->slug) }}" required>
                    @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="content_category_id" class="form-label">Category</label>
                    <select name="content_category_id" id="content_category_id" class="form-select">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ (string) old('content_category_id', $post?->content_category_id) === (string) $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="audience" class="form-label">Audience <span class="text-danger">*</span></label>
                    <select name="audience" id="audience" class="form-select" required>
                        @foreach(['public', 'members', 'internal'] as $audience)
                            <option value="{{ $audience }}" {{ old('audience', $post?->audience ?? 'public') === $audience ? 'selected' : '' }}>{{ ucfirst($audience) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select" required>
                        @foreach(['draft', 'published'] as $status)
                            <option value="{{ $status }}" {{ old('status', $post?->status ?? 'draft') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="published_at" class="form-label">Published At</label>
                    <input type="datetime-local" name="published_at" id="published_at" class="form-control" value="{{ old('published_at', $post?->published_at?->format('Y-m-d\TH:i')) }}">
                </div>
                <div class="col-12">
                    <label for="excerpt" class="form-label">Excerpt</label>
                    <textarea name="excerpt" id="excerpt" rows="3" class="form-control">{{ old('excerpt', $post?->excerpt) }}</textarea>
                </div>
                <div class="col-12">
                    <label for="body" class="form-label">Body <span class="text-danger">*</span></label>
                    <textarea name="body" id="body" rows="10" class="form-control @error('body') is-invalid @enderror" required>{{ old('body', $post?->body) }}</textarea>
                    @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <hr class="my-4">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('super-admin.posts.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Post</button>
            </div>
        </form>
    </div>
</div>
