<?php

namespace App\Models\Imports;

use App\Http\Constants;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class InvoicesImport implements ToModel, WithValidation, SkipsOnFailure, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    use Importable, SkipsFailures;
    private $importedRecordsCount = 0;

    /**
     * @param array $row
     *
     * @return Invoice
     * @throws Exception
     */
    public function model(array $row)
    {
        ++$this->importedRecordsCount;
        $client = Client::where('cellagon_id', "{$row['kd_nr']}")->get()->first();
        $product = '';
        if (isset($row['artikelnr']))
        {
            $product = Product::where('article_number', 'like', "%{$row['artikelnr']}%")->get()->first();
        }
        //The tax value that must be in the Excel to import must always be the percentage and not the total amount of the tax caused so that the final amount of the invoice is correct
        $tax = (isset($row['steuer']) && is_double($row['steuer'])) ? doubleval($row['steuer']) : Constants::TAX;
        $amount = isset($row['gesamt']) ? doubleval($row['gesamt']) : 0.00;
        $dateInvoice = isset($row['datum']) ? Carbon::instance(Date::excelToDateTimeObject($row['datum'])) : null;

        return new Invoice([
            'user_id' => Auth::id(),
            'client_id' => $client ? $client->id : null,
            'product_id' => $product ? $product->id : null,
            'internal_nr' => $client ? $client->cellagon_id : null,
            'invoice_nr' => $row['belegnr'] ?? null,
            'date' => $dateInvoice,
            'amount' => $amount - ($amount * $tax),
            'discount' => doubleval($row['abschlag']) ?? 0.00,
            'tax' => $tax * $amount,
            'tax_percentage' => $tax,
            'quantity' => 0,
            'batch_id' => session('batch_id')
        ]);
    }

    public function rules(): array
    {
        return [
            'belegnr' => [Rule::unique('invoices', 'invoice_nr'), 'required'],
            'kd_nr' => 'required',
            'gesamt' => 'required',
            'datum' => 'required'
        ];
    }

    public function headingRow(): int
    {
        return 2;
    }

    public function chunkSize(): int
    {
        return 200;
    }

    public function batchSize(): int
    {
        return 200;
    }

    public function getImportedRecordsCount()
    {
        return $this->importedRecordsCount;
    }
}
