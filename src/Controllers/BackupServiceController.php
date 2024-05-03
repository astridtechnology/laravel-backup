<?php

namespace AstridTechnology\LaravelBackup\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Queue;
use AstridTechnology\LaravelBackup\ProjectBackup;

class BackupServiceController
{
    public function __invoke(ProjectBackup $astridFun)
    {
        $backups =  $astridFun->backupList();
        return view('astrid::index', compact('backups'));
    }

    public function downloadRouteFile($filename)
    {
        try {
            $newFunction = new ProjectBackup;
            return $newFunction->downloadProjectFun($filename);
        } catch (Exception $ex) {
            abort(404, 'File not found');
        }
    }

    public function deleteRouteFile(Request $request)
    {
        try {
            $filename = $request->filename;
            $newFunction = new ProjectBackup;
            $result = $newFunction->deleteBackupProject($filename);
            return $result;
        } catch (Exception $ex) {
            return response()->json([
                'message' => 'File not found',
                'success' => false
            ]);
        }
    }

    public function createBackup()
    {
        try {
            $newFunction = new ProjectBackup;
            $result = $newFunction->generateBackup();
            return $result;
        } catch (Exception $ex) {
            $output = [];
            $success = false;
            return response()->json([
                'message' => 'Backup creation failed',
                'output' => $output,
                'success' => $success
            ]);
        }
    }

    public function getBackupStatus()
    {
        // Check if there are any pending or running backup jobs in the queue
        $backupJobStatus = Queue::size('default');

        if ($backupJobStatus > 0) {
            // If there are pending or running backup jobs, return appropriate message
            $message = 'Backup job is currently running or pending...';
        } else {
            // If there are no pending or running backup jobs, return a different message
            $message = 'No backup jobs are currently running or pending.';
        }

        return response()->json([
            'message' => $message
        ]);
    }
}
