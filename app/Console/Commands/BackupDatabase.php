<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BackupDatabase extends Command
{

    protected $signature = 'backup:run';
    protected $description = 'Backup MySQL database and uploaded files';

    public function handle()
    {
        try {
            $date = now()->format('Y-m-d_H-i-s');
            $backupPath = storage_path("app/backups/{$date}");

            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0777, true);
            }

            $db = env('DB_DATABASE');
            $user = env('DB_USERNAME');
            $pass = env('DB_PASSWORD');
            $host = env('DB_HOST');

            $dbFile = "{$backupPath}/database.sql";

            $mysqldump = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';

            $command = "\"{$mysqldump}\" -h{$host} -u{$user} {$db} > \"{$dbFile}\"";  
            if ($pass) {
                $command = "\"{$mysqldump}\" -h{$host} -u{$user} -p{$pass} {$db} > \"{$dbFile}\"";
            }

            exec($command);

            $filesPath = storage_path('app/private/complaints');
            $zipFile = "{$backupPath}/files.zip";

            $zip = new \ZipArchive;
            if ($zip->open($zipFile, \ZipArchive::CREATE) === true) {
                $this->zipFolder($filesPath, $zip);
                $zip->close();
            }

            Log::info("Backup completed successfully at {$date}");
            $this->info('Backup completed successfully');

        } catch (\Exception $e) {
            Log::error('Backup failed: ' . $e->getMessage());
            $this->error('Backup failed');
        }
    }

    private function zipFolder($folder, &$zip, $parentFolder = '')
    {
        foreach (scandir($folder) as $file) {
            if ($file == '.' || $file == '..') continue;

            $path = "{$folder}/{$file}";
            $localPath = $parentFolder . $file;

            if (is_dir($path)) {
                $zip->addEmptyDir($localPath);
                $this->zipFolder($path, $zip, $localPath . '/');
            } else {
                $zip->addFile($path, $localPath);
            }
        }
    }
}
