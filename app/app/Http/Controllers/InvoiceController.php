<?php

namespace App\Http\Controllers;

use App\Http\Constants;
use App\Http\Requests\InvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class InvoiceController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param int|null $id
     * @return Response
     */
    public function index(Request $request, int $id = null): object
    {
        $per_page = $request->input(Constants::KEY_PER_PAGE, Constants::DEFAULT_PER_PAGE);

        $year = $request->input('year');
        $month = $request->input('month');
        $day = $request->input('day');

        $query =  Invoice::query();
        if(isset($id)) {
            $query = $query->where('client_id', $id);
        }

        if(isset($year)){
            $query = $query->whereYear('date', '=', $year);
        }
        if(isset($month)){
            $query = $query->whereMonth('date', '=', $month);
        }
        if(isset($day)){
            $query = $query->whereDay('date', '=', $day);
        }

        $query = $query->orderBy('id', 'DESC')->paginate($per_page);

        return InvoiceResource::collection($query);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param InvoiceRequest $request
     * @return Response
     */
    public function store(InvoiceRequest $request): object
    {
        try {
            $data = $request->user()->invoices()->create($request->all());
            $data = new InvoiceResource($data);
            return response()->json(['data' => $data], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id): object
    {
        try {
            $data = Invoice::findOrFail($id);
            $data = new InvoiceResource($data);
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $data = Invoice::findOrFail($id);
            $data->update($request->all());
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): object
    {
        try {
            $data = Invoice::findOrFail($id);
            $data->delete();
            return response()->json(['success' => 'Invoice deleted'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

   /**
    * @param Request $request
    * @return mixed
    */
   public function search(Request $request)
   {
       $search = $request->get('search');

       $invoicesResult = Invoice::select('invoices.*')
               ->join('clients', 'clients.id', '=', 'client_id')
               ->where('invoices.invoice_nr', 'like', "%{$search}%")
               ->orWhere('clients.first_name', 'like', "%{$search}%")
               ->orWhere('clients.last_name', 'like', "%{$search}%")
               ->orderBy('clients.first_name')
               ->orderBy('clients.last_name');

       $per_page = $request->input(Constants::KEY_PER_PAGE, Constants::DEFAULT_PER_PAGE);
       if ($per_page <= 0 || $per_page === null)
       {
           $per_page = $invoicesResult->count();
       }
       return $invoicesResult->paginate($per_page);
   }
}
