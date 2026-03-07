<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class GerarPokedex extends Command
{
    protected $signature = 'pokedex:gerar';
    protected $description = 'Busca os 151 Pokémon na PokeAPI e gera o arquivo config/pokemon.php';

    public function handle()
    {
        $this->info('Iniciando o download de 1025 Pokémon. Vá pegar um café, isso vai levar uns 10 a 15 minutos...');
        $pokemons = [];

        for ($i = 1; $i <= 1025; $i++) {
            $response = Http::withoutVerifying()->get("https://pokeapi.co/api/v2/pokemon/{$i}");
            $species = Http::withoutVerifying()->get("https://pokeapi.co/api/v2/pokemon-species/{$i}");

            if ($response->successful() && $species->successful()) {
                $data = $response->json();
                $speciesData = $species->json();

                $descricao = collect($speciesData['flavor_text_entries'])
                    ->firstWhere('language.name', 'en')['flavor_text'] ?? 'Sem descrição.';
                $descricao = preg_replace('/[\r\n\f]+/', ' ', $descricao);

                $tipos = collect($data['types'])->map(fn($t) => ucfirst($t['type']['name']))->implode(' / ');

                // Identifica a geração com base no ID
                $geracao = match (true) {
                    $i <= 151 => 1,
                    $i <= 251 => 2,
                    $i <= 386 => 3,
                    $i <= 493 => 4,
                    $i <= 649 => 5,
                    $i <= 721 => 6,
                    $i <= 809 => 7,
                    $i <= 905 => 8,
                    default => 9,
                };

                $pokemons[$i] = [
                    'id' => $i,
                    'nome' => ucfirst($data['name']),
                    'geracao' => $geracao, // <--- Nova informação adicionada
                    'hp_max' => $data['stats'][0]['base_stat'],
                    'ataque_base' => $data['stats'][1]['base_stat'],
                    'defesa_base' => $data['stats'][2]['base_stat'],
                    'tipo' => $tipos,
                    'peso' => $data['weight'] / 10,
                    'altura' => $data['height'] / 10,
                    'descricao' => trim($descricao),
                    'habilidades' => [
                        ['nome' => 'Ataque Básico', 'dano' => rand(30, 60), 'texto' => '{pokemon} usou um ataque básico!']
                    ]
                ];
                $this->line("Coletado: {$i} - " . ucfirst($data['name']) . " (Gen {$geracao})");
            }
        }

        $conteudoArquivo = "<?php\n\nreturn " . var_export($pokemons, true) . ";\n";
        \Illuminate\Support\Facades\File::put(config_path('pokemon.php'), $conteudoArquivo);

        $this->info('Sucesso! Arquivo gerado com 1025 Pokémon!');
    }
}
