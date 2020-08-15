<?php namespace App\Http\Services;

use App\Models\Imports\ClientsImport;
use App\Models\Imports\InvoicesImport;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;

class ImportFromExcelService
{
    /**
     * @var Excel
     */
    private $excel;

    /**
     * ImportFromExcelService constructor.
     * @param Excel $excel
     */
    public function __construct(Excel $excel)
    {
        $this->excel = $excel;
    }

    public function importClients(UploadedFile $file)
    {
        $clients = new ClientsImport;
        $clients->import($file);

        return [
            'failures' => $clients->failures()->count() > 0 ? $clients->failures() : null,
            'totalImported' => $clients->getImportedRecordsCount()
        ];
    }

    public function importInvoices(UploadedFile $file)
    {
        $invoices = new InvoicesImport();
        $invoices->import($file);


        return [
                    'failures' => $invoices->failures()->count() > 0 ? $invoices->failures() : null,
                    'totalImported' => $invoices->getImportedRecordsCount()
                ];
    }
}
