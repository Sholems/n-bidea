<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\StaffDocument;
use App\Models\StaffDocumentType;
use App\Models\StaffMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StaffDocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_uploads_a_document_for_their_staff_member(): void
    {
        Storage::fake('private');
        [$owner, $staffMember] = $this->ownerAndStaff();
        $type = StaffDocumentType::factory()->create();

        $this->actingAs($owner)
            ->post(route('business-owner.staff.documents.store', $staffMember), [
                'staff_document_type_id' => $type->id,
                'file' => UploadedFile::fake()->create('photo.pdf', 100, 'application/pdf'),
            ])
            ->assertSessionHas('success');

        $document = $staffMember->documents()->firstOrFail();

        $this->assertSame('pending', $document->status);
        $this->assertSame('photo.pdf', $document->original_file_name);
        Storage::disk('private')->assertExists($document->file_path);
        $this->assertDatabaseHas('audit_logs', ['action' => 'staff_document.uploaded']);
    }

    public function test_each_staff_member_keeps_their_own_documents(): void
    {
        Storage::fake('private');
        [$owner, $first] = $this->ownerAndStaff();
        $second = StaffMember::factory()->for($first->business)->create();
        $type = StaffDocumentType::factory()->create();

        foreach ([$first, $second] as $staffMember) {
            $this->actingAs($owner)->post(route('business-owner.staff.documents.store', $staffMember), [
                'staff_document_type_id' => $type->id,
                'file' => UploadedFile::fake()->create('id.pdf', 100, 'application/pdf'),
            ]);
        }

        $this->assertSame(1, $first->documents()->count());
        $this->assertSame(1, $second->documents()->count());
    }

    public function test_rejects_an_upload_for_an_inactive_document_type(): void
    {
        Storage::fake('private');
        [$owner, $staffMember] = $this->ownerAndStaff();
        $type = StaffDocumentType::factory()->inactive()->create();

        $this->actingAs($owner)
            ->post(route('business-owner.staff.documents.store', $staffMember), [
                'staff_document_type_id' => $type->id,
                'file' => UploadedFile::fake()->create('photo.pdf', 100, 'application/pdf'),
            ])
            ->assertInvalid(['staff_document_type_id']);

        $this->assertSame(0, StaffDocument::count());
    }

    public function test_rejects_a_file_that_is_too_large_or_the_wrong_type(): void
    {
        Storage::fake('private');
        [$owner, $staffMember] = $this->ownerAndStaff();
        $type = StaffDocumentType::factory()->create();

        $this->actingAs($owner)
            ->post(route('business-owner.staff.documents.store', $staffMember), [
                'staff_document_type_id' => $type->id,
                'file' => UploadedFile::fake()->create('big.pdf', 6000, 'application/pdf'),
            ])
            ->assertInvalid(['file']);

        $this->actingAs($owner)
            ->post(route('business-owner.staff.documents.store', $staffMember), [
                'staff_document_type_id' => $type->id,
                'file' => UploadedFile::fake()->create('script.exe', 10, 'application/x-msdownload'),
            ])
            ->assertInvalid(['file']);

        $this->assertSame(0, StaffDocument::count());
    }

    public function test_forbids_uploading_for_another_owners_staff_member(): void
    {
        Storage::fake('private');
        $stranger = User::factory()->create();
        $staffMember = StaffMember::factory()->create();
        $type = StaffDocumentType::factory()->create();

        $this->actingAs($stranger)
            ->post(route('business-owner.staff.documents.store', $staffMember), [
                'staff_document_type_id' => $type->id,
                'file' => UploadedFile::fake()->create('photo.pdf', 100, 'application/pdf'),
            ])
            ->assertForbidden();
    }

    public function test_forbids_uploading_once_the_staff_member_is_submitted(): void
    {
        Storage::fake('private');
        $owner = User::factory()->create();
        $staffMember = StaffMember::factory()->submitted()->for(Business::factory()->approved()->for($owner))->create();
        $type = StaffDocumentType::factory()->create();

        $this->actingAs($owner)
            ->post(route('business-owner.staff.documents.store', $staffMember), [
                'staff_document_type_id' => $type->id,
                'file' => UploadedFile::fake()->create('photo.pdf', 100, 'application/pdf'),
            ])
            ->assertForbidden();

        $this->assertSame(0, StaffDocument::count());
    }

    public function test_replacing_a_rejected_document_resets_review_and_removes_the_old_file(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('staff-documents/1/old.pdf', 'old');
        [$owner, $staffMember] = $this->ownerAndStaff();
        $document = StaffDocument::factory()->rejected()->for($staffMember)->create(['file_path' => 'staff-documents/1/old.pdf']);

        $this->actingAs($owner)
            ->post(route('business-owner.staff-documents.replace', $document), [
                'file' => UploadedFile::fake()->create('new.pdf', 100, 'application/pdf'),
            ])
            ->assertSessionHas('success');

        $document->refresh();

        $this->assertSame('pending', $document->status);
        $this->assertNull($document->admin_note);
        $this->assertSame('new.pdf', $document->original_file_name);
        Storage::disk('private')->assertMissing('staff-documents/1/old.pdf');
        Storage::disk('private')->assertExists($document->file_path);
    }

    public function test_forbids_replacing_an_accepted_document(): void
    {
        Storage::fake('private');
        [$owner, $staffMember] = $this->ownerAndStaff();
        $document = StaffDocument::factory()->accepted()->for($staffMember)->create();

        $this->actingAs($owner)
            ->post(route('business-owner.staff-documents.replace', $document), [
                'file' => UploadedFile::fake()->create('new.pdf', 100, 'application/pdf'),
            ])
            ->assertStatus(422);

        $this->assertSame('accepted', $document->refresh()->status);
    }

    public function test_owner_deletes_a_pending_document_and_its_file(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('staff-documents/1/doc.pdf', 'content');
        [$owner, $staffMember] = $this->ownerAndStaff();
        $document = StaffDocument::factory()->for($staffMember)->create(['file_path' => 'staff-documents/1/doc.pdf']);

        $this->actingAs($owner)
            ->delete(route('business-owner.staff-documents.destroy', $document))
            ->assertSessionHas('success');

        $this->assertModelMissing($document);
        Storage::disk('private')->assertMissing('staff-documents/1/doc.pdf');
    }

    public function test_forbids_deleting_an_accepted_document(): void
    {
        [$owner, $staffMember] = $this->ownerAndStaff();
        $document = StaffDocument::factory()->accepted()->for($staffMember)->create();

        $this->actingAs($owner)
            ->delete(route('business-owner.staff-documents.destroy', $document))
            ->assertStatus(422);

        $this->assertModelExists($document);
    }

    public function test_forbids_deleting_another_owners_document(): void
    {
        $stranger = User::factory()->create();
        $document = StaffDocument::factory()->create();

        $this->actingAs($stranger)
            ->delete(route('business-owner.staff-documents.destroy', $document))
            ->assertForbidden();

        $this->assertModelExists($document);
    }

    public function test_owner_downloads_their_staff_document(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('staff-documents/1/doc.pdf', 'content');
        [$owner, $staffMember] = $this->ownerAndStaff();
        $document = StaffDocument::factory()->for($staffMember)->create([
            'file_path' => 'staff-documents/1/doc.pdf',
            'original_file_name' => 'passport.pdf',
        ]);

        $this->actingAs($owner)
            ->get(route('business-owner.staff-documents.download', $document))
            ->assertDownload('passport.pdf');
    }

    public function test_forbids_downloading_another_owners_staff_document(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('staff-documents/1/doc.pdf', 'content');
        $stranger = User::factory()->create();
        $document = StaffDocument::factory()->create(['file_path' => 'staff-documents/1/doc.pdf']);

        $this->actingAs($stranger)
            ->get(route('business-owner.staff-documents.download', $document))
            ->assertForbidden();
    }

    public function test_returns_404_when_the_stored_file_is_missing(): void
    {
        Storage::fake('private');
        [$owner, $staffMember] = $this->ownerAndStaff();
        $document = StaffDocument::factory()->for($staffMember)->create(['file_path' => 'staff-documents/1/gone.pdf']);

        $this->actingAs($owner)
            ->get(route('business-owner.staff-documents.download', $document))
            ->assertNotFound();
    }

    /**
     * @return array{User, StaffMember}
     */
    private function ownerAndStaff(): array
    {
        $owner = User::factory()->create();
        $staffMember = StaffMember::factory()->for(Business::factory()->approved()->for($owner))->create();

        return [$owner, $staffMember];
    }
}
