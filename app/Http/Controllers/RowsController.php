<?php

namespace App\Http\Controllers;

use App\Http\Requests\RowsRequest;
use App\Models\Row;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class RowsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(RowsRequest $request): \Illuminate\Http\JsonResponse
    {
        #TODO: add pagination

        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        $rows = Row::query()
            ->select(['id','name','date'])
            ->whereBetween('date', [$fromDate, $toDate])
            ->orderBy('date')
            ->get();
        $grouped = $rows->groupBy('date');

        $result = $grouped->map(fn (Collection $items, $date) => [
            'date' => Carbon::make($date)->format('Y-m-d'),
            'rows' => $items->map(fn ($row) => [
                'id' => $row->id,
                'name' => $row->name,
            ])->values(),
        ])->values();

        return response()->json($result);
    }

}
