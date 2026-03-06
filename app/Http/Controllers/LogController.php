<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with(['causer', 'subject'])->latest();

        // Filtro por tipo de evento (created, updated, deleted)
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        $logs = $query->paginate(20);

        return view('admin.logs.index', compact('logs'));
    }
}
