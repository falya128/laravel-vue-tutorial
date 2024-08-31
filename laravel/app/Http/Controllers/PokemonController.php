<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PokemonController extends Controller
{
    /**
     * お気に入り取得
     */
    public function fetchFavorites(Request $request): JsonResponse
    {
        $favorites = Favorite::get()->pluck('pokemon_english_name');
        return response()->json($favorites);
    }

    /**
     * お気に入り登録
     */
    public function addFavorite(Request $request): JsonResponse
    {
        Favorite::updateOrCreate(
            ['pokemon_english_name' => $request->pokemon_english_name],
            ['pokemon_english_name' => $request->pokemon_english_name]
        );
        return response()->json();
    }

    /**
     * お気に入り解除
     */
    public function deleteFavorite(Request $request): JsonResponse
    {
        Favorite::where('pokemon_english_name', $request->pokemon_english_name)->delete();
        return response()->json();
    }
}
