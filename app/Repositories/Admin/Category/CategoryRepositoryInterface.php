<?php

namespace App\Repositories\Admin\Category;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\RepositoryInterface;
use App\Exceptions\CategoryException;
use App\Models\Category;

/**
 * Interface CategoryRepositoryInterface
 *
 * Defines contract for category data persistence and retrieval operations.
 * Extends base RepositoryInterface for common repository methods.
 *
 * @package App\Repositories\Admin\Category
 *
 * Category Retrieval Methods:
 * @method LengthAwarePaginator getAllCategory() Get paginated list of all categories
 * @method Category getCategory(int $id) Get specific category by ID
 * @method Collection getParent() Get list of parent categories only
 *
 * Category Management Methods:
 * @method Category create(array $attributes) Create new category with given attributes
 * @method Category update(array $data, int $id) Update category with new data
 * @method bool deleteCategory(int $id) Delete category if it has no children
 *
 * @see \App\Repositories\RepositoryInterface
 * @see \App\Models\Category
 * @see \App\Exceptions\CategoryException
 */
interface CategoryRepositoryInterface extends RepositoryInterface
{
    /**
     * Get paginated list of all categories.
     *
     * @return LengthAwarePaginator Paginated collection of categories
     * @throws CategoryException When no categories are found
     */
    public function getAllCategory(): LengthAwarePaginator;

    /**
     * Get specific category by ID.
     *
     * @param  int $id Category ID to retrieve
     * @return Category Category model instance
     * @throws CategoryException When category not found
     */
    public function getCategory(int $id): Category;

    /**
     * Get list of parent categories (categories with no parent).
     *
     * @return Collection Collection of parent categories
     * @throws CategoryException When no parent categories found
     */
    public function getParent(): Collection;

    /**
     * Create a new category.
     *
     * @param  array $attributes Category attributes (name, parent_id, slug)
     * @return Category Created category instance
     * @throws CategoryException When category creation fails
     */
    public function create(array $attributes): Category;

    /**
     * Update an existing category.
     *
     * @param  array $data Updated category data (name, parent_id, slug)
     * @param  int   $id   Category ID to update
     * @return Category    Updated category instance
     * @throws CategoryException When category not found or update fails
     */
    public function update(array $request,int $id): Category;

    /**
     * Delete a category if it has no children.
     *
     * @param  int  $id Category ID to delete
     * @return bool     True if deletion successful
     * @throws CategoryException When:
     *         - Category not found
     *         - Category has children
     *         - Deletion fails
     */
    public function deleteCategory(int $id): bool;
}
