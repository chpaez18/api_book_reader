<?php 
namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use App\Models\Quote;

class QuotesImport implements ToModel, WithCalculatedFormulas
{
    use Importable;

    public function model(array $row)
    {
        return new Quote([
            'number_quote' => $row[0],
            'first_title' => $row[1],
            'second_title' => $row[2],
            'message' => $row[3],
        ]);
    }
}
