<?php

use Illuminate\Support\Facades\Route;
use Lunar\Article\Http\Livewire\Admin\Create;
use Lunar\Article\Http\Livewire\Admin\Index;
use Lunar\Article\Http\Livewire\Admin\Show;
use Lunar\Hub\Http\Middleware\Authenticate;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group([
    'prefix' => config('lunar-hub.system.path', 'hub'),
    'middleware' => config('lunar-hub.system.middleware', ['web']),
], function () {
    Route::group([
        'middleware' => [
            Authenticate::class,
        ],
    ], function ($router) {
        Route::group([
            'prefix' => 'article',
             'middleware' => 'can:manage-pages',
        ], function () {
            Route::get('/', Index::class)->name('hub.article.index');
            Route::get('create', Create::class)->name('hub.article.create');
            Route::get('/{article}', Show::class)->name('hub.article.show');
        });
    });
});

