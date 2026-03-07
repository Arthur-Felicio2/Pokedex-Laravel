<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PokemonController;

// Rota principal (Catálogo)
Route::get('/', [PokemonController::class, 'index']);

// Rota de detalhes
Route::get('/pokemon/{id}', [PokemonController::class, 'show']);