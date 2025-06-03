<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plan::insert([
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'stripe_id' => 'price_1RVUoLFxgtNXLvTsQWUICIj4',
                'product_id' => 'prod_SQLVlPrSjMr7eg',
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'stripe_id' => 'price_1RVka7FxgtNXLvTsduBiW9Ne',
                'product_id' => 'prod_SQbnnQ96ZHezoq',
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'stripe_id' => 'price_1RVkeMFxgtNXLvTsLjzefyNl',
                'product_id' => 'prod_SQbspofa0ZaKfS',
            ]
        ]);
    }
}
