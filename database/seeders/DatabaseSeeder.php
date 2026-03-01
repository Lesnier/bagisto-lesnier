<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\Installer\Database\Seeders\DatabaseSeeder as BagistoDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Orden: 1) Data base de Bagisto (canales, locales, admin, etc.)
     *         2) Data de marketplace (categorías, productos, vendors, clientes)
     */
    public function run()
    {
        $this->call(BagistoDatabaseSeeder::class);
        $this->call(VendorMarketplaceSeeder::class);
    }
}

