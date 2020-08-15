<?php

namespace App\Http\Controllers;

use App\Http\Constants;
use App\Http\Requests\ClientRequest;
use App\Models\Client;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Spatie\QueryBuilder\QueryBuilder as QueryBuilder;

class ClientController extends BaseController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $per_page = $request->input(Constants::KEY_PER_PAGE, Constants::DEFAULT_PER_PAGE);

        $sortBy = $request->sortBy ? $request->sortBy : 'id';
        $sortDir = $request->sortDesc === 'true' ? 'DESC' : 'ASC';

        if ($per_page <= 0 || $per_page === null)
        {
            $per_page = Client::count();
        }
        $data = QueryBuilder::for(Client::class)->allowedFilters(['first_name','last_name'])->orderBy($sortBy, $sortDir)->paginate($per_page);
        return response($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ClientRequest $request
     * @return JsonResponse
     */
    public function store(ClientRequest $request)
    {
        try {
            $data = $request->user()->clients()->create($request->all());
            return response()->json(['data' => $data], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id)
    {
        try {
            $data = Client::findOrFail($id);
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id)
    {
        try {
            $data = Client::findOrFail($id);
            $data->update($request->all());
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id)
    {
        try {
            $data = Client::findOrFail($id);
            $data->delete();
            return response()->json(['success' => 'Client deleted'], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Search clients
     * @param Request $request
     * @return Response
     */
    public function searchClient(Request $request)
    {
        $search = $request->input('search');

        $sortBy = $request->sortBy ? $request->sortBy : 'id';
        $sortDir = $request->sortDesc === 'true' ? 'DESC' : 'ASC';

        return Client::where('first_name', 'like', "%{$search}%")
            ->orWhere('last_name', 'like', "%{$search}%")
            ->orWhere('cellagon_id', 'like', "%{$search}%")
            ->orWhere('internal_id', 'like', "%{$search}%")
            ->orWhere('city', 'like', "%{$search}%")
            ->orderBy($sortBy, $sortDir)
            ->paginate($request->input(Constants::KEY_PER_PAGE, Constants::DEFAULT_PER_PAGE));
    }

}
