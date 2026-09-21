<?php

namespace App\Projects\ActivitiesBoard\Http\Controller\Api;

use App\Attributes\Middleware;
use App\Attributes\Route;
use App\Attributes\RoutePrefix;
use App\Attributes\Where;
use App\Projects\ActivitiesBoard\FormRequests\Api\ActivityStoreRequest;
use App\Projects\ActivitiesBoard\FormRequests\Api\ActivityUpdateRequest;
use App\Projects\ActivitiesBoard\Services\Model\ActivityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * CRUD de Activities a nivel de usuario autenticado (PWA). Sin filtros ni
 * paginación en el listado: el volumen esperado por usuario es bajo.
 */
#[RoutePrefix('api/activities')]
#[Middleware(['auth:sanctum'])]
class ActivityController
{
    public function __construct(
        private ActivityService $activityService
    ) {
    }

    #[Route('', methods: ['GET'], name: 'list')]
    public function index(Request $request): JsonResponse
    {
        $activities = $this->activityService->allForUser($request->user()->id);

        return response()->json($activities);
    }

    #[Route('', methods: ['POST'], name: 'store')]
    public function store(ActivityStoreRequest $request): JsonResponse
    {
        $activity = $this->activityService->createForUser($request->validated(), $request->user()->id);

        return response()->json($activity, Response::HTTP_CREATED);
    }

    #[Route('{id}', methods: ['GET'], name: 'show')]
    #[Where([
        'id' => '[0-9]+',
    ])]
    public function show(Request $request, int $id): JsonResponse
    {
        $activity = $this->activityService->findForUser($id, $request->user()->id);

        return response()->json($activity);
    }

    #[Route('{id}', methods: ['PATCH'], name: 'update')]
    #[Where([
        'id' => '[0-9]+',
    ])]
    public function update(ActivityUpdateRequest $request, int $id): JsonResponse
    {
        $activity = $this->activityService->updateForUser($id, $request->validated(), $request->user()->id);

        return response()->json($activity);
    }

    #[Route('{id}', methods: ['DELETE'], name: 'delete')]
    #[Where([
        'id' => '[0-9]+',
    ])]
    public function destroy(Request $request, int $id): Response
    {
        $this->activityService->deleteForUser($id, $request->user()->id);

        return response()->noContent();
    }
}
