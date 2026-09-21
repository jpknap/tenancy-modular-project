<?php

namespace App\Projects\ActivitiesBoard\Http\Controller\Api;

use App\Attributes\Middleware;
use App\Attributes\Route;
use App\Attributes\RoutePrefix;
use App\Attributes\Where;
use App\Projects\ActivitiesBoard\FormRequests\Api\ActivityLogStoreRequest;
use App\Projects\ActivitiesBoard\FormRequests\Api\ActivityLogUpdateRequest;
use App\Projects\ActivitiesBoard\Services\Model\ActivityLogService;
use App\Projects\ActivitiesBoard\Services\Model\ActivityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * CRUD de registros (tiempos) de una Activity del usuario autenticado.
 * Único recurso con paginación: el volumen de logs crece sin límite claro.
 */
#[RoutePrefix('api/activities')]
#[Middleware(['auth:sanctum'])]
class ActivityLogController
{
    public function __construct(
        private ActivityService $activityService,
        private ActivityLogService $activityLogService
    ) {
    }

    #[Route('{activityId}/logs', methods: ['GET'], name: 'logs.list')]
    #[Where([
        'activityId' => '[0-9]+',
    ])]
    public function index(Request $request, int $activityId): JsonResponse
    {
        $activity = $this->activityService->findForUser($activityId, $request->user()->id);

        return response()->json($this->activityLogService->paginateForActivity($activity));
    }

    #[Route('{activityId}/logs', methods: ['POST'], name: 'logs.store')]
    #[Where([
        'activityId' => '[0-9]+',
    ])]
    public function store(ActivityLogStoreRequest $request, int $activityId): JsonResponse
    {
        $activity = $this->activityService->findForUser($activityId, $request->user()->id);
        $log = $this->activityLogService->createIdempotent($activity, $request->validated());

        return response()->json($log, $log->wasRecentlyCreated ? Response::HTTP_CREATED : Response::HTTP_OK);
    }

    #[Route('{activityId}/logs/{id}', methods: ['GET'], name: 'logs.show')]
    #[Where([
        'activityId' => '[0-9]+',
        'id' => '[0-9]+',
    ])]
    public function show(Request $request, int $activityId, int $id): JsonResponse
    {
        $activity = $this->activityService->findForUser($activityId, $request->user()->id);
        $log = $this->activityLogService->findForActivity($activity, $id);

        return response()->json($log);
    }

    #[Route('{activityId}/logs/{id}', methods: ['PATCH'], name: 'logs.update')]
    #[Where([
        'activityId' => '[0-9]+',
        'id' => '[0-9]+',
    ])]
    public function update(ActivityLogUpdateRequest $request, int $activityId, int $id): JsonResponse
    {
        $activity = $this->activityService->findForUser($activityId, $request->user()->id);
        $log = $this->activityLogService->updateForActivity($activity, $id, $request->validated());

        return response()->json($log);
    }

    #[Route('{activityId}/logs/{id}', methods: ['DELETE'], name: 'logs.delete')]
    #[Where([
        'activityId' => '[0-9]+',
        'id' => '[0-9]+',
    ])]
    public function destroy(Request $request, int $activityId, int $id): Response
    {
        $activity = $this->activityService->findForUser($activityId, $request->user()->id);
        $this->activityLogService->deleteForActivity($activity, $id);

        return response()->noContent();
    }
}
