<?php

namespace AstridTechnology\LaravelBackup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Spatie\Backup\Helpers\Format;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatusFactory;
use Spatie\Backup\BackupDestination\Backup;
use AstridTechnology\LaravelBackup\Jobs\AstridBackupJob;

class ProjectBackup extends Controller
{
    protected function formatsaperateBackups($backupDestinationStatuses)
    {
        $formattedBackups = [];

        $backupDestinationStatuses->each(function ($backupDestinationStatus) use (&$formattedBackups) {
            $backups = $backupDestinationStatus->backupDestination()->backups();

            $backups->each(function ($backup) use ($backupDestinationStatus, &$formattedBackups) {
                $backupPath = storage_path('app/' . $backup->path()); // Get the full file directory path of the backup
                $fileSize = filesize($backupPath); // Get the file size

                $fileName = basename($backupPath);
                // Convert file size to human-readable format
                $humanReadableSize = $this->formatSize($fileSize);

                $formattedBackups[] = [
                    //'name' => $backupDestinationStatus->backupDestination()->backupName(),
                    'name' => $fileName,
                    'disk' => $backupDestinationStatus->backupDestination()->diskName(),
                    'reachable' => Format::emoji($backupDestinationStatus->backupDestination()->isReachable()),
                    'healthy' => Format::emoji($backupDestinationStatus->isHealthy()),
                    'count' => 1, // Set count to 1 for each individual backup
                    'newest' => $this->getFormattedBackupDate($backup),
                    'usedStorage' => isset($backupDestinationStatus->size) ? Format::humanReadableSize($backupDestinationStatus->size) : 'Unknown',
                    'backupPath' => $backupPath, // Include the full backup file directory path
                    'fileSize' => $humanReadableSize, // Include the human-readable file size
                ];
            });
        });

        return $formattedBackups;
    }

    protected function formatSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes, 1024));
        return round($bytes / (1024 ** $i), 2) . ' ' . $units[$i];
    }

    protected function getFormattedBackupDate($backup)
    {
        // Assuming $backup->created_at contains the timestamp of the backup creation
        $backupDate = Carbon::parse($backup->date());
        return $backupDate->diffForHumans();
    }

    public function backupList()
    {
        $statuses = BackupDestinationStatusFactory::createForMonitorConfig(config('backup.monitor_backups'));
        return $this->formatsaperateBackups($statuses);
    }

    public function downloadProjectFun($filename)
    {
        $path = storage_path('app/Laravel/' . $filename);

        if (file_exists($path)) {
            return response()->download($path);
        }

        abort(404, 'File not found');
    }

    public function generateBackup()
    {
        AstridBackupJob::dispatch();

        return response()->json([
            'message' => 'Backup job has been queued successfully',
            'success' => true
        ]);
    }

    public function deleteBackupProject($filename)
    {
        $path = storage_path('app/Laravel/' . $filename);

        if (file_exists($path)) {
            // Attempt to delete the file
            if (unlink($path)) {
                // File deleted successfully
                return response()->json([
                    'message' => 'File deleted successfully',
                    'success' => true
                ]);
            } else {
                // Unable to delete the file
                return response()->json([
                    'message' => 'Failed to delete the file',
                    'success' => false
                ]);
            }
        } else {
            // File does not exist
            return response()->json([
                'message' => 'File not found',
                'success' => false
            ]);
        }
    }
}
