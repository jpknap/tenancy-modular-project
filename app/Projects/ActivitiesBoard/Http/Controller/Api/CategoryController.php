<?php

namespace App\Projects\ActivitiesBoard\Http\Controller\Api;

use App\Attributes\Middleware;
use App\Attributes\Route;
use App\Attributes\RoutePrefix;
use App\Attributes\Where;
use App\Projects\ActivitiesBoard\FormRequests\Api\CategoryStoreRequest;
use App\Projects\ActivitiesBoard\FormRequests\Api\CategoryUpdateRequest;
use App\Projects\ActivitiesBoard\Services\Model\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * CRUD de Categories a nivel de usuario autenticado (PWA). Sin filtros ni
 * paginación en el listado: el volumen esperado por usuario es bajo.
 */
#[RoutePrefix('api/categories')]
#[Middleware(['auth:sanctum'])]
class CategoryController
{
    public function __construct(
        private CategoryService $categoryService
    ) {
    }

    #[Route('', methods: ['GET'], name: 'list')]
    public function index(Request $request): JsonResponse
    {
        $categories = $this->categoryService->allForUser($request->user()->id);

        return response()->json($categories);
    }

    #[Route('', methods: ['POST'], name: 'store')]
    public function store(CategoryStoreRequest $request): JsonResponse
    {
        $category = $this->categoryService->createForUser($request->validated(), $request->user()->id);

        return response()->json($category, Response::HTTP_CREATED);
    }

    #[Route('{id}', methods: ['GET'], name: 'show')]
    #[Where([
        'id' => '[0-9]+',
    ])]
    public function show(Request $request, int $id): JsonResponse
    {
        $category = $this->categoryService->findForUser($id, $request->user()->id);

        return response()->json($category);
    }

    #[Route('{id}', methods: ['PATCH'], name: 'update')]
    #[Where([
        'id' => '[0-9]+',
    ])]
    public function update(CategoryUpdateRequest $request, int $id): JsonResponse
    {
        $category = $this->categoryService->updateForUser($id, $request->validated(), $request->user()->id);

        return response()->json($category);
    }

    #[Route('{id}', methods: ['DELETE'], name: 'delete')]
    #[Where([
        'id' => '[0-9]+',
    ])]
    public function destroy(Request $request, int $id): Response
    {
        $this->categoryService->deleteForUser($id, $request->user()->id);

        return response()->noContent();
    }
}
