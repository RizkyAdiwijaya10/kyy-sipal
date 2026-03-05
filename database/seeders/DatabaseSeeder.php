<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        \App\Models\User::factory()->admin()->create();
        
        // Jika ingin membuat user biasa juga
        // \App\Models\User::factory()->count(5)->create();
    }
}
