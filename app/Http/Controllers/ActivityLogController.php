<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogScopeService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request, ActivityLogScopeService $scope): View
    {
        $activities = $scope->paginateForUser($request->user(), $request);

        return view('activity-log.index', compact('activities'));
    }
}
