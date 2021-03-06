<?php

namespace App\Providers;

use App\Article;
use App\Menu;
use App\Permission;
use App\Policies\ArticlePolicy;
use App\Policies\MenusPolicy;
use App\Policies\PermissionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        Article::class => ArticlePolicy::class,
        Permission::class => PermissionPolicy::class,
        Menu::class => MenusPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('VIEW_ADMIN', function ($user) {
            return $user->canDo(['VIEW_ADMIN','ADD_ARTICLES'], TRUE);
        });

        Gate::define('VIEW_ADMIN_ARTICLES', function ($user) {
            return $user->canDo(['VIEW_ADMIN_ARTICLES'], TRUE);
        });

        Gate::define('EDIT_USERS', function ($user) {
            return $user->canDo(['EDIT_USERS'], TRUE);
        });

        Gate::define('VIEW_ADMIN_MENU', function ($user) {
            return $user->canDo(['VIEW_ADMIN_MENU'], TRUE);
        });

        //
    }
}
