<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = ActivityLog::with('user')
            ->when($request->module, fn($q) => $q->where('module', $request->module))
            ->when($request->user_id, fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->action, fn($q) => $q->where('action', $request->action))
            ->when($request->date, fn($q) => $q->whereDate('created_at', $request->date))
            ->latest()
            ->paginate(7)
            ->withQueryString();

        $modules = ActivityLog::distinct()->pluck('module')->sort()->values();
        $users = \App\Models\User::orderBy('name')->get(['id', 'name']);

        return view('admin.activity-logs.index', compact('logs', 'modules', 'users'));
    }
}