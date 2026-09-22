<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BusinessDocumentRequirementTest extends TestCase
{
    use RefreshDatabase;

    public function test_inactive_required_document_type_does_not_block_submission(): void
    {
        DocumentType::query()->update(['status' => 'inactive']);

        DocumentType::create([
            'name' => 'Retired Requirement',
            'is_required' => true,
            'status' => 'inactive',
            'country_code' => 'NG',
        ]);

        $owner = User::factory()->create();
        $business = Business::factory()->for($owner)->create([
            'status' => 'draft',
            'country_code' => 'NG',
        ]);

        $this->actingAs($owner)
            ->post(route('business-owner.businesses.submit', $business))
            ->assertSessionHas('success');

        $this->assertSame('submitted', $business->refresh()->status);
    }

    public function test_document_checklist_only_contains_active_requirements_for_the_business_country(): void
    {
        DocumentType::query()->delete();

        DocumentType::create([
            'name' => 'All-country Requirement',
            'is_required' => true,
            'status' => 'active',
            'country_code' => null,
        ]);
        DocumentType::create([
            'name' => 'Nigeria Requirement',
            'is_required' => true,
            'status' => 'active',
            'country_code' => 'NG',
        ]);
        DocumentType::create([
            'name' => 'Benin Requirement',
            'is_required' => true,
            'status' => 'active',
            'country_code' => 'BJ',
        ]);
        DocumentType::create([
            'name' => 'Inactive Nigeria Requirement',
            'is_required' => true,
            'status' => 'inactive',
            'country_code' => 'NG',
        ]);

        $owner = User::factory()->create();
        $business = Business::factory()->for($owner)->create(['country_code' => 'NG']);

        $this->actingAs($owner)
            ->get(route('business-owner.businesses.documents.index', $business))
            ->assertOk()
            ->assertSeeText('All-country Requirement')
            ->assertSeeText('Nigeria Requirement')
            ->assertDontSeeText('Benin Requirement')
            ->assertDontSeeText('Inactive Nigeria Requirement')
            ->assertSeeText('0 of 2 required documents uploaded');
    }

    public function test_business_cannot_upload_an_inactive_or_foreign_country_document_type(): void
    {
        Storage::fake('private');
        DocumentType::query()->delete();

        $inactive = DocumentType::create([
            'name' => 'Inactive Requirement',
            'is_required' => true,
            'status' => 'inactive',
            'country_code' => 'NG',
        ]);
        $foreign = DocumentType::create([
            'name' => 'Benin Requirement',
            'is_required' => true,
            'status' => 'active',
            'country_code' => 'BJ',
        ]);

        $owner = User::factory()->create();
        $business = Business::factory()->for($owner)->create(['country_code' => 'NG']);

        foreach ([$inactive, $foreign] as $documentType) {
            $this->actingAs($owner)
                ->post(route('business-owner.businesses.documents.store', $business), [
                    'document_type_id' => $documentType->id,
                    'file' => UploadedFile::fake()->create('certificate.pdf', 100, 'application/pdf'),
                ])
                ->assertInvalid(['document_type_id']);
        }

        $this->assertCount(0, $business->documents);
    }

    public function test_correction_resubmission_is_blocked_while_a_required_document_is_missing(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->for($owner)->create([
            'status' => 'correction_required',
            'country_code' => 'NG',
        ]);

        $this->actingAs($owner)
            ->post(route('business-owner.businesses.correction-response', $business), [
                'note' => 'I have updated the business details.',
            ])
            ->assertSessionHas('error', 'Please upload all required documents before resubmitting.');

        $this->assertSame('correction_required', $business->refresh()->status);
    }

    public function test_super_admin_can_create_a_country_scoped_document_requirement(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin)
            ->post(route('super-admin.document-types.store'), [
                'name' => 'RCCM Registration Certificate',
                'description' => 'Commercial registration evidence for businesses registered in Benin.',
                'is_required' => 1,
                'status' => 'active',
                'country_code' => 'BJ',
            ])
            ->assertRedirect(route('super-admin.document-types.index'));

        $this->assertDatabaseHas('document_types', [
            'name' => 'RCCM Registration Certificate',
            'country_code' => 'BJ',
            'is_required' => true,
        ]);
    }

    public function test_document_type_names_must_be_unique(): void
    {
        DocumentType::query()->delete();

        DocumentType::create([
            'name' => 'Custom Compliance Record',
            'is_required' => true,
            'status' => 'active',
        ]);

        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin)
            ->post(route('super-admin.document-types.store'), [
                'name' => 'Custom Compliance Record',
                'is_required' => 1,
                'status' => 'active',
                'country_code' => null,
            ])
            ->assertInvalid(['name']);

        $this->assertDatabaseCount('document_types', 1);
    }
}
