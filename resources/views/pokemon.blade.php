<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokédex - {{ $pokemon['nome'] }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

@php
$coresTipos = [
'normal' => '#A8A878', 'fire' => '#F08030', 'water' => '#6890F0',
'electric' => '#F8D030', 'grass' => '#78C850', 'ice' => '#98D8D8',
'fighting' => '#C03028', 'poison' => '#A040A0', 'ground' => '#E0C068',
'flying' => '#A890F0', 'psychic' => '#F85888', 'bug' => '#A8B820',
'rock' => '#B8A038', 'ghost' => '#705898', 'dragon' => '#7038F8',
'dark' => '#705848', 'steel' => '#B8B8D0', 'fairy' => '#EE99AC'
];

$cor1 = $coresTipos[$pokemon['tipo'][0]] ?? '#ccc';
$cor2 = isset($pokemon['tipo'][1]) ? $coresTipos[$pokemon['tipo'][1]] : $cor1;

if (isset($pokemon['tipo'][1])) {
$bgBody = "linear-gradient(to bottom left, {$cor1} 0%, {$cor1} calc(50% - 5px), #ffffff calc(50% - 5px), #ffffff calc(50% + 5px), {$cor2} calc(50% + 5px), {$cor2} 100%)";
} else {
$bgBody = $cor1;
}

$todasAsFormas = [
'normal' => [
'nome' => $pokemon['nome'],
'tipos' => $pokemon['tipo'],
'status' => $pokemon['status'],
'img' => $pokemon['img_oficial']
]
];

if (!empty($pokemon['img_shiny'])) {
$todasAsFormas['shiny'] = [
'nome' => $pokemon['nome'] . ' (Shiny)',
'tipos' => $pokemon['tipo'],
'status' => $pokemon['status'],
'img' => $pokemon['img_shiny']
];
}

foreach ($pokemon['formas_alternativas'] as $index => $forma) {
if(empty($forma['img_oficial'])) continue;

$key = 'forma_' . $index;
$todasAsFormas[$key] = [
'nome' => $forma['nome'],
'tipos' => $forma['tipos'],
'status' => $forma['status'],
'img' => $forma['img_oficial']
];

if (!empty($forma['img_shiny'])) {
$todasAsFormas[$key.'_shiny'] = [
'nome' => $forma['nome'] . ' (Shiny)',
'tipos' => $forma['tipos'],
'status' => $forma['status'],
'img' => $forma['img_shiny']
];
}
}
@endphp

<body style="background: {{ $bgBody }}; background-attachment: fixed;" id="body-bg" class="body-detalhe">

    <a href="/?{{ http_build_query(request()->all()) }}" class="btn-voltar">⬅ Voltar</a>

    <div class="main-wrapper">
        <div class="formas-menu">
            <button class="btn-forma ativo" onclick="mudarForma('normal', this)">Normal</button>
            @if(!empty($pokemon['img_shiny']))
            <button class="btn-forma" onclick="mudarForma('shiny', this)">✨ Shiny</button>
            @endif

            @foreach ($pokemon['formas_alternativas'] as $index => $forma)
            @if(!empty($forma['img_oficial']))
            <button class="btn-forma" onclick="mudarForma('forma_{{ $index }}', this)">{{ $forma['tipo_forma'] }}</button>
            @if(!empty($forma['img_shiny']))
            <button class="btn-forma" onclick="mudarForma('forma_{{ $index }}_shiny', this)">✨ {{ $forma['tipo_forma'] }} Shiny</button>
            @endif
            @endif
            @endforeach
        </div>

        <div class="pokemon-card-detalhe" id="card-pokemon">
            <div class="moldura-esquerda" id="moldura-tipo" style="background: linear-gradient(135deg, {{ $cor1 }} 0%, {{ $cor2 }} 100%);">
                <div class="pedestal-pokemon">
                    <img id="poke-img" src="{{ $pokemon['img_oficial'] }}" alt="{{ $pokemon['nome'] }}">
                </div>
            </div>

            <div class="card-direita">
                <div class="topo-direita">
                    <h2 id="poke-nome">{{ $pokemon['nome'] }}</h2>
                    <button class="btn-acao" id="btn-ataque" style="background-color: {{ $cor1 }};">Ação!</button>
                </div>

                <div class="grid-info">
                    <div class="info-box box-span-2">
                        <small>Tipos</small>
                        <div id="container-tipos" class="tipos-wrapper"></div>
                    </div>

                    <div class="info-box">
                        <small>Peso Médio</small>
                        <p>{{ $pokemon['peso'] }} kg</p>
                    </div>
                    <div class="info-box">
                        <small>Tamanho</small>
                        <p>{{ $pokemon['altura'] }} m</p>
                    </div>
                </div>

                <div class="pokedex-entry">
                    <h3>Descrição Pokedex</h3>
                    <p>{{ $pokemon['descricao'] }}</p>
                </div>

                <div class="status-container">
                    <h3>Status Base</h3>
                    <div id="container-status"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const dadosFormas = @json($todasAsFormas);
        const coresTipos = @json($coresTipos);

        function renderizarTipos(tipos) {
            const container = document.getElementById('container-tipos');
            container.innerHTML = '';
            tipos.forEach(tipo => {
                const cor = coresTipos[tipo] || '#ccc';
                container.innerHTML += `
                    <div class="badge-tipo" style="background-color: ${cor};">
                        <img src="https://raw.githubusercontent.com/duiker101/pokemon-type-svg-icons/master/icons/${tipo}.svg">
                        ${tipo}
                    </div>
                `;
            });
        }

        function renderizarStatus(status, corPrincipal) {
            const container = document.getElementById('container-status');
            container.innerHTML = '';
            for (const [nome, valor] of Object.entries(status)) {
                const porcentagem = Math.min(100, (valor / 255) * 100);
                container.innerHTML += `
                    <div class="status-row">
                        <span class="status-label">${nome}</span>
                        <span class="status-valor">${valor}</span>
                        <div class="status-bar-bg">
                            <div class="status-bar-fill" style="width: ${porcentagem}%; background-color: ${corPrincipal};"></div>
                        </div>
                    </div>
                `;
            }
        }

        function atualizarCoresFundo(tipos) {
            const cor1 = coresTipos[tipos[0]] || '#ccc';
            const cor2 = tipos[1] ? coresTipos[tipos[1]] : cor1;

            document.getElementById('moldura-tipo').style.background = `linear-gradient(135deg, ${cor1} 0%, ${cor2} 100%)`;
            document.getElementById('btn-ataque').style.backgroundColor = cor1;

            const bodyBg = tipos[1] ?
                `linear-gradient(to bottom left, ${cor1} 0%, ${cor1} calc(50% - 5px), #ffffff calc(50% - 5px), #ffffff calc(50% + 5px), ${cor2} calc(50% + 5px), ${cor2} 100%)` :
                cor1;
            document.getElementById('body-bg').style.background = bodyBg;

            return cor1;
        }

        function mudarForma(chaveForma, btnElement) {
            document.querySelectorAll('.btn-forma').forEach(b => b.classList.remove('ativo'));
            btnElement.classList.add('ativo');

            const dados = dadosFormas[chaveForma];
            const imgEl = document.getElementById('poke-img');

            imgEl.classList.add('fade-out');

            setTimeout(() => {
                imgEl.src = dados.img;
                document.getElementById('poke-nome').innerText = dados.nome;

                const corPrincipal = atualizarCoresFundo(dados.tipos);
                renderizarTipos(dados.tipos);
                renderizarStatus(dados.status, corPrincipal);

                imgEl.classList.remove('fade-out');
            }, 300);
        }

        // Init
        renderizarTipos(dadosFormas['normal'].tipos);
        renderizarStatus(dadosFormas['normal'].status, coresTipos[dadosFormas['normal'].tipos[0]]);

        document.getElementById('btn-ataque').addEventListener('click', () => {
            const img = document.getElementById('poke-img');
            img.style.transform = 'scale(1.2) rotate(10deg)';
            setTimeout(() => img.style.transform = 'scale(1) rotate(0deg)', 300);
        });
    </script>
</body>

</html>