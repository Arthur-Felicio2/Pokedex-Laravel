<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator; // <-- Importante adicionar isso!

class CartaController extends Controller
{
    public function index(Request $request)
    {
        $cartas = collect(config('cartas', []));

        // 1. Filtros (Mantidos iguais)
        if ($request->filled('busca')) {
            $termo = strtolower($request->busca);
            $cartas = $cartas->filter(function ($carta) use ($termo) {
                return str_contains(strtolower($carta['nome']), $termo);
            });
        }

        if ($request->filled('supertype')) {
            $cartas = $cartas->where('supertype', $request->supertype);
        }

        if ($request->filled('raridade')) {
            $cartas = $cartas->where('raridade', $request->raridade);
        }

        // 2. Ordenação
        $ordem = $request->input('ordem', 'preco_desc');
        $cartas = match ($ordem) {
            'preco_asc'  => $cartas->sortBy('preco_dolar'),
            'preco_desc' => $cartas->sortByDesc('preco_dolar'),
            'a_z'        => $cartas->sortBy('nome'),
            'z_a'        => $cartas->sortByDesc('nome'),
            default      => $cartas->sortByDesc('preco_dolar'),
        };

        // 3. PAGINAÇÃO MANUAL PARA A COLLECTION
        $paginaAtual = $request->get('page', 1);
        $porPagina = 40; // Quantidade de cartas por página (ajuste como quiser)
        
        $fatia = $cartas->slice(($paginaAtual - 1) * $porPagina, $porPagina)->values();

        $cartasPaginadas = new LengthAwarePaginator(
            $fatia,
            $cartas->count(),
            $porPagina,
            $paginaAtual,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Extrai as raridades
        $raridadesDisponiveis = collect(config('cartas', []))->pluck('raridade')->unique()->filter()->sort();

        // Passa a variável paginada para a view
        return view('cartas', [
            'cartas' => $cartasPaginadas,
            'raridadesDisponiveis' => $raridadesDisponiveis
        ]);
    }
}