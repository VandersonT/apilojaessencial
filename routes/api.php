<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClothesController;

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

/*--------------------------------------------------------------------------------------*/