<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncStorage extends Command
{
    protected $signature   = 'storage:sync';
    protected $description = 'Copy storage/app/public to public/storage (use when symlink unavailable)';

    public function handle(): void
    {
        $source = storage_path('app/public');
        $dest   = public_path('storage');

        $this->info('Starting storage sync...');
        $this->copyDir($source, $dest);
        $this->info('Storage synced successfully!');
    }

    private function copyDir(string $src, string $dest): void
    {
        if (!is_dir($dest)) mkdir($dest, 0755, true);

        foreach (scandir($src) as $file) {
            if ($file === '.' || $file === '..') continue;

            $srcPath  = $src  . '/' . $file;
            $destPath = $dest . '/' . $file;

            if (is_dir($srcPath)) {
                $this->copyDir($srcPath, $destPath);
            } else {
                // Use file_get_contents instead of copy() 
                // because copy() is disabled on this host
                $contents = @file_get_contents($srcPath);
                if ($contents !== false) {
                    file_put_contents($destPath, $contents);
                    $this->line("  ✅ $file");
                } else {
                    $this->error("  ❌ Failed: $file");
                }
            }
        }
    }
}