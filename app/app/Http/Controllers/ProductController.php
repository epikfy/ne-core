<?php

namespace App\Http\Controllers;

use App\Http\Constants;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Spatie\QueryBuilder\QueryBuilder as QueryBuilder;

class ProductController extends BaseController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function index(Request $request) : Response
    {
        if ($request->input(Constants::KEY_PER_PAGE))
        {
            $per_page = $request->input(Constants::KEY_PER_PAGE, Constants::DEFAULT_PER_PAGE);
            $data = QueryBuilder::for(Product::class)
                ->allowedFilters(['description'])
                ->orderBy('description')
                ->paginate($per_page);
        } else {
            $data = QueryBuilder::for(Product::class)
                ->allowedFields(['id', 'description'])
                ->allowedFilters(['description'])
                ->orderBy('description')
                ->get();
        }
        return response($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductRequest $request
     * @return JsonResponse
     */
    public function store(ProductRequest $request)
    {
        try {
            $data = $request->user()->products()->create($request->all());
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
            $data = Product::findOrFail($id);
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
            $data = Product::findOrFail($id);
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
            $data = Product::findOrFail($id);
            $data->delete();
            return response()->json(['success' => 'Product deleted'], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Search products
     * @param Request $request
     * @return Response
     */
    public function searchProduct(Request $request)
    {
        $search = $request->input('search');
        return Product::where('description', 'like', "%{$search}%")
            ->paginate($request->input(Constants::KEY_PER_PAGE, Constants::DEFAULT_PER_PAGE));
    }
}
