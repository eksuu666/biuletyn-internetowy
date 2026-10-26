<?php

use Illuminate\Support\Facades\Route;
use Modules\Front\App\Http\Controllers\AboutUsController;
use Modules\Front\App\Http\Controllers\AuthorController;
use Modules\Front\App\Http\Controllers\CategoryController;
use Modules\Front\App\Http\Controllers\SearchController;
use Modules\Front\App\Http\Controllers\SitemapController;
use Modules\Front\App\Http\Controllers\TagController;

Route::get('sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('sitemaps/pages.xml', [SitemapController::class, 'pages'])->name('sitemap.pages');
Route::get('sitemaps/articles-{page}.xml', [SitemapController::class, 'articles'])->whereNumber('page')->name('sitemap.articles');

Route::get('author/{user:username}', AuthorController::class)->name('author.index');

Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

Route::get('tags/{tag:slug}', [TagController::class, 'show'])->name('tags.show');

Route::get('search', SearchController::class)->name('search.index');

Route::get('about-us', AboutUsController::class)->name('about-us.index');
