<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PokemonController extends Controller
{
    public function index(Request $request)
    {
        // 1. Pegamos todos os pokémon e transformamos em Collection
        $pokemons = collect(config('pokemon'));

        // 2. Busca por Nome ou Número (NOVO)
        if ($request->filled('busca')) {
            $termo = strtolower($request->busca);
            $pokemons = $pokemons->filter(function ($pokemon) use ($termo) {
                // Procura parte do nome ou o número exato do ID
                return str_contains(strtolower($pokemon['nome']), $termo) || (string)$pokemon['id'] === $termo;
            });
        }

        // 3. Filtro por Tipo (NOVO)
        if ($request->filled('tipo')) {
            $tipoSelecionado = $request->tipo;
            $pokemons = $pokemons->filter(function ($pokemon) use ($tipoSelecionado) {
                // Verifica se o tipo pesquisado está dentro do array de tipos do Pokémon
                return in_array($tipoSelecionado, $pokemon['tipo'] ?? []);
            });
        }

        // 4. Filtro por Geração
        if ($request->filled('geracao')) {
            $pokemons = $pokemons->where('geracao', (int) $request->geracao);
        }

        // 5. Ordenação
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