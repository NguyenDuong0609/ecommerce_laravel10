<?php

namespace App\Repositories\Admin\Category;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Config as ConfigFacade;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\Collection;

use App\Exceptions\Api\CategoryException;
use App\Repositories\BaseRepository;
use App\Models\Category;
use App\Enums\Paginate;

/**
 * Class CategoryRepository
 *
 * Repository class handling category data persistence and retrieval operations.
 * Extends BaseRepository and implements CategoryRepositoryInterface.
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
 * Inherited Methods:
 * @method mixed getModel() Get the associated model class name
 *
 * @throws CategoryException When:
 *         - Categories not found
 *         - Category creation fails
 *         - Category update fails
 *         - Category deletion fails
 *         - Attempting to delete category with children
 *
 * @see \App\Repositories\BaseRepository
 * @see \App\Models\Category
 * @see \App\Exceptions\Api\CategoryException
 */
class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    /**
     * Create a new CategoryRepository instance.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Get the model class name for the repository.
     *
     * @return string The Category model class name
     */
    public function getModel(): string
    {
        return Category::class;
    }

    /**
     * Get paginated list of all categories.
     *
     * @return LengthAwarePaginator Paginated collection of categories
     * @throws CategoryException When no categories are found
     */
    public function getAllCategory(): LengthAwarePaginator
    {
        $perPage = request()->get('limit', Paginate::LIMIT_PAGINATE);

        $data = $this->getAll()->paginate($perPage);
        if($data->isEmpty()) {
            throw new CategoryException(
                ConfigFacade::get('messages.CATEGORY.NOT_FOUND'),
                "",
                Response::HTTP_NOT_FOUND);
        }

        return $data;
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
        $data = $this->find($id);

        if(!$data) {
            throw new CategoryException(
                ConfigFacade::get('messages.CATEGORY.NOT_FOUND'),
                "",
                Response::HTTP_NOT_FOUND
            );
        }

        return $data;
    }

    /**
     * Get list of parent categories (categories with no parent).
     *
     * @return Collection Collection of parent categories
     * @throws CategoryException When no parent categories found
     */
    public function getParent(): Collection
    {
        $data = $this->_model->where('parent_id', null)->get();
        if($data->isEmpty()) {
            throw new CategoryException(
                ConfigFacade::get('messages.CATEGORY.NOT_FOUND'),
                "",
                Response::HTTP_NOT_FOUND);
        }

        return $data;
    }

    /**
     * Create a new category.
     *
     * @param  array $attributes Category attributes (name, parent_id, slug)
     * @return Category Created category instance
     * @throws CategoryException When category creation fails
     */
    public function create(array $attributes): Category
    {
        $category = $this->_model->create([
            'name' => $attributes['name'],
            'parent_id' => $attributes['parent_id'],
            'slug' => $attributes['slug']
        ]);

        if(!$category) {
            throw new CategoryException(
                config('messages.CATEGORY.CREATE_FAIL'),
                "",
                Response::HTTP_BAD_GATEWAY);
        }

        return $category;
    }

    /**
     * Update an existing category.
     *
     * @param  array $data Updated category data (name, parent_id, slug)
     * @param  int   $id   Category ID to update
     * @return Category    Updated category instance
     * @throws CategoryException When category not found or update fails
     */
    public function update(array $data,int $id): Category
    {
        $category = $this->find($id);

        if (!$category) {
            throw new CategoryException(
                config('messages.CATEGORY.NOT_FOUND'),
                "",
                Response::HTTP_NOT_FOUND
            );
        }

        if(!$category->update([
            'name' => $data['name'],
            'parent_id' => $data['parent_id'],
            'slug' => $data['slug']
        ])) {
            throw new CategoryException(
                config('messages.CATEGORY.UPDATE_FAIL'),
                "",
                Response::HTTP_BAD_GATEWAY
            );
        }

        return $category->refresh();
    }

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
    public function deleteCategory(int $id): bool
    {
        $category = $this->find($id);

        if (!$category) {
            throw new CategoryException(
                config('messages.CATEGORY.NOT_FOUND'),
                "",
                Response::HTTP_NOT_FOUND
            );
        }

        if ($category->children()->exists()) {
            throw new CategoryException(
                config('messages.CATEGORY.DELETE_FAIL_WIT_SUBCATEGORIES'),
                "",
                Response::HTTP_BAD_GATEWAY
            );
        }

        $category->delete();

        return true;
    }
}
