<?php

namespace Database\Seeders\domains\vehicles\car_models;

use App\Models\CarManufacturer;
use App\Models\CarModel;
use App\Models\CarServiceLevel;
use DOMDocument;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CarModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $html = file_get_contents(database_path('seeders/data/Accepted cars YallaGo! - Google Drive.htm'));
        $dom = new DOMDocument();
        @$dom->loadHTML($html);

        $tables = $dom->getElementsByTagName('table');
        $waffleTable = null;

        foreach ($tables as $table) {
            if (str_contains($table->getAttribute('class'), 'waffle')) {
                $waffleTable = $table;
                break;
            }
        }

        if (!$waffleTable) return;

        $rows = $waffleTable->getElementsByTagName('tr');
        $currentManufacturer = null;
        $serviceLevels = CarServiceLevel::pluck('id', 'name')->all();

        // Process each row
        foreach ($rows as $row) {
            $cells = $row->getElementsByTagName('td');

            // Skip empty rows
            if ($cells->count() === 0) continue;

            // Manufacturer row (colspan=4)
            if ($cells->item(0)->getAttribute('colspan') == '4') {
                $brandName = trim($cells->item(0)->textContent);
                $currentManufacturer = CarManufacturer::where('name', $brandName)->first();
                continue;
            }

            // Regular model row
            if ($currentManufacturer && $cells->count() >= 27) {
                $modelName = trim($cells->item(1)->textContent);
                $years = $this->extractYears($cells, $modelName);

                foreach ($years as $year => $serviceLevelName) {
                    if ($serviceLevelId = $serviceLevels[$serviceLevelName] ?? null) {
                        CarModel::create([
                            'car_manufacturer_id' => $currentManufacturer->id,
                            'car_service_level_id' => $serviceLevelId,
                            'name' => $modelName,
                            'model_year' => $year,
                            'is_active' => true
                        ]);
                    }
                }
            }
        }
    }

    private function extractYears($cells, $modelName)
    {
        $years = [];
        $yearColumns = range(5, 26); // Columns containing years (2003-2025)
        $startYear = 2003;

        foreach ($yearColumns as $index => $col) {
            $year = $startYear + $index;
            $value = trim($cells->item($col)->textContent);

            if ($value && $value !== '#') {
                $years[$year] = $this->normalizeServiceLevel($value, $modelName);
            }
        }

        return $years;
    }

    private function normalizeServiceLevel($value)
    {
        $SUV = array(
            "Sorento",
            "Mohave",
            "Max Cruze",
            "Vera Cruze",
            "Santafe",
            "Tuscon",
            "CRV",
            "EQUINOX",
            "Captiva",
            "Sportage",
            "Q Family",
        );
        $SUV = array_map('strtolower', $SUV);
        $SUV = array_map('trim', $SUV);
        if (in_array(strtolower(trim($value)), $SUV)) {
            return "SUV";
        }
        return match (strtolower(trim($value))) {
            'v.i.p' => 'V.I.P',
            'comfort' => 'Comfort',
            default => 'Classic'
        };
    }
}
