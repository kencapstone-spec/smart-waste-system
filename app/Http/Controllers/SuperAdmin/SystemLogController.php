<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use Inertia\Inertia;

class SystemLogController extends Controller
{
    public function index()
    {
        $logs = SystemLog::with('user')
            ->latest()
            ->paginate(20);

        return Inertia::render('SuperAdmin/SystemLogs/Index', [
            'logs' => $logs,
        ]);
    }
}