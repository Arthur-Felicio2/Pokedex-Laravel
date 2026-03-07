<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokédex - {{ $pokemon['nome'] }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <a href="/" class="btn-voltar">⬅ Voltar</a>

    <div class="pokemon-container">
        <div class="pokemon-card-detalhe" id="card-pokemon">
            <div class="card-esquerda">
                <h2>{{ $pokemon['nome'] }}</h2>
                <img id="poke-img" src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/{{ $pokemon['id'] }}.png" alt="{{ $pokemon['nome'] }}">
                <button id="btn-ataque">Ação / Animação!</button>
            </div>

            <div class="card-direita">
                <div class="grid-info">
                    <div class="info-box">
                        <small>Tipo</small>
                        <p>{{ $pokemon['tipo'] }}</p>
                    </div>
                    <div class="info-box">
                        <small>Peso Médio</small>
                        <p>{{ $pokemon['peso'] }} kg</p>
                    </div>
                    <div class="info-box">
                        <small>Tamanho</small>
                        <p>{{ $pokemon['altura'] }} m</p>
                    </div>
                    <div class="info-box">
                        <small>HP Max</small>
                        <p>{{ $pokemon['hp_max'] }}</p>
                    </div>
                </div>

                <div class="pokedex-entry">
                    <h3>Descrição Pokedex</h3>
                    <p>{{ $pokemon['descricao'] }}</p>
                </div>
                
                @if(isset($pokemon['habilidades']))
                <div class="habilidades-entry">
                    <h3>Habilidades</h3>
                    <ul>
                        @foreach($pokemon['habilidades'] as $habilidade)
                            <li><strong>{{ $habilidade['nome'] }}</strong>: Causa {{ $habilidade['dano'] }} de dano.</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // O JavaScript agora fica apenas para interações de tela, como a animação
        document.getElementById('btn-ataque').addEventListener('click', () => {
            const img = document.getElementById('poke-img');
            img.classList.add('ataque-animacao');
            setTimeout(() => img.classList.remove('ataque-animacao'), 500);
        });
    </script>
</body>
</html>