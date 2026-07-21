<?php

namespace App\Projects\ActivitiesBoard\Services\Model;

use App\Common\Repository\Service\TransactionService;
use App\Projects\ActivitiesBoard\Models\Category;
use App\Projects\ActivitiesBoard\Repositories\CategoryRepository;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private TransactionService $transactionService
    ) {
    }

    /**
     * Categorías del usuario autenticado (API, sin filtros ni paginación).
     */
    public function allForUser(int $userId): Collection
    {
        return $this->categoryRepository->getQueryBuilder()
            ->where('user_id', $userId)
            ->orderBy('position')
            ->get();
    }

    public function findForUser(int $id, int $userId): Category
    {
        /** @var Category $category */
        $category = $this->categoryRepository->getQueryBuilder()
            ->where('user_id', $userId)
            ->findOrFail($id);

        return $category;
    }

    public function createForUser(array $data, int $userId): Category
    {
        return $this->transactionService->execute(function () use ($data, $userId) {
            // user_id no es mass-assignable (no está en $fillable) a propósito,
            // para que nunca pueda llegar desde el payload del cliente.
            /** @var Category $category */
            $category = $this->categoryRepository->getModel()
                ->newInstance($data);
            $category->user_id = $userId;
            $category->save();

            return $category;
        });
    }

    public function updateForUser(int $id, array $data, int $userId): Category
    {
        return $this->transactionService->execute(function () use ($id, $data, $userId) {
            $category = $this->findForUser($id, $userId);
            $category->update($data);

            return $category;
        });
    }

    public function deleteForUser(int $id, int $userId): void
    {
        $this->transactionService->execute(function () use ($id, $userId) {
            $this->findForUser($id, $userId)
                ->delete();
        });
    }
}
