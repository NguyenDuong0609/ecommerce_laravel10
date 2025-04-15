<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Exceptions\Api\CategoryException;
use App\Transformers\CategoryTransformer;
use App\Services\Admin\CategoryService;

/**
 * Class CategoryController
 *
 * Controller handling all category-related operations in the admin section
 * including CRUD operations for categories and parent-child relationships.
 *
 * @package App\Http\Controllers\Admin
 *
 * Category Listing Methods:
 * @method JsonResponse getParent() Get list of parent categories
 * @method JsonResponse getAllCategory() Get all categories with pagination
 * @method JsonResponse show(int $id) Get specific category details
 *
 * Category Management Methods:
 * @method JsonResponse store(StoreCategoryRequest $request) Create new category
 * @method JsonResponse update(UpdateCategoryRequest $request, int $id) Update category
 * @method JsonResponse destroy(int $id) Delete category
 *
 * Class Properties:
 * @property-read CategoryService $categoryService Service layer for category operations
 * @property-read Response $response Response handling utility
 *
 * Exceptions:
 * @throws CategoryException When category operations fail
 *
 * @see \App\Services\Admin\CategoryService
 * @see \App\Transformers\CategoryTransformer
 * @see \App\Exceptions\Api\CategoryException
 */
class CategoryController extends Controller
{
    /**
     * The category service instance.
     *
     * @var CategoryService
     */
    protected $categoryService;

    /**
     * Create a new CategoryController instance.
     *
     * @param  CategoryService $categoryService Service for category operations
     * @return void
     */
    public function __construct(CategoryService $categoryService)
    {
        parent::__construct();
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of parent categories.
     *
     * @return JsonResponse Collection of parent categories
     * @throws CategoryException When retrieval fails
     */
    public function getParent(): JsonResponse
    {
        try {
            $data = $this->categoryService->getParent();

            Log::info(config('messages.CATEGORY.GET_PARENT_SUCCESS'));

            return $this->response->withOK($data);
        } catch (CategoryException $e) {
            return $e->getResponse();
        }
    }

    /**
     * Display a paginated list of all categories.
     *
     * @return JsonResponse Paginated collection of all categories
     * @throws CategoryException When retrieval fails
     */
    public function getAllCategory(): JsonResponse
    {
        try {
            $data = $this->categoryService->getAllCategory();

            Log::info(config('messages.CATEGORY.GET_ALL_SUCCESS'));

            return $this->response->collection($data, new CategoryTransformer());
        } catch (CategoryException $e) {
            return $e->getResponse();
        }
    }

    /**
     * Store a newly created category.
     *
     * @param  StoreCategoryRequest $request Category creation data
     * @return JsonResponse Created category data
     * @throws CategoryException When creation fails
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        try {
            $data = $this->categoryService->create($request);

            Log::info(config('messages.CATEGORY.CREATE_CATEGORY_SUCCESS'));

            return $this->response->withCreated($data);
        } catch (CategoryException $e) {
            return $e->getResponse();
        }
    }

    /**
     * Display the specified category.
     *
     * @param  int $id Category ID
     * @return JsonResponse Category details
     * @throws CategoryException When category not found
     */
    public function show(int $id): JsonResponse
    {
        try {
            $data = $this->categoryService->getCategory($id);

            Log::info(config('messages.CATEGORY.GET_INFO_CATEGORY_SUCCESS'));

            return $this->response->withOK($data);
        } catch (CategoryException $e) {
            return $e->getResponse();
        }
    }

    /**
     * Update the specified category.
     *
     * @param  UpdateCategoryRequest $request Update data
     * @param  int $id Category ID
     * @return JsonResponse Updated category data
     * @throws CategoryException When update fails
     */
    public function update(UpdateCategoryRequest $request,int $id): JsonResponse
    {
        try {
            $data = $this->categoryService->update($request, $id);

            Log::info(config('messages.CATEGORY.UPDATE_CATEGORY_SUCCESS'));

            return $this->response->withOK($data);
        } catch (CategoryException $e) {
            return $e->getResponse();
        }
    }

    /**
     * Remove the specified category.
     *
     * @param  int $id Category ID
     * @return JsonResponse Success message
     * @throws CategoryException When deletion fails
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->categoryService->delete($id);

            Log::info(config('messages.CATEGORY.DELETE_CATEGORY_SUCCESS'));

            return $this->response->withSuccess(config('messages.CATEGORY.DELETE_CATEGORY_SUCCESS'));
        } catch (CategoryException $e) {
            return $e->getResponse();
        }
    }
}
