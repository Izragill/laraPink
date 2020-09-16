<?php

use Illuminate\Support\Facades\Route;

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

Route::resource('/','IndexController',[
    'only' => ['index'],
    'names' => ['index' => 'home']
]);

Route::resource('portfolios', 'PortfolioController', [
    'parameters' => [
        'portfolios' => 'alias'
    ]
]);

Route::resource('articles', 'ArticlesController', [
    'parameters' => [
        'articles' => 'alias'
    ]
]);

Route::get('articles/cat/{cat_alias?}',
    ['uses' => 'ArticlesController@index', 'as' => 'articlesCat'])
    ->where('cat_alias', '[\w-]+');

Route::resource('comment', 'CommentController', ['only' => ['store']]);

Route::match(['get', 'post'], '/contacts', ['uses'=>'ContactsController@index', 'as' => 'contacts']);

//Auth::routes();
Route::get('login', 'Auth\AuthController@showLoginForm');
Route::post('login', 'Auth\AuthController@login');
Route::get('logout', 'Auth\AuthController@logout');

// admin
Route::group(['prefix' => 'admin', 'middleware' => 'auth', 'as' => 'admin.'], function () {
    // admin
    Route::get('/', ['uses' => 'Admin\IndexController@index', 'as' => 'adminIndex']);
    // articles
    Route::resource('articles', 'Admin\ArticlesController');

    Route::resource('permissions', 'Admin\PermissionsController');

    Route::resource('menus', 'Admin\MenusController');

    Route::resource('/users', 'Admin\UsersController');
});
