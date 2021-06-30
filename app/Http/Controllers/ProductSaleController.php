<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\Income;
use App\Models\ProductSale;
use App\Models\ProductSaleLog;
use App\Models\ProductStock;
use Illuminate\Http\Request;

class ProductSaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'product_id' => 'required|array',
                'quantity' => 'required|array',
                'price' => 'required|array',
                'customer_id' => 'required|exists:customers,id',
                'branch_id' => 'required|exists:branches,id',
                'paid' => 'required|numeric',
                'delivery_method' => 'required',
                'status' => 'required',
            ]
        );

        $invoice = $this->generateInvoice();
        $productSale = new ProductSale();
        $total = 0;
        $discount = 0;
        $vat = 0;
        foreach ($request->product_id as $key => $value)
        {
            $total += $request->price[$key];
        }

        if ($request->discount && $request->discount > 0)
        {
            $discount = ($request->discount * $total) / 100;
            $productSale->total_discount = $request->discount;
        }
        if ($request->vat && $request->vat > 0)
        {
            $vat = ($request->vat * $total) / 100;
            $productSale->total_vat = $request->vat;
        }
        $final_total = $total + $vat - $discount;

        $productSale->customer_id = $request->customer_id;
        $productSale->branch_id = $request->branch_id;
        $productSale->invoice = $invoice;
        $productSale->paid = $request->paid;
        $productSale->total = $final_total;

        $productSale->status = $request->status;
        $productSale->delivery_method = $request->delivery_method;
        //return $productSale;
        if ($productSale->save())
        {
            $income = new Income();
            $income->total = $productSale->total;
            $income->type = $productSale->delivery_method;
            $income->branch_id = $productSale->branch_id;
            $income->save();

            foreach ($request->product_id as $key => $value)
            {
                $productSaleLog = new ProductSaleLog();
                $productSaleLog->product_sale_id = $productSale->id;
                $productSaleLog->product_id = $value;
                $productSaleLog->price = $request->price[$key];
                $productSaleLog->quantity = $request->quantity[$key];
                if ($productSaleLog->save())
                {
                    $productStockChange = ProductStock::query()
                        ->where('product_id','=', $productSaleLog->product_id)
                        ->where('branch_id','=', $productSale->branch_id)
                        ->first();
                    $qty =  $productStockChange->available_quantity -  $productSaleLog->quantity;

                    $stock['available_quantity'] = $qty > 0 ? $qty : 0;

                    $productStockChange->update($stock);
                }
            }
            return Helper::response_with_data($productSale->load('product_sale_log','customer'), false);
        }
        return Helper::response_with_data(null, true);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductSale  $productSale
     * @return \Illuminate\Http\Response
     */
    public function show(ProductSale $productSale)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProductSale  $productSale
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductSale $productSale)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductSale  $productSale
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductSale $productSale)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductSale  $productSale
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductSale $productSale)
    {
        //
    }

    public function generateInvoice()
    {
        $invoice = "HNST".rand(000000000, 999999999);
        $sku = ProductSale::query()->where('invoice','=', $invoice)->first();
        if ($sku)
        {
            return $this->generateInvoice();
        }
        return $invoice;
    }


    public function onlineDelivery()
    {
        $data = ProductSale::with('product_sale_log','product_sale_log.product','customer.user')
            ->where('branch_id','=', auth()->user()->branch->branch_id)
            ->where('delivery_method','=','online')
            ->get();

        return Helper::response_with_data($data, false);
    }

    public function regularDelivery()
    {
        $data = ProductSale::with('product_sale_log','product_sale_log.product','customer.user')
            ->where('branch_id','=', auth()->user()->branch->branch_id)
            ->where('delivery_method','=','regular')
            ->get();

        return Helper::response_with_data($data, false);
    }
}
