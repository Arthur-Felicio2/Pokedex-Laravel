@php
// Lista de tipos para gerar os botões dinamicamente
$tipos = [
'normal', 'fire', 'water', 'electric', 'grass', 'ice',
'fighting', 'poison', 'ground', 'flying', 'psychic', 'bug',
'rock', 'ghost', 'dragon', 'dark', 'steel', 'fairy'
];
// Define o tema atual. Se não houver tipo, usa o padrão (vermelho)
$tipoSelecionado = request('tipo', 'padrao');
@endphp

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokédex Completa</title>
    <link rel="stylesheet" href="{{ asset('css/pokedex.css') }}">
</head>

<body class="tema-{{ $tipoSelecionado }}">

    <header class="cabecalho-principal">
        <h1>Pokédex</h1>
        <div class="botoes-navegacao">
            <a href="/cartas" class="btn-voltar">Ver Cartas TCG</a>
        </div>
    </header>
    <form method="GET" action="/" class="form-filtros" id="form-filtros">

        <div class="linha-busca">
            <input type="text" name="busca" class="input-busca"
                placeholder="Buscar Pokémon por nome..."
                value="{{ request('busca') }}">
            <button type="submit" class="btn-filtrar">Buscar</button>
        </div>

        <div class="linha-seletores">
            <select name="geracao" onchange="this.form.submit()">
                <option value="">Todas as Gerações</option>
                @for($g = 1; $g <= 9; $g++)
                    <option value="{{ $g }}" {{ request('geracao') == $g ? 'selected' : '' }}>
                    {{ $g }}ª Geração
                    </option>
                    @endfor
            </select>

            <select name="ordem" onchange="this.form.submit()">
                <option value="id_asc" {{ request('ordem') == 'id_asc' ? 'selected' : '' }}>Menor Número (#)</option>
                <option value="id_desc" {{ request('ordem') == 'id_desc' ? 'selected' : '' }}>Maior Número (#)</option>
                <option value="a_z" {{ request('ordem') == 'a_z' ? 'selected' : '' }}>A - Z</option>
                <option value="z_a" {{ request('ordem') == 'z_a' ? 'selected' : '' }}>Z - A</option>
            </select>
        </div>

        <div class="linha-tipos">
            <label class="label-tipo" title="Todos os Tipos">
                <input type="radio" name="tipo" value="" onchange="this.form.submit()"
                    {{ empty(request('tipo')) ? 'checked' : '' }}>
                <span class="icone-tipo icone-todos">All</span>
            </label>

            @foreach($tipos as $tipo)
            <label class="label-tipo" title="{{ ucfirst($tipo) }}">
                <input type="radio" name="tipo" value="{{ $tipo }}" onchange="this.form.submit()"
                    {{ request('tipo') == $tipo ? 'checked' : '' }}>
                <span class="icone-tipo">
                    <img src="https://raw.githubusercontent.com/duiker101/pokemon-type-svg-icons/master/icons/{{ $tipo }}.svg" alt="{{ $tipo }}">
                </span>
            </label>
            @endforeach
        </div>
    </form>

    <div class="grid-pokedex">
        @foreach ($pokemons as $pokemon)
        <a href="/pokemon/{{ $pokemon['id'] }}?{{ http_build_query(request()->all()) }}" class="link-cartao">
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