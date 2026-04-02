<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class GerarCartas extends Command
{
    protected $signature = 'cartas:gerar';
    protected $description = 'Busca as cartas de Pokémon (TCG) da API e gera o arquivo config/cartas.php';

    public function handle()
    {
        $this->info('Iniciando o download das cartas do Pokémon TCG. Como são mais de 16.000 cartas, isso pode demorar bastante!');

        $cartas = [];
        $pagina = 1;
        $tamanhoPagina = 250; // O máximo que a API permite por vez
        $temMaisCartas = true;

        while ($temMaisCartas) {
            $this->info("Buscando página {$pagina}...");

            // Endpoint da API oficial do TCG
            $response = Http::withoutVerifying()
                ->timeout(60) // Dá mais tempo pro servidor não dar timeout
                ->get("https://api.pokemontcg.io/v2/cards", [
                    'page' => $pagina,
                    'pageSize' => $tamanhoPagina,
                    'select' => 'id,name,supertype,subtypes,rarity,images,tcgplayer,set' // Baixa só o que precisamos pra ficar leve
                ]);

            if (!$response->successful()) {
                $this->error("Erro ao buscar a página {$pagina}. Pode ser um bloqueio temporário (Rate Limit) da API.");
                break;
            }

            $dados = $response->json();
            $cartasRetornadas = $dados['data'] ?? [];

            // Se não vier nada, chegamos ao fim!
            if (empty($cartasRetornadas)) {
                $temMaisCartas = false;
                continue;
            }

            foreach ($cartasRetornadas as $carta) {
                // Lógica para extrair o preço (Tenta o normal, se não tiver, tenta Holo, etc)
                $precoNormal = $carta['tcgplayer']['prices']['normal']['market'] ?? null;
                $precoHolo = $carta['tcgplayer']['prices']['holofoil']['market'] ?? null;
                $precoReverse = $carta['tcgplayer']['prices']['reverseHolofoil']['market'] ?? null;
                
                $precoFinal = $precoNormal ?? $precoHolo ?? $precoReverse ?? 0.00;

                $cartas[$carta['id']] = [
                    'id' => $carta['id'],
                    'nome' => $carta['name'],
                    'supertype' => $carta['supertype'], // Pokémon, Trainer ou Energy
                    'subtypes' => $carta['subtypes'] ?? [], // Ex: Item, Supporter, Basic
                    'raridade' => $carta['rarity'] ?? 'Desconhecida',
                    'colecao' => $carta['set']['name'] ?? 'Desconhecida',
                    'preco_dolar' => $precoFinal,
                    'img' => $carta['images']['large'] ?? ($carta['images']['small'] ?? null),
                ];
            }

            $this->info("Página {$pagina} concluída. " . count($cartas) . " cartas coletadas até agora.");

            // A API bloqueia quem faz requisições muito rápido sem uma API Key.
            // Essa pausa de 2 segundos ajuda a não tomar "ban" temporário.
            sleep(2);
            $pagina++;

            // ---- DICA DE TESTE ---- //
            // Se quiser apenas testar para ver como fica na tela antes de baixar 16 mil cartas, 
            // mude o 3 para um número pequeno e descomente as duas linhas abaixo:
            /*
            if ($pagina > 3) {
                $this->warn("Parando cedo para testes...");
                break;
            }
            */
            // ----------------------- //
        }

        $this->info('Criando o banco de dados das cartas...');

        $conteudoArquivo = "<?php\n\nreturn " . var_export($cartas, true) . ";\n";
        File::put(config_path('cartas.php'), $conteudoArquivo);

        $this->info('Sucesso épico! Arquivo gerado com ' . count($cartas) . ' cartas prontas para o novo filtro!');
    }
}