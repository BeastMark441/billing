<?php

namespace Database\Seeders;

use App\Models\InfrastructureCategory;
use App\Models\InfrastructureService;
use App\Models\InfrastructureSubcategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InfrastructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Игровые серверы',
                'description' => 'Высокопроизводительные серверы для ваших любимых игр с минимальной задержкой.',
                'subcategories' => [
                    [
                        'name' => 'Minecraft',
                        'description' => 'Хостинг для Minecraft с поддержкой любых плагинов и модов.',
                        'services' => [
                            ['name' => 'Minecraft Start', 'price' => 250, 'specs' => ['RAM' => '2GB', 'memory' => 2048, 'CPU' => '2 Core', 'cpu' => 200, 'Disk' => '10GB NVMe', 'disk' => 10240, 'egg_id' => 1]],
                            ['name' => 'Minecraft Pro', 'price' => 500, 'specs' => ['RAM' => '4GB', 'memory' => 4096, 'CPU' => '4 Core', 'cpu' => 400, 'Disk' => '20GB NVMe', 'disk' => 20480, 'egg_id' => 1]],
                            ['name' => 'Minecraft Elite', 'price' => 1000, 'specs' => ['RAM' => '8GB', 'memory' => 8192, 'CPU' => '6 Core', 'cpu' => 600, 'Disk' => '40GB NVMe', 'disk' => 40960, 'egg_id' => 1]],
                            ['name' => 'Minecraft Ultra', 'price' => 1800, 'specs' => ['RAM' => '16GB', 'memory' => 16384, 'CPU' => '8 Core', 'cpu' => 800, 'Disk' => '80GB NVMe', 'disk' => 81920, 'egg_id' => 1]],
                            ['name' => 'Minecraft Ultimate', 'price' => 3500, 'specs' => ['RAM' => '32GB', 'memory' => 32768, 'CPU' => '12 Core', 'cpu' => 1200, 'Disk' => '160GB NVMe', 'disk' => 163840, 'egg_id' => 1]],
                        ],
                    ],
                    [
                        'name' => 'Rust',
                        'description' => 'Стабильные серверы для Rust с защитой от DDoS.',
                        'services' => [
                            ['name' => 'Rust Basic', 'price' => 600, 'specs' => ['RAM' => '8GB', 'memory' => 8192, 'CPU' => '4 Core', 'cpu' => 400, 'Disk' => '30GB NVMe', 'disk' => 30720, 'egg_id' => 2]],
                            ['name' => 'Rust Standard', 'price' => 1200, 'specs' => ['RAM' => '16GB', 'memory' => 16384, 'CPU' => '6 Core', 'cpu' => 600, 'Disk' => '60GB NVMe', 'disk' => 61440, 'egg_id' => 2]],
                            ['name' => 'Rust Advanced', 'price' => 2400, 'specs' => ['RAM' => '32GB', 'memory' => 32768, 'CPU' => '8 Core', 'cpu' => 800, 'Disk' => '120GB NVMe', 'disk' => 122880, 'egg_id' => 2]],
                            ['name' => 'Rust Extreme', 'price' => 4500, 'specs' => ['RAM' => '64GB', 'memory' => 65536, 'CPU' => '12 Core', 'cpu' => 1200, 'Disk' => '240GB NVMe', 'disk' => 245760, 'egg_id' => 2]],
                            ['name' => 'Rust Godlike', 'price' => 8000, 'specs' => ['RAM' => '128GB', 'memory' => 131072, 'CPU' => '16 Core', 'cpu' => 1600, 'Disk' => '500GB NVMe', 'disk' => 512000, 'egg_id' => 2]],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Виртуальные серверы',
                'description' => 'Масштабируемые VPS/VDS для любых задач.',
                'subcategories' => [
                    [
                        'name' => 'Linux VPS',
                        'description' => 'Быстрые Linux серверы на базе Ubuntu, Debian, CentOS.',
                        'services' => [
                            ['name' => 'Linux XS', 'price' => 300, 'specs' => ['RAM' => '1GB', 'memory' => 1024, 'CPU' => '1 Core', 'cpu' => 100, 'Disk' => '20GB SSD', 'disk' => 20480]],
                            ['name' => 'Linux S', 'price' => 600, 'specs' => ['RAM' => '2GB', 'memory' => 2048, 'CPU' => '2 Core', 'cpu' => 200, 'Disk' => '40GB SSD', 'disk' => 40960]],
                            ['name' => 'Linux M', 'price' => 1200, 'specs' => ['RAM' => '4GB', 'memory' => 4096, 'CPU' => '4 Core', 'cpu' => 400, 'Disk' => '80GB SSD', 'disk' => 81920]],
                            ['name' => 'Linux L', 'price' => 2400, 'specs' => ['RAM' => '8GB', 'memory' => 8192, 'CPU' => '6 Core', 'cpu' => 600, 'Disk' => '160GB SSD', 'disk' => 163840]],
                            ['name' => 'Linux XL', 'price' => 4800, 'specs' => ['RAM' => '16GB', 'memory' => 16384, 'CPU' => '8 Core', 'cpu' => 800, 'Disk' => '320GB SSD', 'disk' => 327680]],
                        ],
                    ],
                    [
                        'name' => 'Windows VPS',
                        'description' => 'Удаленные рабочие столы на Windows Server.',
                        'services' => [
                            ['name' => 'Windows Start', 'price' => 800, 'specs' => ['RAM' => '4GB', 'memory' => 4096, 'CPU' => '2 Core', 'cpu' => 200, 'Disk' => '50GB SSD', 'disk' => 51200]],
                            ['name' => 'Windows Office', 'price' => 1500, 'specs' => ['RAM' => '8GB', 'memory' => 8192, 'CPU' => '4 Core', 'cpu' => 400, 'Disk' => '100GB SSD', 'disk' => 102400]],
                            ['name' => 'Windows Power', 'price' => 3000, 'specs' => ['RAM' => '16GB', 'memory' => 16384, 'CPU' => '6 Core', 'cpu' => 600, 'Disk' => '200GB SSD', 'disk' => 204800]],
                            ['name' => 'Windows Enterprise', 'price' => 5500, 'specs' => ['RAM' => '32GB', 'memory' => 32768, 'CPU' => '8 Core', 'cpu' => 800, 'Disk' => '400GB SSD', 'disk' => 409600]],
                            ['name' => 'Windows Max', 'price' => 10000, 'specs' => ['RAM' => '64GB', 'memory' => 65536, 'CPU' => '12 Core', 'cpu' => 1200, 'Disk' => '800GB SSD', 'disk' => 819200]],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Выделенные серверы',
                'description' => 'Аренда физического оборудования для максимальной мощности.',
                'subcategories' => [
                    [
                        'name' => 'Intel-based',
                        'description' => 'Серверы на базе процессоров Intel Xeon.',
                        'services' => [
                            ['name' => 'Intel Xeon E-2224', 'price' => 5000, 'specs' => ['CPU' => '4c/4t', 'RAM' => '16GB', 'Disk' => '2x480GB SSD']],
                            ['name' => 'Intel Xeon E-2276G', 'price' => 8500, 'specs' => ['CPU' => '6c/12t', 'RAM' => '32GB', 'Disk' => '2x960GB SSD']],
                            ['name' => 'Intel Xeon Silver 4210', 'price' => 15000, 'specs' => ['CPU' => '10c/20t', 'RAM' => '64GB', 'Disk' => '2x1.92TB SSD']],
                            ['name' => 'Intel Xeon Gold 6230', 'price' => 25000, 'specs' => ['CPU' => '20c/40t', 'RAM' => '128GB', 'Disk' => '4x1.92TB SSD']],
                            ['name' => 'Intel Dual Xeon Gold', 'price' => 45000, 'specs' => ['CPU' => '40c/80t', 'RAM' => '256GB', 'Disk' => '8x1.92TB SSD']],
                        ],
                    ],
                    [
                        'name' => 'AMD-based',
                        'description' => 'Серверы на базе процессоров AMD Ryzen и EPYC.',
                        'services' => [
                            ['name' => 'Ryzen 5 3600', 'price' => 4500, 'specs' => ['CPU' => '6c/12t', 'RAM' => '32GB', 'Disk' => '2x512GB NVMe']],
                            ['name' => 'Ryzen 9 5950X', 'price' => 9500, 'specs' => ['CPU' => '16c/32t', 'RAM' => '64GB', 'Disk' => '2x1TB NVMe']],
                            ['name' => 'EPYC 7302P', 'price' => 14000, 'specs' => ['CPU' => '16c/32t', 'RAM' => '128GB', 'Disk' => '2x2TB NVMe']],
                            ['name' => 'EPYC 7542', 'price' => 28000, 'specs' => ['CPU' => '32c/64t', 'RAM' => '256GB', 'Disk' => '4x2TB NVMe']],
                            ['name' => 'Dual EPYC 7742', 'price' => 60000, 'specs' => ['CPU' => '128c/256t', 'RAM' => '512GB', 'Disk' => '8x2TB NVMe']],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Веб-хостинг',
                'description' => 'Простое решение для размещения ваших сайтов.',
                'subcategories' => [
                    [
                        'name' => 'Shared Hosting',
                        'description' => 'Общий хостинг для небольших сайтов и визиток.',
                        'services' => [
                            ['name' => 'Hosting Mini', 'price' => 100, 'specs' => ['Sites' => '1', 'Disk' => '2GB', 'DB' => '1']],
                            ['name' => 'Hosting Plus', 'price' => 250, 'specs' => ['Sites' => '5', 'Disk' => '10GB', 'DB' => '5']],
                            ['name' => 'Hosting Business', 'price' => 500, 'specs' => ['Sites' => '20', 'Disk' => '50GB', 'DB' => '20']],
                            ['name' => 'Hosting Premium', 'price' => 900, 'specs' => ['Sites' => '50', 'Disk' => '100GB', 'DB' => '50']],
                            ['name' => 'Hosting Unlimited', 'price' => 1500, 'specs' => ['Sites' => '∞', 'Disk' => 'Unlimited', 'DB' => '∞']],
                        ],
                    ],
                    [
                        'name' => 'Managed WordPress',
                        'description' => 'Оптимизированный хостинг для сайтов на WordPress.',
                        'services' => [
                            ['name' => 'WP Start', 'price' => 350, 'specs' => ['Sites' => '1', 'Disk' => '5GB', 'Backup' => 'Daily']],
                            ['name' => 'WP Grow', 'price' => 700, 'specs' => ['Sites' => '3', 'Disk' => '15GB', 'Backup' => 'Daily']],
                            ['name' => 'WP Professional', 'price' => 1400, 'specs' => ['Sites' => '10', 'Disk' => '40GB', 'Backup' => 'Hourly']],
                            ['name' => 'WP Agency', 'price' => 2800, 'specs' => ['Sites' => '30', 'Disk' => '100GB', 'Backup' => 'Hourly']],
                            ['name' => 'WP Enterprise', 'price' => 5000, 'specs' => ['Sites' => '100', 'Disk' => '250GB', 'Backup' => 'Real-time']],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Облачные базы данных',
                'description' => 'Управляемые базы данных с высокой доступностью.',
                'subcategories' => [
                    [
                        'name' => 'PostgreSQL',
                        'description' => 'Надежная реляционная база данных PostgreSQL.',
                        'services' => [
                            ['name' => 'PG Nano', 'price' => 400, 'specs' => ['RAM' => '512MB', 'CPU' => '0.5 Core', 'Disk' => '5GB']],
                            ['name' => 'PG Micro', 'price' => 800, 'specs' => ['RAM' => '1GB', 'CPU' => '1 Core', 'Disk' => '10GB']],
                            ['name' => 'PG Small', 'price' => 1600, 'specs' => ['RAM' => '2GB', 'CPU' => '2 Core', 'Disk' => '20GB']],
                            ['name' => 'PG Medium', 'price' => 3200, 'specs' => ['RAM' => '4GB', 'CPU' => '4 Core', 'Disk' => '40GB']],
                            ['name' => 'PG Large', 'price' => 6400, 'specs' => ['RAM' => '8GB', 'CPU' => '8 Core', 'Disk' => '80GB']],
                        ],
                    ],
                    [
                        'name' => 'MySQL',
                        'description' => 'Популярная база данных MySQL в облаке.',
                        'services' => [
                            ['name' => 'MySQL Start', 'price' => 350, 'specs' => ['RAM' => '512MB', 'memory' => 512, 'CPU' => '0.5 Core', 'cpu' => 50, 'Disk' => '5GB', 'disk' => 5120]],
                            ['name' => 'MySQL Base', 'price' => 700, 'specs' => ['RAM' => '1GB', 'memory' => 1024, 'CPU' => '1 Core', 'cpu' => 100, 'Disk' => '10GB', 'disk' => 10240]],
                            ['name' => 'MySQL Plus', 'price' => 1400, 'specs' => ['RAM' => '2GB', 'memory' => 2048, 'CPU' => '2 Core', 'cpu' => 200, 'Disk' => '20GB', 'disk' => 20480]],
                            ['name' => 'MySQL Pro', 'price' => 2800, 'specs' => ['RAM' => '4GB', 'memory' => 4096, 'CPU' => '4 Core', 'cpu' => 400, 'Disk' => '40GB', 'disk' => 40960]],
                            ['name' => 'MySQL Ultra', 'price' => 5600, 'specs' => ['RAM' => '8GB', 'memory' => 8192, 'CPU' => '8 Core', 'cpu' => 800, 'Disk' => '80GB', 'disk' => 81920]],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($data as $catData) {
            $category = InfrastructureCategory::create([
                'name' => $catData['name'],
                'slug' => Str::slug($catData['name']),
                'description' => $catData['description'],
                'is_active' => true,
            ]);

            foreach ($catData['subcategories'] as $subData) {
                $subcategory = InfrastructureSubcategory::create([
                    'infrastructure_category_id' => $category->id,
                    'name' => $subData['name'],
                    'slug' => Str::slug($category->name.'-'.$subData['name']),
                    'description' => $subData['description'],
                    'is_active' => true,
                ]);

                foreach ($subData['services'] as $svc) {
                    InfrastructureService::create([
                        'infrastructure_category_id' => $category->id,
                        'infrastructure_subcategory_id' => $subcategory->id,
                        'name' => $svc['name'],
                        'slug' => Str::slug($svc['name']),
                        'description' => 'Тарифный план '.$svc['name'].' в категории '.$subData['name'],
                        'price' => $svc['price'],
                        'specifications' => $svc['specs'],
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
}
