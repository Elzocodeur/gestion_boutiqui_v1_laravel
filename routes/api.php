<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DetteController;




Route::prefix('v1/auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/token/refresh', [AuthController::class, 'refreshToken']);
});

// Routes protégées par authentification
Route::middleware(['auth:api'])->prefix('v1')->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

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
    Route::middleware(['auth:api'])->get('/api/v1/clients', [ClientController::class, 'index']);


    // Routes pour les utilisateurs
    Route::apiResource('/users', UserController::class)->only(['index', 'store', 'show']);



    // dette
    Route::apiResource('/dettes', DetteController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    // Route::post('/dettes', [DetteController::class, 'store']);


    // Ajoutez votre route protégée ici
    Route::get('/route-protegee', function () {
        return 'Bienvenue sur la route protégée !';
    });
});



