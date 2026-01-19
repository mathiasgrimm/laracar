<?php

namespace Database\Seeders;

use App\Models\CarModel;
use App\Models\Make;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class FipeSeeder extends Seeder
{
    private const BASE_URL = 'https://parallelum.com.br/fipe/api/v1';

    public function run(): void
    {
        $this->command->info('Importando marcas da FIPE...');

        $makes = $this->fetchMakes();
        $this->command->info(sprintf('Encontradas %d marcas.', count($makes)));

        $progressBar = $this->command->getOutput()->createProgressBar(count($makes));
        $progressBar->start();

        foreach ($makes as $makeData) {
            $make = Make::updateOrCreate(
                ['fipe_code' => $makeData['codigo']],
                [
                    'name' => $makeData['nome'],
                    'slug' => Str::slug($makeData['nome']),
                ]
            );

            $this->importModelsForMake($make, $makeData['codigo']);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->command->newLine();
        $this->command->info('Importação concluída!');
        $this->command->info(sprintf('Total: %d marcas, %d modelos.', Make::count(), CarModel::count()));
    }

    /**
     * @return array<int, array{codigo: string, nome: string}>
     */
    private function fetchMakes(): array
    {
        $response = Http::timeout(30)->get(self::BASE_URL.'/carros/marcas');

        if (! $response->successful()) {
            $this->command->error('Erro ao buscar marcas da API FIPE.');

            return [];
        }

        return $response->json();
    }

    private function importModelsForMake(Make $make, string $fipeCode): void
    {
        $response = Http::timeout(30)->get(self::BASE_URL."/carros/marcas/{$fipeCode}/modelos");

        if (! $response->successful()) {
            $this->command->warn("Erro ao buscar modelos para marca: {$make->name}");

            return;
        }

        $data = $response->json();
        $models = $data['modelos'] ?? [];

        foreach ($models as $modelData) {
            CarModel::updateOrCreate(
                [
                    'make_id' => $make->id,
                    'fipe_code' => $modelData['codigo'],
                ],
                [
                    'name' => $modelData['nome'],
                    'slug' => Str::slug($modelData['nome']),
                ]
            );
        }
    }
}
