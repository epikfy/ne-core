<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportRequest;
use App\Http\Services\ImportFromExcelService;
use App\Http\Status;
use App\Models\Client;
use App\Models\ImportedFile;
use App\Models\Invoice;
use DateTimeZone;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Validators\ValidationException;

class ImportController extends BaseController
{
    /**
     * @var ImportFromExcelService
     */
    private $importFromExcelService;

    /**
     * ImportController constructor.
     * @param ImportFromExcelService $importFromExcelService
     */
    public function __construct(ImportFromExcelService $importFromExcelService)
    {
        $this->importFromExcelService = $importFromExcelService;
    }

    /**
     * @param ImportRequest $request
     * @return JsonResponse
     */
    public function invoices(ImportRequest $request)
    {
        try {
            $import = ImportedFile::create([
                'user_id' => Auth::id(),
                'started_at' => date_create(null, new DateTimeZone(config('app.timezone'))),
                'filename' => $request->file('file')->getRealPath(),
            ]);
            $returnImport = $this->importFromExcelService->importInvoices($request->file);
            $import->update([
                'ended_at' => date_create(null, new DateTimeZone(config('app.timezone'))),
                'status' => Status::SUCCESS,
                'total_records' => $returnImport['totalImported']
            ]);
            $returnMessages = [
                            'totalImportedRecords' => $returnImport['totalImported'],
                            'message' => trans('messages.import.success'),
                            'error' => []
                        ];
                        $responseCode = Response::HTTP_OK;

                        if ($returnImport['failures'] !== null) {
                            $responseCode = Response::HTTP_BAD_REQUEST;
                            $returnMessages['message'] = trans('messages.import.failed');
                            $returnMessages['error'] = $returnImport['failures'];
                        }

                        return response()->json($returnMessages, $responseCode);

        } catch (Exception $e) {
            return response()->json(['message' => trans('messages.import.failed'), 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param ImportRequest $request
     * @return JsonResponse
     */
    public function clients(ImportRequest $request)
    {
        try {
            $import = ImportedFile::create([
                'user_id' => Auth::id(),
                'started_at' => date_create(null, new DateTimeZone(config('app.timezone'))),
                'filename' => $request->file('file')->getRealPath(),
            ]);

            $returnImport = $this->importFromExcelService->importClients($request->file);
            $import->update([
                'ended_at' => date_create(null, new DateTimeZone(config('app.timezone'))),
                'status' => Status::SUCCESS,
                'total_records' => $returnImport['totalImported']
            ]);

            $returnMessages = [
                'totalImportedRecords' => $returnImport['totalImported'],
                'message' => trans('messages.import.success'),
                'error' => []
            ];
            $responseCode = Response::HTTP_OK;

            if ($returnImport['failures'] !== null) {
                $responseCode = Response::HTTP_BAD_REQUEST;
                $returnMessages['message'] = trans('messages.import.failed');
                $returnMessages['error'] = $returnImport['failures'];
            }

            return response()->json($returnMessages, $responseCode);
        } catch (Exception $e) {
            return response()->json([
                'message' => trans('messages.import.failed'),
                'error' => $e->getMessage(),
                'failures' => $e->failures()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
