<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMonitorRequest;
use App\Http\Resources\MonitorCheckResource;
use App\Http\Resources\MonitorResource;
use App\Models\Monitor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MonitorController extends Controller
{
    /**
     * POST /api/monitors
     */
    public function store(StoreMonitorRequest $request): JsonResponse
    {
        $monitor = $request->user()->monitors()->create([
            'url'            => $request->url,
            'check_interval' => $request->input('check_interval', 5),
            'threshold'      => $request->input('threshold', 3),
            'status'         => 'pending',
        ]);

        return response()->json(
            ['data' => new MonitorResource($monitor)],
            201
        );
    }

    /**
     * GET /api/monitors
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        // Only return monitors belonging to the authenticated user
        $monitors = $request->user()->monitors()->get();

        return MonitorResource::collection($monitors);
    }

    /**
     * GET /api/monitors/{id}/history
     */
    public function history(Request $request, int $id): JsonResponse
    {
        // Scope to authenticated user's monitors only
        $monitor = $request->user()->monitors()->find($id);

        if (! $monitor) {
            return response()->json(['message' => 'Monitor not found.'], 404);
        }

        $perPage = min((int) $request->input('per_page', 15), 100);

        $checks = $monitor->checks()
            ->orderBy('checked_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'data' => MonitorCheckResource::collection($checks->items()),
            'meta' => [
                'current_page' => $checks->currentPage(),
                'per_page'     => $checks->perPage(),
                'total'        => $checks->total(),
            ],
        ]);
    }
}
