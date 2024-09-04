<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;

//Route::get('/user', function (Request $request) {
  //  return $request->user();
//})->middleware('auth:sanctum');

// Routes pour l'inscription et la connexion
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Routes pour la gestion des utilisateurs
Route::middleware('auth:sanctum')->group(function () {
    // Route pour afficher les informations de l'utilisateur connecté
    Route::get('/user', [AuthController::class, 'user']);

    // Route pour mettre à jour les informations de l'utilisateur connecté
    Route::put('/user', [AuthController::class, 'update']);

    // Route pour la déconnexion
    Route::post('/logout', [AuthController::class, 'logout']);


  // Routes pour la gestion des transactions
  // Route pour lister toutes les transactions de l'utilisateur connecté
  Route::get('/transactions', [TransactionController::class, 'index']);
  // Route pour afficher une transaction spécifique
  Route::get('/transactions/{id}', [TransactionController::class, 'show']);
  // Route pour créer une nouvelle transaction
  Route::post('/transactions', [TransactionController::class, 'store']);
   // Route pour mettre à jour une transaction existante
  Route::put('/transactions/{id}', [TransactionController::class, 'update']);
  // Route pour supprimer une transaction
  Route::delete('/transactions/{id}', [TransactionController::class, 'destroy']);
});