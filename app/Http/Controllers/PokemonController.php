<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PokemonController extends Controller
{
    public function index(Request $request)
    {
        // 1. Pegamos todos os pokémon e transformamos em Collection
        $pokemons = collect(config('pokemon'));

        // 2. Filtro por Geração
        if ($request->filled('geracao')) {
            $pokemons = $pokemons->where('geracao', (int) $request->geracao);
        }

        // 3. Ordenação
        $ordem = $request->input('ordem', 'id_asc'); // Padrão é Menor Número

        $pokemons = match ($ordem) {
            'id_desc' => $pokemons->sortByDesc('id'),
            'a_z'     => $pokemons->sortBy('nome'),
            'z_a'     => $pokemons->sortByDesc('nome'),
            default   => $pokemons->sortBy('id'), // id_asc
        };

        // Retornamos para a view
        return view('pokedex', compact('pokemons'));
    }

    public function show($id)
    {
        // Busca o pokemon pelo ID direto no array de configuração
        $pokemon = config("pokemon.{$id}");

        // Se o Pokémon não existir no array, retorna erro 404
        if (!$pokemon) {
            abort(404, 'Pokémon não encontrado');
        }

        return view('pokemon', compact('pokemon'));
    }
}
