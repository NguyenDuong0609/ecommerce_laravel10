<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exceptions\LoginException;
use App\Repositories\Admin\User\UserRepositoryInterface;
use App\Repositories\Admin\User\UserRepository;
use App\Repositories\Admin\Category\CategoryRepositoryInterface;
use App\Repositories\Admin\Category\CategoryRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // $this->app->singleton(
        //     \App\Repositories\Admin\User\UserRepositoryInterface::class,
        //     \App\Repositories\Admin\User\UserRepository::class
        // );
        \App\Exceptions\LoginException::class;
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Don't use Redis when query
        $this->app->singleton(
            \App\Repositories\Admin\User\UserRepositoryInterface::class,
            \App\Repositories\Admin\User\UserRepository::class
        );

        // Use Redis when query
        // $this->app->singleton(
        //     \App\Repositories\Admin\User\UserRepositoryInterface::class,
        //     \App\Repositories\Admin\User\UserCachedRepository::class
        // );

        $this->app->singleton(
            \App\Repositories\Admin\Category\CategoryRepositoryInterface::class,
            \App\Repositories\Admin\Category\CategoryRepository::class
        );

        /**
         * Add paginate function on Collection
         */
        Collection::macro('paginate', function($perPage, $total = null, $page = null, $pageName = 'page') {
            $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);

            return new LengthAwarePaginator(
                $this->forPage($page, $perPage),
                $total ?: $this->count(),
                $perPage,
                $page,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => $pageName,
                ]
            );
        });
    }
}
