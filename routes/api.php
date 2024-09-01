<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\UserController;



// Route::prefix('v1')->group(function () {
//     Route::post('/login', [AuthController::class, 'login']);
//     Route::post('/token/refresh', [AuthController::class, 'refreshToken']);

//     Route::middleware('auth:api')->post('/register', [AuthController::class, 'register']);
//     Route::middleware('auth:api')->post('/logout', [AuthController::class, 'logout']);
// });



// Route::prefix('v1')->group(function () {
//     Route::apiResource('/articles', ArticleController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
// });

// Route::prefix('v1')->group(function () {
//     Route::post('/articles/libelle/{libelle}', [ArticleController::class, 'getByLibelle']);
// });

// Route::prefix('v1')->group(function () {
//     Route::post('articles/stock', [ArticleController::class, 'updateStock']);
//     Route::patch('articles/{id}', [ArticleController::class, 'updateStockById']);
// });

// Route::prefix('v1')->group(function () {
//     Route::apiResource('/clients', ClientController::class)->only(['index', 'store', 'show']);
//     Route::patch('/clients/{id}/add-user', [ClientController::class, 'addUserToClient']);
//     Route::post('clients/telephone/{telephone}', [ClientController::class, 'showClientByTelephone']);
//     Route::post('clients/{id}/dettes', [ClientController::class, 'listDettesClient']);
//     Route::post('clients/{id}/user', [ClientController::class, 'showClientWithUser']);
// });

// Route::prefix('v1')->group(function () {
//     Route::apiResource('/users', UserController::class)->only(['index', 'store', 'show']);
// });















Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/token/refresh', [AuthController::class, 'refreshToken']);
    // Route d'enregistrement, si nécessaire
    // Route::post('/register', [AuthController::class, 'register']);
});

// Routes protégées par authentification
Route::middleware(['auth:api', 'check.auth'])->prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Routes pour les articles
    Route::apiResource('/articles', ArticleController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    Route::post('/articles/libelle/{libelle}', [ArticleController::class, 'getByLibelle']);
    Route::post('/articles/stock', [ArticleController::class, 'updateStock']);
    Route::patch('/articles/{id}', [ArticleController::class, 'updateStockById']);

    // Routes pour les clients
    Route::apiResource('/clients', ClientController::class)->only(['index', 'store', 'show']);
    Route::patch('/clients/{id}/add-user', [ClientController::class, 'addUserToClient']);
    Route::post('/clients/telephone/{telephone}', [ClientController::class, 'showClientByTelephone']);
    Route::post('/clients/{id}/dettes', [ClientController::class, 'listDettesClient']);
    Route::post('/clients/{id}/user', [ClientController::class, 'showClientWithUser']);

    // Routes pour les utilisateurs
    Route::apiResource('/users', UserController::class)->only(['index', 'store', 'show']);

    // Ajoutez votre route protégée ici
    Route::get('/route-protegee', function () {
        return 'Bienvenue sur la route protégée !';
    });
});
