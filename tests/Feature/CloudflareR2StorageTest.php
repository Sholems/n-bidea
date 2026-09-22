<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CloudflareR2StorageTest extends TestCase
{
    public function test_existing_private_uploads_can_be_copied_to_r2_without_overwriting_objects(): void
    {
        Storage::fake('private-local');
        Storage::fake('private');
        config()->set('filesystems.disks.private.endpoint', 'https://account.r2.cloudflarestorage.com');
        config()->set('filesystems.disks.private.bucket', 'nbcci-uploads');

        Storage::disk('private-local')->put('business-documents/1/certificate.pdf', 'new certificate');
        Storage::disk('private-local')->put('staff-documents/1/passport.pdf', 'passport');
        Storage::disk('private')->put('business-documents/1/certificate.pdf', 'existing certificate');

        $this->artisan('storage:migrate-private-to-r2')
            ->expectsOutput('Migration complete: 1 copied, 1 skipped.')
            ->assertSuccessful();

        $this->assertSame('existing certificate', Storage::disk('private')->get('business-documents/1/certificate.pdf'));
        $this->assertSame('passport', Storage::disk('private')->get('staff-documents/1/passport.pdf'));
    }

    public function test_private_upload_migration_supports_a_dry_run(): void
    {
        Storage::fake('private-local');
        Storage::fake('private');
        config()->set('filesystems.disks.private.endpoint', 'https://account.r2.cloudflarestorage.com');
        config()->set('filesystems.disks.private.bucket', 'nbcci-uploads');

        Storage::disk('private-local')->put('publications/report.pdf', 'report');

        $this->artisan('storage:migrate-private-to-r2', ['--dry-run' => true])
            ->expectsOutput('publications/report.pdf')
            ->expectsOutput('Dry run complete: 1 local upload(s) found.')
            ->assertSuccessful();

        Storage::disk('private')->assertMissing('publications/report.pdf');
    }
}
