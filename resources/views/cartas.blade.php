@php
    $superTipos = ['Pokémon', 'Trainer', 'Energy'];
    
    // Lógica para mudar a cor de fundo por página
    $paginaAtual = request('page', 1);
    $temas = [
        'tema-padrao',  // Vermelho
        'tema-pokemon', // Azul
        'tema-trainer', // Roxo
        'tema-energy',  // Verde
        'tema-dark',    // Escuro
        'tema-fairy'    // Rosa
    ];
    // O operador % (módulo) faz com que o ciclo se repita infinitamente
    $temaAtual = $temas[($paginaAtual - 1) % count($temas)];
@endphp

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokémon TCG - Galeria de Cartas</title>
    <link rel="stylesheet" href="{{ asset('css/cartas.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="{{ $temaAtual }}">

    <header>
        <h1>Galeria TCG</h1>
        <a href="/" class="btn-voltar">Voltar para Pokédex</a>
    </header>

    <form method="GET" action="/cartas" class="form-filtros" id="form-filtros">

        <div class="linha-busca">
            <input type="text" name="busca" class="input-busca"
                placeholder="Buscar carta por nome (Ex: Charizard, Potion)..."
                value="{{ request('busca') }}">
            <button type="submit" class="btn-filtrar">Buscar</button>
        </div>

        <div class="linha-seletores">
            <select name="raridade" onchange="this.form.submit()">
                <option value="">Todas as Raridades</option>
                @foreach($raridadesDisponiveis as $raridade)
                    <option value="{{ $raridade }}" {{ request('raridade') == $raridade ? 'selected' : '' }}>
                        {{ $raridade }}
                    </option>
                @endforeach
            </select>

            <select name="ordem" onchange="this.form.submit()">
                <option value="preco_desc" {{ request('ordem') == 'preco_desc' ? 'selected' : '' }}>Maior Preço ($)</option>
                <option value="preco_asc" {{ request('ordem') == 'preco_asc' ? 'selected' : '' }}>Menor Preço ($)</option>
                <option value="a_z" {{ request('ordem') == 'a_z' ? 'selected' : '' }}>A - Z</option>
                <option value="z_a" {{ request('ordem') == 'z_a' ? 'selected' : '' }}>Z - A</option>
            </select>
        </div>

        <div class="linha-tipos">
            <label class="label-tipo" title="Todas as Cartas">
                <input type="radio" name="supertype" value="" onchange="this.form.submit()"
                    {{ empty(request('supertype')) ? 'checked' : '' }}>
                <span class="icone-tipo icone-todos">Todos</span>
            </label>

            @foreach($superTipos as $tipo)
            <label class="label-tipo" title="{{ $tipo }}">
                <input type="radio" name="supertype" value="{{ $tipo }}" onchange="this.form.submit()"
                    {{ request('supertype') == $tipo ? 'checked' : '' }}>
                <span class="icone-tipo">{{ $tipo }}</span>
            </label>
            @endforeach
        </div>
    </form>

    <div class="grid-cartas">
        @forelse ($cartas as $carta)
            <div class="cartao-tcg">
                <div class="imagem-container">
                    @if($carta['img'])
                        <img src="{{ $carta['img'] }}" alt="{{ $carta['nome'] }}" loading="lazy">
                    @else
                        <div class="sem-imagem">Sem Imagem</div>
                    @endif
                </div>
                <div class="info-carta">
                    <div class="textos-superiores">
                        <p class="nome-carta">{{ $carta['nome'] }}</p>
                        <p class="colecao-carta">{{ $carta['colecao'] }} • {{ $carta['raridade'] }}</p>
                    </div>
                    <p class="preco-carta">
                        {{ $carta['preco_dolar'] > 0 ? '$ ' . number_format($carta['preco_dolar'], 2) : 'Sem Preço' }}
                    </p>
                </div>
            </div>
        @empty
            <div class="mensagem-vazia">
                <h2>Nenhuma carta encontrada!</h2>
                <p>Execute o comando <br><code>php artisan cartas:gerar</code><br> para baixar as cartas.</p>
            </div>
        @endforelse
    </div>

    <div class="paginacao-container">
        {{ $cartas->appends(request()->all())->links() }}
    </div>

</body>
</html>