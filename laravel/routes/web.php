<?php

use Illuminate\Support\Facades\Route;

Route::get('/pokemons', function () {
    return view('index');
});
