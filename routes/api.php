<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClothesController;
use App\Http\Controllers\UserController;

/*This is just a test api project using laravel*/

/*-----------------------------------Products-------------------------------------------*/
Route::get('/clothes', [ClothesController::class, 'getAllClothes']);
Route::post('/cloth', [ClothesController::class, 'addNewCloth']);
Route::get('/cloth/{id}', [ClothesController::class, 'getCloth']);
Route::put('/cloth/{id}', [ClothesController::class, 'editCloth']);
Route::delete('/clothImage/{id}', [ClothesController::class, 'deleteClothImage']);
Route::delete('/cloth/{id}', [ClothesController::class, 'deleteCloth']);
/*--------------------------------------------------------------------------------------*/

/*-------------------------------------Users--------------------------------------------*/
Route::get('/users', [UserController::class, 'getAllUsers']);//get all user
Route::get('/user/{id}', [UserController::class, 'getUser']);//get a specific user
Route::post('/users', [UserController::class, 'addNewUser']);//create new user
Route::post('/usersLogin', [UserController::class, 'loginAction']);//loga user
Route::post('/userAuth', [UserController::class, 'authentication']);//check login
Route::put('/user/{id}', [UserController::class, 'editUser']);//edit user
/*--------------------------------------------------------------------------------------*/