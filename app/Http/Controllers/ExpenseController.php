<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\Expense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $data = Expense::query()
            ->where('branch_id', '=', auth()->user()->branch->branch_id)
            ->orderBy('id','DESC')
            ->paginate(20);
        return Helper::response_with_data($data, false);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'branch_id' => 'required|exists:branches,id',
                'amount' => 'required|numeric',
                'reason' => 'required'
            ]
        );

        $expense = new Expense();
        $expense->branch_id = $request->branch_id;
        $expense->amount = $request->amount;
        $expense->reason = $request->reason;

        if ($expense->save()) {
            return Helper::response_with_data($expense, false);
        }

        return Helper::response_with_data(null, true);
    }

    /**
     * Display the specified resource.
     *
     * @param Expense $expense
     * @return JsonResponse
     */
    public function show(Expense $expense)
    {
        return Helper::response_with_data($expense, false);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Expense $expense
     * @return JsonResponse
     */
    public function update(Request $request, Expense $expense)
    {
        $request->validate(
            [
                'branch_id' => 'nullable|exists:branches,id',
                'amount' => 'nullable|numeric',
                'reason' => 'nullable'
            ]
        );

        $data['branch_id'] = $request->branch_id ? $request->branch_id : $expense->branch_id;
        $data['amount'] = $request->amount ? $request->amount : $expense->amount;
        $data['reason'] = $request->reason ? $request->reason : $expense->reason;

        if ($expense->update($data))
            return Helper::response_with_data(Expense::query()->find($expense->id), false);
        return Helper::response_with_data(null, true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Expense $expense
     * @return JsonResponse
     */
    public function destroy(Expense $expense)
    {
        if ($expense->delete())
            return Helper::response_with_data(null, false);
        return Helper::response_with_data(null, true);
    }


    /**
     * Get Expense list Based On Date Between
     * @param Request $request
     * @return JsonResponse
     */
    public function expenseList(Request $request)
    {
        $start = $request->start_date;
        $end = $request->end_date;
        
        $data = Expense::query()
            ->where('branch_id', '=', auth()->user()->branch->branch_id)
            ->whereBetween('created_at', [$request->start_date, $request->end_date])
            ->orderBy('id','DESC')
            ->paginate(20);
        return Helper::response_with_data($data, false);
    }
}
