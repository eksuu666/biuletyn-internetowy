<?php

use Illuminate\Support\Facades\Route;
use Modules\Front\App\Http\Controllers\AboutUsController;
use Modules\Front\App\Http\Controllers\AuthorController;
use Modules\Front\App\Http\Controllers\CategoryController;
use Modules\Front\App\Http\Controllers\ForumController;
use Modules\Front\App\Http\Controllers\SavedArticlesController;
use Modules\Front\App\Http\Controllers\SearchController;
use Modules\Front\App\Http\Controllers\SitemapController;
use Modules\Front\App\Http\Controllers\TagController;
use Modules\Front\App\Http\Controllers\WeatherController;

Route::get('sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('sitemaps/pages.xml', [SitemapController::class, 'pages'])->name('sitemap.pages');
Route::get('sitemaps/articles-{page}.xml', [SitemapController::class, 'articles'])->whereNumber('page')->name('sitemap.articles');

Route::get('author/{user:username}', AuthorController::class)->name('author.index');

Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

Route::get('tags/{tag:slug}', [TagController::class, 'show'])->name('tags.show');

Route::get('search', SearchController::class)->name('search.index');
Route::get('pogoda', WeatherController::class)->name('weather.index');
Route::get('zapisane', [SavedArticlesController::class, 'index'])->name('saved.index');
Route::get('zapisane/dane', [SavedArticlesController::class, 'data'])->middleware('throttle:30,1')->name('saved.data');
Route::prefix('forum')->name('forum.')->controller(ForumController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/temat/{topic}', 'show')->whereNumber('topic')->name('topics.show');
    Route::get('/dzial/{section}', 'section')->name('sections.show');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/dzial/{section}/nowy', 'create')->name('topics.create');
        Route::post('/dzial/{section}/nowy', 'store')->middleware('throttle:6,1')->name('topics.store');
        Route::get('/temat/{topic}/odpowiedz', 'replyForm')->whereNumber('topic')->name('topics.reply-form');
        Route::post('/temat/{topic}/odpowiedz', 'reply')->whereNumber('topic')->middleware('throttle:12,1')->name('topics.reply');

        Route::middleware('can:comment::reject')->group(function () {
            Route::delete('/temat/{topic}', 'removeTopic')->whereNumber('topic')->name('topics.destroy');
            Route::delete('/temat/{topic}/odpowiedz/{reply}', 'removeReply')->whereNumber(['topic', 'reply'])->name('replies.destroy');
        });
    });
});

Route::get('about-us', AboutUsController::class)->name('about-us.index');
