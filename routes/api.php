<?php

use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CharactersController;

Route::post('/registro', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/personajes', [CharactersController::class, 'getAll']);

Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::get('/favoritos', [CharactersController::class, 'getFavorites']);
    Route::post('/favoritos', [CharactersController::class, 'saveCharacter']);
    Route::delete('/favoritos/{id}', [CharactersController::class, 'deleteCharacter']);
});

Route::any('{any}', function(){
    return response(['error' => 'No hay such route'], Response::HTTP_NOT_FOUND);
})->where('any', '.*');

