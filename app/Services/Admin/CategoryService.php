<?php

namespace App\Services\Admin;

use App\Repositories\Admin\Category\CategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Category;

/**
 * Class CategoryService
 *
 * Service class handling business logic for category operations including
 * CRUD operations and parent-child relationship management.
 *
 * @package App\Services\Admin
 *
 * Category Listing Methods:
 * @method LengthAwarePaginator getAllCategory() Get paginated list of all categories
 * @method Collection getParent() Get list of parent categories
 * @method Category getCategory(int $id) Get specific category by ID
 *
 * Category Management Methods:
 * @method Category create(Request $request) Create new category
 * @method Category update(Request $request, int $id) Update existing category
 * @method bool delete(int $id) Delete category by ID
 *
 * Class Properties:
 * @property-read CategoryRepositoryInterface $categoryRepository Repository for category data operations
 *
 * @throws CategoryException When category operations fail
 *
 * @see \App\Repositories\Admin\Category\CategoryRepositoryInterface
 * @see \App\Models\Category
 */
class CategoryService
{
    /**
     * The category repository instance.
     *
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * Create a new CategoryService instance.
     *
     * @param  CategoryRepositoryInterface $categoryRepository Repository for category operations
     * @return void
     */
    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Get paginated list of all categories.
     *
     * @return LengthAwarePaginator Paginated collection of categories
     * @throws CategoryException When retrieval fails
     */
    public function getAllCategory(): LengthAwarePaginator
    {
        return $this->categoryRepository->getAllCategory();
    }

    /**
     * Get specific category by ID.
     *
     * @param  int $id Category ID
     * @return Category Category model instance
     * @throws CategoryException When category not found
     */
    public function getCategory(int $id): Category
    {
        return $this->categoryRepository->getCategory($id);
    }

    /**
     * Get list of parent categories.
     *
     * @return Collection Collection of parent categories
     * @throws CategoryException When retrieval fails
     */
    public function getParent(): Collection
    {
        return $this->categoryRepository->getParent();
    }

    /**
     * Create a new category.
     *
     * @param  mixed $request Request containing category data (name, parent_id, slug)
     * @return Category Created category instance
     * @throws CategoryException When creation fails
     */
    public function create($request): Category
    {
        return $this->categoryRepository->create($request->only('name', 'parent_id', 'slug'));
    }

    /**
     * Update an existing category.
     *
     * @param  mixed $request Request containing update data (name, parent_id, slug)
     * @param  int   $id      Category ID to update
     * @return Category       Updated category instance
     * @throws CategoryException When update fails
     */
    public function update($request, $id): Category
    {
        return $this->categoryRepository->update($request->only('name', 'parent_id', 'slug'), $id);
    }

    /**
     * Delete a category.
     *
     * @param  int  $id Category ID to delete
     * @return bool    True if deletion successful
     * @throws CategoryException When deletion fails
     */
    public function delete(int $id): bool
    {
        return  $this->categoryRepository->deleteCategory($id);
    }
}
