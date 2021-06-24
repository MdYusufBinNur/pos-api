<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPUnit\TextUI\Help;

class PurchaseRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return Helper::response_with_data(PurchaseRequest::with('purchase_request_log.product')->where('isActive', true)->get(), false);
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
                'supplier_id' => 'required|exists:suppliers,id',
                'branch_id' => 'required|exists:branches,id',
                'product_id' => 'required|array',
                'unit_id' => 'required|array',
                'quantity' => 'required|array',
                'status' => 'nullable'
            ]
        );
        $purchaseRequest = new PurchaseRequest();
        $purchaseRequest->supplier_id = $request->supplier_id;
        $purchaseRequest->isActive = $request->isActive;
        $purchaseRequest->branch_id = $request->branch_id;

        if ($purchaseRequest->save()) {
            foreach ($request->product_id as $key => $value) {
                $requestLog = new PurchaseRequestLog();
                $requestLog->purchase_request_id = $purchaseRequest->id;
                $requestLog->product_id = $request->product_id[0];
                $requestLog->unit_id = $request->unit_id[0];
                $requestLog->quantity = $request->quantity[0];
                $requestLog->details = $request->details;
                $requestLog->isActive = $request->isAvtive;
                $requestLog->save();
            }

            return Helper::response_with_data($purchaseRequest->load('purchase_request_log'), false);
        }
        return Helper::response_with_data(null, true);
    }

    /**
     * Display the specified resource.
     *
     * @param PurchaseRequest $purchaseRequest
     * @return Response
     */
    public function show(PurchaseRequest $purchaseRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param PurchaseRequest $purchaseRequest
     * @return Response
     */
    public function edit(PurchaseRequest $purchaseRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param PurchaseRequest $purchaseRequest
     * @return JsonResponse
     */
    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        $purchase['isActive'] = $request->isActive;

        if ($request->isActive) {
            $purchaseRequest->update($purchase);
        }
        $purchaseRequestLog = PurchaseRequestLog::query()
            ->find($request->purchase_request_log_id);

        $data['quantity'] = $request->quantity ? $request->quantity : $purchaseRequestLog->quantity;
        $data['unit_id'] = $request->unit_id ? $request->unit_id :  $purchaseRequestLog->unit_id;
        if ($purchaseRequestLog->update($data)) {
            return Helper::response_with_data($purchaseRequest->load('purchase_request_log'), false);
        }
        return Helper::response_with_data(null, true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param PurchaseRequest $purchaseRequest
     * @return JsonResponse
     */
    public function destroy(PurchaseRequest $purchaseRequest)
    {
        $purchase_request_id = $purchaseRequest->id;
        $purchaseRequestLog = PurchaseRequestLog::query()
            ->where('purchase_request_id', '=', $purchaseRequest->id)
            ->get();
        if ($purchaseRequest->delete())
        {
            foreach ($purchaseRequestLog as $value){
                $value->delete();
            }
            return Helper::response_with_data(null, false);
        }
        return Helper::response_with_data(null, true);
    }
}
