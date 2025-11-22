<?php

namespace Database\Seeders;

use App\Models\Government_sector;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GovernmentSectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $government_sectors = [
            ['name' => 'الكهرباء'],
            ['name' => 'المياه'],
            ['name' => 'الاتصالات'],
            ['name' => 'وزارة الداخلية'],
            ['name' => 'النقل'],
            ['name' => 'الإدارة المحلية'],
            ['name' => 'خدمات حكومية'],
            ['name' => 'وزارة المالية'],
            ['name' => 'التعليم العالي'],
            ['name' => 'وزارة الإشغال العامة والإسكان'],
            ['name' => 'وزارة الإقتصاد والصناعة'],
        ];

        foreach ($government_sectors as $government_sector) {
            Government_sector::query()->create($government_sector);
        }
    }
}
