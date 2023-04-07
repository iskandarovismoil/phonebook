<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group([
    'prefix' => 'auth',
    'name' => 'auth'
], function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/registration', [AuthController::class, 'registration'])->name('registration');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::get('/user', [AuthController::class, 'user'])->name('user');
});

Route::group([
    'prefix' => 'contacts',
    'name' => 'contacts'
], function () {
    Route::get('/', [ContactController::class, 'get'])->name('get');
    Route::get('/{id}', [ContactController::class, 'get'])->name('get_by_id');
    Route::post('/create', [ContactController::class, 'create'])->name('create');
    Route::post('/{id}/edit', [ContactController::class, 'edit'])->name('edit');
    Route::post('/{id}/delete', [ContactController::class, 'delete'])->name('delete');
    Route::post('/search', [ContactController::class, 'search'])->name('search');
});
