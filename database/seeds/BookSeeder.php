<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Book::create([
            'name' => '100 citas para cultivar el Amor Propio',
            'autor' => 'Dairew',
            'description' => 'Es un book de registros fotográficos donde podrás vivir experiencias únicas, dinámicas y retadoras que no solo lograrán divertirte sino que además te ayudarán en el fortalecimiento de tu amor propio.',
            'number_pages' => 100,
            'number_quotes' => 100,
            'status' => 1,
        ]);
    }
}
