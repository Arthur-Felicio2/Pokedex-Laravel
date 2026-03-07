<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokédex Completa</title>
    <link rel="stylesheet" href="{{ asset('css/pokedex.css') }}">
    <style>
        /* Um CSS simples para o formulário ficar bonito */
        .filtros { display: flex; gap: 10px; justify-content: center; margin-bottom: 20px; }
        .filtros select, .filtros button { padding: 8px 15px; border-radius: 5px; border: 1px solid #ccc; }
        .filtros button { background-color: #e3350d; color: white; cursor: pointer; border: none; font-weight: bold;}
    </style>
</head>
<body>
    <header>
        <h1>Pokédex Nacional</h1>
    </header>

    <form method="GET" action="/" class="filtros">
        <select name="geracao">
            <option value="">Todas as Gerações</option>
            @for($g = 1; $g <= 9; $g++)
                <option value="{{ $g }}" {{ request('geracao') == $g ? 'selected' : '' }}>
                    {{ $g }}ª Geração
                </option>
            @endfor
        </select>

        <select name="ordem">
            <option value="id_asc" {{ request('ordem') == 'id_asc' ? 'selected' : '' }}>Menor Número (#)</option>
            <option value="id_desc" {{ request('ordem') == 'id_desc' ? 'selected' : '' }}>Maior Número (#)</option>
            <option value="a_z" {{ request('ordem') == 'a_z' ? 'selected' : '' }}>A - Z</option>
            <option value="z_a" {{ request('ordem') == 'z_a' ? 'selected' : '' }}>Z - A</option>
        </select>

        <button type="submit">Filtrar</button>
    </form>

    <div class="grid-pokedex">
        @foreach ($pokemons as $pokemon)
            <a href="/pokemon/{{ $pokemon['id'] }}" style="text-decoration: none; color: inherit;">
                <div class="cartao-mini">
                    <span class="numero">#{{ str_pad($pokemon['id'], 3, '0', STR_PAD_LEFT) }}</span>
                    <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/{{ $pokemon['id'] }}.png" alt="{{ $pokemon['nome'] }}" loading="lazy">
                    <p class="nome-pokemon">{{ $pokemon['nome'] }}</p>
                </div>
            </a>
        @endforeach
    </div>

</body>
</html>