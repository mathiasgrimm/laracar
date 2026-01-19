<?php

namespace Database\Seeders;

use App\Models\CarModel;
use App\Models\Make;
use App\Models\ModelVersion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class FipeSeeder extends Seeder
{
    private const BASE_URL = 'https://parallelum.com.br/fipe/api/v1';

    private const CACHE_DIR = 'database/data/fipe';

    /**
     * Nomes de modelos compostos (duas ou mais palavras).
     * Ordenados do mais longo para o mais curto para matching correto.
     */
    private const COMPOUND_MODEL_NAMES = [
        // Land Rover
        'Range Rover Evoque',
        'Range Rover Sport',
        'Range Rover Velar',
        'Range Rover',
        'Land Cruiser Prado',
        'Land Cruiser',
        'Discovery Sport',
        // Jeep
        'Grand Cherokee',
        'Grand Commander',
        'Wrangler Unlimited',
        // Toyota
        'Hilux SW4',
        'Corolla Cross',
        'RAV 4',
        'RAV4',
        // Volkswagen
        'Novo Fusca',
        'Space Fox',
        'Space Cross',
        'Cross Fox',
        'Saveiro Cross',
        'Gol Rallye',
        'Golf GTI',
        'Golf GTD',
        'Polo GTI',
        // Fiat
        'Punto T-Jet',
        'Strada Adventure',
        'Strada Trekking',
        'Strada Working',
        'Palio Weekend',
        'Palio Adventure',
        'Uno Mille',
        'Grand Siena',
        // Ford
        'Focus Sedan',
        'Focus Hatch',
        'New Fiesta',
        'Maverick Hybrid',
        // Chevrolet
        'S10 Blazer',
        'Spin Activ',
        'Cruze Sport6',
        // Renault
        'Grand Scenic',
        'Megane Scenic',
        // Honda
        'Accord Sedan',
        'Accord Coupe',
        'City Sedan',
        'City Hatchback',
        'Civic Sedan',
        'Civic Coupe',
        'Civic Hatch',
        'Civic Si',
        'CR-V',
        'HR-V',
        'WR-V',
        'ZR-V',
        'BR-V',
        // Hyundai
        'Santa Fe',
        'Vera Cruz',
        'New Tucson',
        // Mitsubishi
        'Pajero Sport',
        'Pajero Full',
        'Pajero Dakar',
        'Pajero TR4',
        'L200 Triton',
        'L200 Outdoor',
        'Lancer Evolution',
        // Nissan
        'Grand Livina',
        'X-Trail',
        'X-Terra',
        // Mercedes
        'Classe A',
        'Classe B',
        'Classe C',
        'Classe E',
        'Classe S',
        'Classe G',
        'Classe GLA',
        'Classe GLB',
        'Classe GLC',
        'Classe GLE',
        'Classe GLS',
        'Classe CLA',
        'Classe CLS',
        // BMW
        'Série 1',
        'Série 2',
        'Série 3',
        'Série 4',
        'Série 5',
        'Série 6',
        'Série 7',
        'Série 8',
        'Serie 1',
        'Serie 2',
        'Serie 3',
        'Serie 4',
        'Serie 5',
        'Serie 6',
        'Serie 7',
        'Serie 8',
        'X1',
        'X2',
        'X3',
        'X4',
        'X5',
        'X6',
        'X7',
        // Audi
        'RS Q3',
        'RS Q8',
        'RS 3',
        'RS 4',
        'RS 5',
        'RS 6',
        'RS 7',
        'SQ 5',
        'SQ 7',
        'SQ 8',
        'TT RS',
        // Porsche
        'Cayenne Coupe',
        'Macan S',
        '911 Carrera',
        '911 Turbo',
        '911 GT3',
        '911 GT2',
        // Kia
        'Soul EV',
        'Cerato Koup',
        // Outros
        'Alfa Romeo',
        'Aston Martin',
        'New Beetle',
    ];

    public function run(): void
    {
        $this->ensureCacheDirectoryExists();

        $this->command->info('Importando marcas da FIPE...');

        $makes = $this->getMakes();
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
        $this->command->info(sprintf(
            'Total: %d marcas, %d modelos, %d versões.',
            Make::count(),
            CarModel::count(),
            ModelVersion::count()
        ));
    }

    private function ensureCacheDirectoryExists(): void
    {
        $path = base_path(self::CACHE_DIR);

        if (! File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
        }
    }

    private function getCachePath(string $filename): string
    {
        return base_path(self::CACHE_DIR.'/'.$filename);
    }

    /**
     * @return array<int, array{codigo: string, nome: string}>
     */
    private function getMakes(): array
    {
        $cachePath = $this->getCachePath('makes.json');

        if (File::exists($cachePath)) {
            $this->command->info('Usando dados de marcas do cache local.');

            return json_decode(File::get($cachePath), true);
        }

        $this->command->info('Buscando marcas da API FIPE...');
        $data = $this->fetchMakes();

        if (! empty($data)) {
            File::put($cachePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $this->command->info('Dados de marcas salvos em cache.');
        }

        return $data;
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

    /**
     * @return array<int, array{codigo: int, nome: string}>
     */
    private function getModelsForMake(string $fipeCode): array
    {
        $cachePath = $this->getCachePath("models_{$fipeCode}.json");

        if (File::exists($cachePath)) {
            return json_decode(File::get($cachePath), true);
        }

        $data = $this->fetchModelsForMake($fipeCode);

        if (! empty($data)) {
            File::put($cachePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        return $data;
    }

    /**
     * @return array<int, array{codigo: int, nome: string}>
     */
    private function fetchModelsForMake(string $fipeCode): array
    {
        $response = Http::timeout(30)->get(self::BASE_URL."/carros/marcas/{$fipeCode}/modelos");

        if (! $response->successful()) {
            return [];
        }

        $data = $response->json();

        return $data['modelos'] ?? [];
    }

    private function importModelsForMake(Make $make, string $fipeCode): void
    {
        $models = $this->getModelsForMake($fipeCode);

        if (empty($models)) {
            $this->command->warn("Erro ao buscar modelos para marca: {$make->name}");

            return;
        }

        foreach ($models as $modelData) {
            $fullName = $modelData['nome'];
            [$modelName, $versionName] = $this->extractModelAndVersion($fullName);

            $carModel = CarModel::firstOrCreate(
                [
                    'make_id' => $make->id,
                    'slug' => Str::slug($modelName),
                ],
                [
                    'name' => $modelName,
                ]
            );

            ModelVersion::updateOrCreate(
                [
                    'car_model_id' => $carModel->id,
                    'fipe_code' => $modelData['codigo'],
                ],
                [
                    'name' => $versionName ?: $modelName,
                    'slug' => Str::slug($fullName),
                ]
            );
        }
    }

    /**
     * Extrai o nome do modelo base e a versão do nome completo da FIPE.
     *
     * @return array{0: string, 1: string} [modelName, versionName]
     */
    private function extractModelAndVersion(string $fullName): array
    {
        $fullName = trim($fullName);

        foreach (self::COMPOUND_MODEL_NAMES as $compound) {
            if (stripos($fullName, $compound) === 0) {
                $versionName = trim(substr($fullName, strlen($compound)));

                return [$compound, $versionName];
            }
        }

        $parts = preg_split('/\s+/', $fullName, 2);
        $modelName = $parts[0] ?? $fullName;
        $versionName = $parts[1] ?? '';

        return [$modelName, $versionName];
    }
}
