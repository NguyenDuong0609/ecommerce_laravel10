<?php

namespace App\Repositories\Admin\Category;

use Illuminate\Support\Facades\Redis;
use App\Repositories\BaseRepository;
use App\Models\Category;
use App\Enums\Paginate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryCachedRepository extends BaseRepository implements CategoryRepositoryInterface
{
    private $categoryRepository;
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
        parent::__construct();
    }

     /**
     * [getModel description]
     *
     * @return  mixed    [return description]
     */
    public function getModel(): mixed
    {
        return Category::class;
    }

    /**
     * [getAllCategory description]
     *
     * @return  mixed   [return description]
     */
    public function getAllCategory(): LengthAwarePaginator
    {
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $expire = rand(2419200, 4838400);
        $cachedKey = 'ListCategory_page_' . $page;

        if (!Redis::exists($cachedKey) || (Redis::get($cachedKey) == null)) {
            Redis::setex($cachedKey, $expire, serialize($this->categoryRepository->getAllCategory()));
        }
        return unserialize(Redis::get($cachedKey));
    }

    /**
     * [getCategory description]
     *
     * @param int $id
     * @return Category
     *
     * @throws CategoryException
     */
    public function getCategory($id): Category
    {
        $expire = rand(2419200, 4838400);
        $cachedKey = 'InfoCategory_' . $id;
        if (!Redis::exists($cachedKey) || (Redis::get($cachedKey) == null)) {
            Redis::setex($cachedKey, $expire, serialize($this->categoryRepository->getCategory($id)));
        }
        return unserialize(Redis::get($cachedKey));
    }

    public function getParent(): Collection
    {
        $expire = rand(2419200, 4838400);
        $cachedKey = 'ListParentCategory';
        if (!Redis::exists($cachedKey) || (Redis::get($cachedKey) == null)) {
            Redis::setex($cachedKey, $expire, serialize($this->categoryRepository->getParent()));
        }
        return unserialize(Redis::get($cachedKey));
    }

    public function update($request, $id): Category
    {
        $expire = rand(2419200, 4838400);
        $cachedKey = 'InfoVategory_' . $id;
        $this->categoryRepository->updateCategory($request, $id);

        if (Redis::exists($cachedKey))
            Redis::del($cachedKey);
        Redis::setex($cachedKey, $expire, serialize($this->categoryRepository->getCategory($id)));

        return unserialize(Redis::get($cachedKey));
    }

    public function deleteCategory($id): mixed
    {
        return true;
    }

    public function create($request): mixed
    {
        return null;
    }
}
