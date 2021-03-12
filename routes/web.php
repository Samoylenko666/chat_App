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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post('/getFriends', [App\Http\Controllers\Controller::class, 'getFriends']);//Метод для получения списка всех пользователей(Изменить на друзей)
Route::post('/session/create', [App\Http\Controllers\SessionController::class, 'create']);// Метод для создания сессии
Route::post('/send/{session}', [App\Http\Controllers\ChatController::class, 'send']);//метод для отправки смс
Route::post('/session/{session}/chats', [App\Http\Controllers\ChatController::class, 'getChats']);//метод для получения всех сообщений для текущего чата
Route::post('/session/{session}/read', [App\Http\Controllers\ChatController::class, 'read']);//метод для записси в бд когда прочитано сообщение