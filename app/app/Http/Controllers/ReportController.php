<?php

namespace App\Http\Controllers;

use App\Http\Constants;
use App\Http\Requests\ReportYearRequest;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class ReportController extends BaseController
{
    public function clients(Request $request)
    {
        return Client::where('first_name', 'LIKE', '%' . $request->first_name . '%')->paginate($request->input(Constants::KEY_PER_PAGE, Constants::DEFAULT_PER_PAGE));
    }

    public function calls(Request $request)
    {
        $result = DB::table('invoices')
            ->leftJoin('clients', 'invoices.client_id', '=', 'clients.id')
            ->select('client_id', 'clients.id', 'clients.email', 'clients.first_name', 'clients.last_name', 'clients.telephone', 'clients.mobile', DB::raw('SUM(amount) as total'), DB::raw('max(date) as max_date'), DB::raw('min(invoices.date) as min_date'), DB::raw('count(invoices.id) as count'))
            ->groupBy('clients.id', 'clients.email', 'clients.first_name', 'clients.last_name')->orderBy('max_date', 'DESC')
            ->get();

        $finalResult = [];


        foreach ($result as $r) {
            $minDate = Carbon::create($r->min_date);
            $maxDate = Carbon::create($r->max_date);
            $total_sales = $r->count;

            $diffInDays = $minDate->diffInDays($maxDate);

            $average = $diffInDays / $total_sales;

            $difference = Carbon::now()->diffInDays($maxDate);

            if ($difference > $average - 15 && $difference < $average + 15) {
                $r->average = $average;
                $r->difference = $difference;
                $finalResult[] = $r;
            }
        }

        $total = count($finalResult);
        $perPage = $request->input(Constants::KEY_PER_PAGE, Constants::DEFAULT_PER_PAGE);
        $currentPage = $request->input("page") ?? 1;

        $starting_point = ($currentPage * $perPage) - $perPage;
        $paginationArray = array_slice($finalResult, $starting_point, $perPage, true);

        $array = new Paginator($paginationArray, $perPage, $currentPage, [
            'path' => $request->url(),
            'query' => $request->query(),
            'total' => $total,
        ]);

        return $array;
    }

    public function bests(ReportYearRequest $request)
    {
        $year = $request->input('year') === null ? Carbon::now()->format('Y') : $request->input('year');
        $bestResult = DB::table('invoices')
            ->leftJoin('clients', 'invoices.client_id', '=', 'clients.id')
            ->select(
                'clients.id',
                'clients.cellagon_id',
                'clients.internal_id',
                'clients.email',
                'clients.first_name',
                'clients.last_name',
                'clients.telephone',
                'clients.mobile',
                'clients.address',
                'clients.city',
                'clients.country',
                'clients.postal_code',
                DB::raw('SUM(invoices.amount) as total, MAX(invoices.date) as date, COUNT(invoices.id) as countInvoices')
                )
            ->whereYear('invoices.date', '=', $year)
            ->groupBy('clients.id', 'clients.email', 'clients.first_name', 'clients.last_name')
            ->orderBy('total', 'desc');

        $perPage = $request->input(Constants::KEY_PER_PAGE, Constants::DEFAULT_PER_PAGE);

        if ($perPage <= 0 || $perPage === null)
        {
            $perPage = $bestResult->get()->count();
        }

         return $bestResult->paginate($perPage);
    }

    public function worst(ReportYearRequest $request)
    {
        $year = $request->input('year') === null ? Carbon::now()->format('Y') : $request->input('year');
        return DB::table('invoices')
            ->leftJoin('clients', 'invoices.client_id', '=', 'clients.id')
            ->select('clients.id', 'clients.email', 'clients.first_name', 'clients.last_name', 'clients.telephone', 'clients.mobile', DB::raw('SUM(invoices.amount) as total, MAX(invoices.date) as date, COUNT(invoices.id) as countInvoices'))
            ->whereYear('invoices.date', '=', $year)
            ->groupBy('clients.id', 'clients.email', 'clients.first_name', 'clients.last_name')->orderBy('total', 'asc')->paginate($request->input(Constants::KEY_PER_PAGE, Constants::DEFAULT_PER_PAGE));

    }

    public function deprecated(Request $request)
    {
        return DB::table('invoices')
            ->leftJoin('clients', 'invoices.client_id', '=', 'clients.id')
            ->select('clients.id', 'clients.email', 'clients.first_name', 'clients.last_name', 'clients.telephone', 'clients.mobile', DB::raw('SUM(invoices.amount) as total, MAX(invoices.date) as date, COUNT(invoices.id) as countInvoices, '. Constants::DEFAULT_DAYS_DEPRECATED . ' - DATEDIFF(now(), MAX(invoices.date)) as days_to_block'))
            ->whereRaw('DATEDIFF(now(), date) > ' . Constants::DEFAULT_DAYS_DEPRECATED . '  and client_id not in (SELECT client_id from invoices where DATEDIFF(now(), date) < ' . Constants::DEFAULT_DAYS_DEPRECATED . ' group by client_id)')
            ->groupBy('clients.id', 'clients.email', 'clients.first_name', 'clients.last_name')->orderBy('days_to_block', 'desc')->paginate($request->input(Constants::KEY_PER_PAGE, Constants::DEFAULT_PER_PAGE));

    }

    public function totalPerYear(Request $request, string $clientId)
    {
        $result =  DB::table('invoices')
            ->select(DB::raw('YEAR(date) as year'), DB::raw('SUM(amount) as total'))
            ->where('client_id', $clientId)
            ->groupBy('year')->orderBy('year', 'desc')->get();

        $total = count($result->toArray());
        $perPage = $request->input(Constants::KEY_PER_PAGE, Constants::DEFAULT_PER_PAGE);
        $currentPage = $request->input("page") ?? 1;

        $starting_point = ($currentPage * $perPage) - $perPage;
        $paginationArray = array_slice($result->toArray(), $starting_point, $perPage, true);

        $array = new Paginator($paginationArray, $perPage, $currentPage, [
            'path' => $request->url(),
            'query' => $request->query(),
            'total' => $total,
        ]);

        return $array;
    }

    public function totalInvoices(ReportYearRequest $request)
    {
       $year = $request->input('year') === null ? Carbon::now()->format('Y') : $request->input('year');
       return  DB::table('invoices')
                ->select(DB::raw('MONTH(date) as month'), DB::raw('YEAR(date) as year'), DB::raw('SUM(amount) as total'))
                ->whereYear('date', '=', $year)
                ->groupBy('month')
                ->get();

    }
}
