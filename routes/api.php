<?php

use App\Http\Controllers\Actions\Post\CreatePostAction;
use App\Http\Controllers\Actions\Post\RetrieveUserPostsAction;
use App\Http\Controllers\Actions\Post\UpdatePostAction;
use App\Http\Controllers\Actions\User\Google\ActivateGoogleUserAction;
use App\Http\Controllers\Actions\User\Google\RetrieveRedirectUriAction;
use App\Http\Controllers\Actions\User\LoginAction;
use App\Http\Controllers\Actions\User\RetrieveAction;
use App\Http\Controllers\Actions\User\SendAuthCodeAction;
use App\Http\Controllers\Actions\User\ValidateAuthCodeAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::group(['middleware' => 'api'], function ($router) {
//     Route::post('/users/login', LoginAction::class);
//     Route::post('/users', RetrieveAction::class)->middleware('auth:jwt');
// });

Route::post('users/register', SendAuthCodeAction::class)->name('send.auth.code');
Route::post('users/activate', ValidateAuthCodeAction::class)->name('validate.auth.code');
Route::get('users/mypage', RetrieveUserPostsAction::class)->name('user.mypage');

//Socialiteがセッションを使うから
Route::middleware('web')->group(function () {
    // auth/redirect Googleの認証ページへのリダイレクトuriを返す
    Route::get('auth/redirect', RetrieveRedirectUriAction::class)->name('google.redirect');
    Route::get('auth/callback', ActivateGoogleUserAction::class)->name('google.activate');
});



Route::middleware('auth')->group(function() {
    Route::post('posts/register', CreatePostAction::class)->name('create.post');
    Route::post('posts/update/{post}', UpdatePostAction::class)->name('post.update');
});
