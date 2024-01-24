<?php

namespace Lunar\Article;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Lunar\Article\Http\Livewire\Admin\Create;
use Lunar\Article\Http\Livewire\Admin\Index;
use Lunar\Article\Http\Livewire\Components\Admin\ComponentCreate;
use Lunar\Article\Http\Livewire\Components\Admin\ComponentIndex;
use Lunar\Article\Http\Livewire\Components\Admin\ComponentShow;
use Lunar\Article\Http\Livewire\Components\Admin\ComponentTable;
use Lunar\Base\AttributeManifestInterface;
use Lunar\Hub\Auth\Manifest;
use Lunar\Hub\Auth\Permission;
use Lunar\Article\Http\Livewire\Components\ActivityLogFeed;
use Illuminate\Support\Facades\Config;
use Lunar\Hub\Facades\Menu;
use Lunar\Article\Models\Article;
use Lunar\Article\Search\ArticleIndexer;

class ArticleServiceProvider extends ServiceProvider
{

    public function register()
    {

    }

    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'article');

        Route::bind('article', function ($id) {
            return Article::withTrashed()->findOrFail($id);
        });

        $manifestAttribute = app(AttributeManifestInterface::class);
        $manifestAttribute->addType(Article::class);

        $this->app->booted(function () {
            $manifest = $this->app->get(Manifest::class);
            $manifest->addPermission(function (Permission $permission) {
                $permission->name = __('article::global.manage.article.title');
                $permission->handle = 'manage-article'; // or 'group:handle to group permissions
                $permission->description = __('article::global.manage.article.description');
            });
        });

        $slot = Menu::slot('sidebar');

        $slot->addItem(function ($item) {
            $item
                ->name(__('article::menu.sidebar.article'))
                ->handle('hub.Article')
                ->route('hub.article.index')
                ->icon('book-open');
        });

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'article');

        $this->publishes([__DIR__ . '/../resources/lang' => resource_path('lang/vendor/article')], 'article_lang');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        Config::set('lunar.search.models', array_merge(config('lunar.search.models'),
            [Article::class]));
        Config::set('lunar.search.indexers', array_merge(config('lunar.search.indexers'), [
            Article::class => ArticleIndexer::class,
        ]));

        $this->registerLivewireComponents();

    }

    public function registerLivewireComponents()
    {
        Livewire::component('article.admin.index', Index::class);
        Livewire::component('article.admin.create', Create::class);
        Livewire::component('article.components.admin.component-index', ComponentIndex::class);
        Livewire::component('article.components.admin.component-create', ComponentCreate::class);
        Livewire::component('article.components.admin.component-table', ComponentTable::class);
        Livewire::component('article.components.admin.component-show', ComponentShow::class);
        Livewire::component('article.components.activity-log-feed', ActivityLogFeed::class);
    }
}
