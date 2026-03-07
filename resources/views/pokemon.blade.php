<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokédex - {{ $pokemon['nome'] }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        /* CSS Adicional para o novo layout, sem mexer no app.css */
        
        /* O contêiner do card agora é o pedestal branco */
        .pokemon-card-detalhe {
            background-color: #ffffff !important; /* Força o fundo branco */
            padding: 20px !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.4) !important; /* Sombra mais forte */
        }

        /* Moldura colorida na esquerda */
        .moldura-esquerda {
            border-radius: 20px;
            padding: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 20px;
        }

        /* O círculo pedestal branco onde o pokemon fica */
        .pedestal-pokemon {
            background-color: #ffffff;
            border-radius: 50%;
            width: 280px;
            height: 280px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: inset 0 0 20px rgba(0,0,0,0.1);
        }

        .pedestal-pokemon img {
            width: 240px;
            height: 240px;
            object-fit: contain;
            filter: drop-shadow(0px 10px 10px rgba(0,0,0,0.3));
        }

        /* Nome do Pokémon no topo da direita */
        .topo-direita {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }

        .topo-direita h2 {
            margin: 0;
            font-size: 2.2em;
            text-transform: capitalize;
            color: #333;
        }

        .topo-direita button {
            margin: 0;
        }

        /* Responsividade para o novo layout */
        @media (max-width: 600px) {
            .moldura-esquerda {
                margin-right: 0;
                margin-bottom: 20px;
            }
            .pedestal-pokemon {
                width: 200px;
                height: 200px;
            }
            .pedestal-pokemon img {
                width: 170px;
                height: 170px;
            }
            .topo-direita {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
        }
    </style>
</head>

@php
    // Dicionário de Cores para os Tipos
    $coresTipos = [
        'normal' => '#A8A878', 'fire' => '#F08030', 'water' => '#6890F0',
        'electric' => '#F8D030', 'grass' => '#78C850', 'ice' => '#98D8D8',
        'fighting' => '#C03028', 'poison' => '#A040A0', 'ground' => '#E0C068',
        'flying' => '#A890F0', 'psychic' => '#F85888', 'bug' => '#A8B820',
        'rock' => '#B8A038', 'ghost' => '#705898', 'dragon' => '#7038F8',
        'dark' => '#705848', 'steel' => '#B8B8D0', 'fairy' => '#EE99AC'
    ];

    // Define a cor 1 e cor 2
    $cor1 = $coresTipos[$pokemon['tipo'][0]] ?? '#ccc';
    $cor2 = isset($pokemon['tipo'][1]) ? $coresTipos[$pokemon['tipo'][1]] : $cor1;

    // Fundo diagonal do Body (permanece)
    if (isset($pokemon['tipo'][1])) {
        $bgBody = "linear-gradient(to bottom left, {$cor1} 0%, {$cor1} calc(50% - 5px), #ffffff calc(50% - 5px), #ffffff calc(50% + 5px), {$cor2} calc(50% + 5px), {$cor2} 100%)";
    } else {
        $bgBody = $cor1;
    }
@endphp

<body style="background: {{ $bgBody }}; min-height: 100vh; margin: 0; background-attachment: fixed; padding: 20px;">

    <a href="/" class="btn-voltar">⬅ Voltar</a>

    <div class="pokemon-container">
        <div class="pokemon-card-detalhe" id="card-pokemon" style="display: flex; align-items: stretch;">
            
            <div class="moldura-esquerda" style="background: linear-gradient(135deg, {{ $cor1 }} 0%, {{ $cor2 }} 100%); flex: none;">
                <div class="pedestal-pokemon">
                    <img id="poke-img" src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/{{ $pokemon['id'] }}.png" alt="{{ $pokemon['nome'] }}">
                </div>
            </div>

            <div class="card-direita" style="flex: 1; padding: 0 10px;">
                
                <div class="topo-direita">
                    <h2>{{ $pokemon['nome'] }}</h2>
                    <button id="btn-ataque" style="background-color: {{ $cor1 }}; color: white;">Ação / Animação!</button>
                </div>

                <div class="grid-info">
                    <div class="info-box" style="grid-column: span 2;">
                        <small>Tipos</small>
                        <div style="display: flex; justify-content: center; gap: 10px; margin-top: 5px;">
                            @foreach($pokemon['tipo'] as $tipo)
                                <div style="background-color: {{ $coresTipos[$tipo] }}; padding: 5px 12px; border-radius: 20px; color: white; display: flex; align-items: center; gap: 5px; font-size: 0.85em; text-transform: uppercase; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                    <img src="https://raw.githubusercontent.com/duiker101/pokemon-type-svg-icons/master/icons/{{ $tipo }}.svg" style="width: 14px; height: 14px; margin: 0; transition: none; filter: none;">
                                    {{ $tipo }}
                                </div>
                            @endforeach
                        </div>
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

                <div class="pokedex-entry" style="margin-bottom: 20px;">
                    <h3>Descrição Pokedex</h3>
                    <p>{{ $pokemon['descricao'] }}</p>
                </div>
                
                <div>
                    <h3 style="color: #333; font-size: 1.1em; margin-bottom: 15px; border-bottom: 2px solid #eee; padding-bottom: 5px;">Status Base</h3>
                    @foreach($pokemon['status'] as $nome => $valor)
                        <div style="display: flex; align-items: center; margin-bottom: 8px; font-size: 0.9em;">
                            <span style="width: 80px; font-weight: bold; color: #666;">{{ $nome }}</span>
                            <span style="width: 35px; text-align: right; margin-right: 15px; font-weight: bold; color: #333;">{{ $valor }}</span>
                            <div style="flex: 1; background: #e0e0e0; border-radius: 10px; height: 12px; overflow: hidden;">
                                <div style="width: {{ min(100, ($valor / 255) * 100) }}%; height: 100%; background-color: {{ $cor1 }}; border-radius: 10px;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>

    <script>
        document.getElementById('btn-ataque').addEventListener('click', () => {
            const img = document.getElementById('poke-img');
            img.classList.add('ataque-animacao');
            setTimeout(() => img.classList.remove('ataque-animacao'), 500);
        });
    </script>
</body>
</html>