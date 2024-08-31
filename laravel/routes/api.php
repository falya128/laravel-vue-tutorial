<?php

use Illuminate\Support\Facades\Route;

Route::get('/pokemons/favorites', [App\Http\Controllers\PokemonController::class, 'fetchFavorites']);
Route::post('/pokemons/add-favorite', [App\Http\Controllers\PokemonController::class, 'addFavorite']);
Route::post('/pokemons/delete-favorite', [App\Http\Controllers\PokemonController::class, 'deleteFavorite']);
