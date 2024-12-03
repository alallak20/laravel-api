<?php

use App\Livewire\Search;
use App\Livewire\ArticleIndex;
use App\Livewire\ShowArticle;
use App\Livewire\Dashboard;
use App\Livewire\ArticleList;
use App\Livewire\CreateArticle;
use App\Livewire\EditArticle;
use Illuminate\Support\Facades\Route;

Route::get('/', ArticleIndex::class,);

//Route::get('/search', Search::class);
Route::get('/articles/{article}', showArticle::class);
Route::get('/dashboard', Dashboard::class);
Route::get('/dashboard/articles', ArticleList::class);
Route::get('/dashboard/articles/create', CreateArticle::class);
Route::get('/dashboard/articles/{article}/edit', EditArticle::class);


//Route::middleware([
//    'auth:sanctum',
//    config('jetstream.auth_session'),
//    'verified',
//])->group(function () {
//    Route::get('/dashboard', function () {
//        return view('dashboard');
//    })->name('dashboard');
//});