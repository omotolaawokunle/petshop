<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OrderStatus::upsert([
            ['id' => 1, 'title' => 'open', 'uuid' => Str::orderedUuid()],
            ['id' => 2, 'title' => 'pending payment', 'uuid' => Str::orderedUuid()],
            ['id' => 3, 'title' => 'paid', 'uuid' => Str::orderedUuid()],
            ['id' => 4, 'title' => 'shipped', 'uuid' => Str::orderedUuid()],
            ['id' => 5, 'title' => 'cancelled', 'uuid' => Str::orderedUuid()],
        ], ['id', 'title'], ['uuid']);
    }
}
