<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class GerarPokedex extends Command
{
    protected $signature = 'pokedex:gerar';
    protected $description = 'Busca os 1025 Pokémon na PokeAPI e gera o arquivo config/pokemon.php com Megas, Gmax e Regionais';

    public function handle()
    {
        $this->info('Iniciando o download de 1025 Pokémon com todas as formas alternativas. Isso pode levar mais de 20 minutos. Relaxe e pegue um café!');
        $pokemons = [];

        for ($i = 1; $i <= 1025; $i++) {
            $response = Http::withoutVerifying()->get("https://pokeapi.co/api/v2/pokemon/{$i}");
            $species = Http::withoutVerifying()->get("https://pokeapi.co/api/v2/pokemon-species/{$i}");

            if (!$response->successful() || !$species->successful()) {
                $this->error("Erro ao buscar o Pokémon {$i}. Pulando...");
                continue;
            }

            $data = $response->json();
            $speciesData = $species->json();

            // Pega a descrição em inglês
            $descricao = collect($speciesData['flavor_text_entries'])
                ->firstWhere('language.name', 'en')['flavor_text'] ?? 'Sem descrição.';
            $descricao = preg_replace('/[\r\n\f]+/', ' ', $descricao);

            // Pega os tipos
            $tipos = collect($data['types'])->map(fn($t) => strtolower($t['type']['name']))->toArray();

            $geracao = match (true) {
                $i <= 151 => 1, $i <= 251 => 2, $i <= 386 => 3, $i <= 493 => 4,
                $i <= 649 => 5, $i <= 721 => 6, $i <= 809 => 7, $i <= 905 => 8,
                default => 9,
            };

            // ---- INÍCIO DO CÓDIGO DAS FORMAS ALTERNATIVAS ---- //
            $formasAlternativas = [];
            
            // A API guarda as formas (Megas, Alola, etc) na chave 'varieties'
            foreach ($speciesData['varieties'] as $variety) {
                // Pula a forma padrão ('is_default' = true), pois já temos ela
                if ($variety['is_default']) continue;

                $nomeVariedade = $variety['pokemon']['name'];
                $urlVariedade = $variety['pokemon']['url'];

                // Filtramos apenas as formas que nos interessam usando regex ou string match
                $tipoForma = null;
                if (str_contains($nomeVariedade, '-mega')) $tipoForma = 'Mega';
                elseif (str_contains($nomeVariedade, '-gmax')) $tipoForma = 'Gigantamax';
                elseif (str_contains($nomeVariedade, '-alola')) $tipoForma = 'Alola';
                elseif (str_contains($nomeVariedade, '-galar')) $tipoForma = 'Galar';
                elseif (str_contains($nomeVariedade, '-hisui')) $tipoForma = 'Hisui';
                elseif (str_contains($nomeVariedade, '-paldea')) $tipoForma = 'Paldea';

                if ($tipoForma) {
                    $this->line("   -> Buscando Forma Alternativa: {$nomeVariedade}");
                    $responseForma = Http::withoutVerifying()->get($urlVariedade);
                    
                    if ($responseForma->successful()) {
                        $dataForma = $responseForma->json();
                        $tiposForma = collect($dataForma['types'])->map(fn($t) => strtolower($t['type']['name']))->toArray();
                        
                        $formasAlternativas[] = [
                            'tipo_forma' => $tipoForma,
                            'nome' => ucfirst(str_replace('-', ' ', $nomeVariedade)), // Ex: Charizard mega x
                            'tipos' => $tiposForma,
                            'status' => [
                                'HP' => $dataForma['stats'][0]['base_stat'],
                                'Ataque' => $dataForma['stats'][1]['base_stat'],
                                'Defesa' => $dataForma['stats'][2]['base_stat'],
                                'Sp. Atk' => $dataForma['stats'][3]['base_stat'],
                                'Sp. Def' => $dataForma['stats'][4]['base_stat'],
                                'Velocidade' => $dataForma['stats'][5]['base_stat'],
                            ],
                            // Pega a imagem oficial e a versão shiny oficial
                            'img_oficial' => $dataForma['sprites']['other']['official-artwork']['front_default'],
                            'img_shiny' => $dataForma['sprites']['other']['official-artwork']['front_shiny'] ?? null,
                            'peso' => $dataForma['weight'] / 10,
                            'altura' => $dataForma['height'] / 10,
                        ];
                    }
                }
            }
            // ---- FIM DO CÓDIGO DAS FORMAS ALTERNATIVAS ---- //

            $pokemons[$i] = [
                'id' => $i,
                'nome' => ucfirst($data['name']),
                'geracao' => $geracao,
                'tipo' => $tipos,
                'status' => [
                    'HP' => $data['stats'][0]['base_stat'],
                    'Ataque' => $data['stats'][1]['base_stat'],
                    'Defesa' => $data['stats'][2]['base_stat'],
                    'Sp. Atk' => $data['stats'][3]['base_stat'],
                    'Sp. Def' => $data['stats'][4]['base_stat'],
                    'Velocidade' => $data['stats'][5]['base_stat'],
                ],
                'peso' => $data['weight'] / 10,
                'altura' => $data['height'] / 10,
                'descricao' => trim($descricao),
                
                // NOVO: Adicionando links diretos para os sprites
                'img_oficial' => "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/{$i}.png",
                'img_shiny' => $data['sprites']['other']['official-artwork']['front_shiny'] ?? null, // Pega o Shiny do JSON
                
                // Anexa o array de formas (pode estar vazio ou cheio)
                'formas_alternativas' => $formasAlternativas,
            ];
            
            $this->info("Coletado: {$i} - " . ucfirst($data['name']));
        }

        $conteudoArquivo = "<?php\n\nreturn " . var_export($pokemons, true) . ";\n";
        File::put(config_path('pokemon.php'), $conteudoArquivo);

        $this->info('Sucesso! Arquivo gerado com 1025 Pokémon e suas formas alternativas!');
    }
}