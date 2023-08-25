<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::inRandomOrder()->take(10)->get();
        $users->each(function ($user) {
            Order::factory(50)->create([
                'user_id' => $user->id
            ]);
        });
    }
}
