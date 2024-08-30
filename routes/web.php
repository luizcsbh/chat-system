<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ElasticsearchTestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('/test-redis', function () {
    \Illuminate\Support\Facades\Cache::put('key', 'Redis is working!', 10);
    return \Illuminate\Support\Facades\Cache::get('key');
});

Route::get('/test-elasticsearch', [ElasticsearchTestController::class, 'testConnection']);