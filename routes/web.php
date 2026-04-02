<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PokemonController;
use App\Http\Controllers\CartaController;
use App\Http\Controllers\JogoController;

// Rota principal (Catálogo de Pokémon)
Route::get('/', [PokemonController::class, 'index']);
Route::get('/pokemon/{id}', [PokemonController::class, 'show']);

// Novas Rotas para Cartas
Route::get('/cartas', [CartaController::class, 'index']);