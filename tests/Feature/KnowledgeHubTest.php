<?php

namespace Tests\Feature;

use App\Models\ContentCategory;
use App\Models\Post;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KnowledgeHubTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_only_see_published_posts_and_publications(): void
    {
        $category = ContentCategory::factory()->create(['name' => 'Corridor Intelligence']);
        Post::factory()->published()->for($category, 'category')->create([
            'title' => 'Nigeria-Benin Corridor Outlook',
            'excerpt' => 'Trade intelligence for the corridor.',
        ]);
        Post::factory()->draft()->for($category, 'category')->create([
            'title' => 'Draft Policy Memo',
        ]);
        Publication::factory()->published()->for($category, 'category')->create([
            'title' => 'AfCFTA Border Guide',
        ]);
        Publication::factory()->draft()->for($category, 'category')->create([
            'title' => 'Draft Customs Report',
        ]);

        $this->get(route('public.resources'))
            ->assertOk()
            ->assertSeeText('Knowledge Hub')
            ->assertSeeText('Nigeria-Benin Corridor Outlook')
            ->assertSeeText('AfCFTA Border Guide')
            ->assertDontSeeText('Draft Policy Memo')
            ->assertDontSeeText('Draft Customs Report');
    }

    public function test_guest_can_read_published_post_but_not_draft_post(): void
    {
        $publishedPost = Post::factory()->published()->create([
            'title' => 'Export Readiness Checklist',
            'slug' => 'export-readiness-checklist',
            'body' => 'Practical steps for corridor exporters.',
        ]);
        $draftPost = Post::factory()->draft()->create([
            'title' => 'Internal Draft',
            'slug' => 'internal-draft',
        ]);

        $this->get(route('public.blog.show', $publishedPost))
            ->assertOk()
            ->assertSeeText('Export Readiness Checklist')
            ->assertSeeText('Practical steps for corridor exporters.');

        $this->get(route('public.blog.show', $draftPost))
            ->assertNotFound();
    }

    public function test_super_admin_can_create_a_published_post_with_unique_slug(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'account_status' => 'active',
        ]);
        $category = ContentCategory::factory()->create();
        Post::factory()->create(['slug' => 'customs-compliance-update']);

        $this->actingAs($superAdmin)
            ->post(route('super-admin.posts.store'), [
                'content_category_id' => $category->id,
                'title' => 'Customs Compliance Update',
                'slug' => 'customs-compliance-update',
                'excerpt' => 'Latest compliance guidance.',
                'body' => 'Border teams should prepare updated documentation.',
                'status' => 'published',
                'audience' => 'public',
                'published_at' => now()->format('Y-m-d\TH:i'),
            ])
            ->assertInvalid(['slug']);

        $this->actingAs($superAdmin)
            ->post(route('super-admin.posts.store'), [
                'content_category_id' => $category->id,
                'title' => 'Customs Compliance Update',
                'slug' => 'customs-compliance-update-2026',
                'excerpt' => 'Latest compliance guidance.',
                'body' => 'Border teams should prepare updated documentation.',
                'status' => 'published',
                'audience' => 'public',
                'published_at' => now()->format('Y-m-d\TH:i'),
            ])
            ->assertRedirect(route('super-admin.posts.index'));

        $this->assertDatabaseHas('posts', [
            'slug' => 'customs-compliance-update-2026',
            'status' => 'published',
        ]);
    }

    public function test_publication_download_increments_count_without_showing_private_path(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('publications/afcfta-guide.pdf', 'PDF content');
        $publication = Publication::factory()->published()->create([
            'title' => 'AfCFTA Practical Guide',
            'slug' => 'afcfta-practical-guide',
            'file_path' => 'publications/afcfta-guide.pdf',
            'download_count' => 0,
        ]);

        $this->get(route('public.publications.show', $publication))
            ->assertOk()
            ->assertSeeText('AfCFTA Practical Guide')
            ->assertDontSeeText('publications/afcfta-guide.pdf');

        $this->get(route('public.publications.download', $publication))
            ->assertDownload('AfCFTA Practical Guide.pdf');

        $this->assertSame(1, $publication->refresh()->download_count);
    }
}
