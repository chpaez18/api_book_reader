<?php

namespace Database\Seeders;

use App\Imports\QuotesImport;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class QuotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Excel::import(new QuotesImport, storage_path('/datos_citas.xlsx'));
    }
}
