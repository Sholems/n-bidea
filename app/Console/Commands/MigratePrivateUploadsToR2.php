<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

#[Signature('storage:migrate-private-to-r2 {--force : Replace objects that already exist in R2} {--dry-run : List files without copying them}')]
#[Description('Copy existing private local uploads to the configured Cloudflare R2 bucket')]
class MigratePrivateUploadsToR2 extends Command
{
    public function handle(): int
    {
        if (! config('filesystems.disks.private.endpoint') || ! config('filesystems.disks.private.bucket')) {
            $this->error('Configure CLOUDFLARE_R2_ENDPOINT and CLOUDFLARE_R2_BUCKET before migrating uploads.');

            return self::FAILURE;
        }

        $source = Storage::disk('private-local');
        $destination = Storage::disk('private');
        $files = collect($source->allFiles())->reject(fn (string $path): bool => basename($path) === '.gitignore');
        $copied = 0;
        $skipped = 0;

        foreach ($files as $path) {
            if (! $this->option('force') && $destination->exists($path)) {
                $skipped++;

                continue;
            }

            if ($this->option('dry-run')) {
                $this->line($path);

                continue;
            }

            $stream = $source->readStream($path);

            if ($stream === false) {
                throw new RuntimeException("Unable to read local upload [{$path}].");
            }

            try {
                $destination->writeStream($path, $stream, ['visibility' => 'private']);
            } catch (Throwable $exception) {
                $this->error("Upload failed for [{$path}]: {$exception->getMessage()}");

                return self::FAILURE;
            } finally {
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }

            $copied++;
        }

        if ($this->option('dry-run')) {
            $this->info("Dry run complete: {$files->count()} local upload(s) found.");

            return self::SUCCESS;
        }

        $this->info("Migration complete: {$copied} copied, {$skipped} skipped.");

        return self::SUCCESS;
    }
}
