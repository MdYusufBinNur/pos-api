<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\ProductStock;
use App\Models\ProductStockLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return ProductStock[]|\Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return ProductStock::all();
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
                //Stock
                'selling_price' => 'required|numeric',
                'vat' => 'required|numeric',
                'discount' => 'required|numeric',
                'final_price' => 'nullable|numeric',
                'buying_price' => 'nullable|numeric',
                'damaged' => 'nullable|numeric',
                'type' => 'required|string',
                'available_quantity' => 'nullable|string',

                //common
                'branch_id' => 'required|exists:branches,id',
                'product_id' => 'required|exists:products,id',


                // Stock Log
                'unit_id' => 'required|exists:units,id',
                'supplier_id' => 'nullable|exists:suppliers,id',
                'received_by' => 'nullable|exists:users,id',
                'sku' => 'nullable',
                'per_unit_price' => 'required|numeric',
                'quantity' => 'required|numeric',
                'cost' => 'required|numeric', //total buying cost
            ]
        );

        $product_stock = ProductStock::query()
            ->where('product_id', '=', $request->product_id)
            ->where('branch_id', '=', $request->branch_id)
            ->first();

        if ($product_stock) {
            return $this->updateIfSameProduct($request, $product_stock);
        }
        $product_stock = new ProductStock();
        $product_stock->branch_id = $request->branch_id;
        $product_stock->product_id = $request->product_id;
        $product_stock->selling_price = $request->selling_price;
        $product_stock->vat = $request->vat;
        $product_stock->discount = $request->discount;
        $product_stock->buying_price = $request->per_unit_price;
        $product_stock->type = $request->type;
        $product_stock->available_quantity = $request->quantity;
        $product_stock->damaged = $request->damaged ? $request->damaged : 0;
        $total = $request->selling_price;
        $vat = 0;
        $discount = 0;
        if (!empty($request->vat) && $request->vat > 0) {
            $vat = ($request->vat * $request->selling_price) / 100;
        }
        if (!empty($request->discount) && $request->discount > 0) {
            $discount = ($request->discount * $request->selling_price) / 100;
        }
        $final_selling_price = ($total + $vat) - $discount;
        $product_stock->final_price = $final_selling_price;

        $skuCode = $this->generateSKU();

        if ($product_stock->save()) {
            $product_stock_log = new ProductStockLog();
            $product_stock_log->product_stock_id = $product_stock->id;
            $product_stock_log->branch_id = $request->branch_id;
            $product_stock_log->product_id = $request->product_id;
            $product_stock_log->unit_id = $request->unit_id;
            $product_stock_log->supplier_id = $request->supplier_id;
            $product_stock_log->received_by = auth()->user()->id;
            $product_stock_log->sku = $skuCode;
            $product_stock_log->per_unit_price = $request->per_unit_price;
            $product_stock_log->quantity = $request->quantity;
            $product_stock_log->cost = $request->cost;
            if ($product_stock_log->save()) {
                return Helper::response_with_data($product_stock->load('product_stock_log', 'product'), false);
            }
        }
        return Helper::response_with_data(null, true);
    }

    /**
     * Display the specified resource.
     *
     * @param ProductStock $productStock
     * @return JsonResponse
     */
    public function show(ProductStock $productStock)
    {
        return Helper::response_with_data($productStock->load('product', 'product_stock_log'), false);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param ProductStock $product_stock
     * @return JsonResponse
     */
    public function update(Request $request, ProductStock $product_stock)
    {
        $request->validate(
            [
                //Stock
                'selling_price' => 'nullable|numeric',
                'vat' => 'nullable|numeric',
                'discount' => 'nullable|numeric',
                'final_price' => 'nullable|numeric',
                'buying_price' => 'nullable|numeric',
                'damaged' => 'nullable|numeric',
                'type' => 'nullable|string',
                'available_quantity' => 'nullable|string',

                //common
                'branch_id' => 'nullable|exists:branches,id',
                'product_id' => 'nullable|exists:products,id',


                // Stock Log
                'unit_id' => 'nullable|exists:units,id',
                'supplier_id' => 'nullable|exists:suppliers,id',
                'per_unit_price' => 'nullable|numeric',
                'quantity' => 'nullable|numeric',
                'cost' => 'nullable|numeric',
                'sku' => 'required|exists:product_stock_logs,sku',
            ]
        );

        $data['branch_id'] = $request->branch_id ? $request->branch_id : $product_stock->branch_id;
        $data['product_id'] = $request->product_id ? $request->product_id : $product_stock->product_id;
        $data['selling_price'] = $request->selling_price ? $request->selling_price : $product_stock->selling_price;
        $data['vat'] = $request->vat ? $request->vat : $product_stock->vat;
        $data['discount'] = $request->discount ? $request->discount : $product_stock->discount;
        $data['buying_price'] = $request->per_unit_price ? $request->per_unit_price : $product_stock->buying_price;
        $data['damaged'] = $request->damaged ? $request->damaged : $product_stock->damaged;
        $data['type'] = $request->type ? $request->type : $product_stock->type;
        $data['available_quantity'] = $request->quantity ? $request->quantity : $product_stock->available_quantity;

        $total = $request->selling_price ? $request->selling_price : $product_stock->selling_price;
        $vat = 0;
        $discount = 0;
        if (!empty($request->vat) && $request->vat > 0) {
            $vat = ($request->vat * $total) / 100;
        }
        if (!empty($request->discount) && $request->discount > 0) {
            $discount = ($request->discount * $total) / 100;
        }
        $final_selling_price = $total + $vat + $discount;
        $data['final_price'] = $final_selling_price;

        //return $data;
        if ($product_stock->update($data)) {

            $product_stock_log = ProductStockLog::query()
                ->where('sku','=', $request->sku)
                ->first();

            $log_data['branch_id'] = $request->branch_id ? $request->branch_id : $product_stock_log->branch_id;
            $log_data['product_id'] = $request->product_id ? $request->product_id : $product_stock_log->product_id;
            $log_data['unit_id'] = $request->unit_id ? $request->unit_id : $product_stock_log->unit_id;
            $log_data['supplier_id'] = $request->supplier_id ? $request->supplier_id : $product_stock_log->supplier_id;
            $log_data['per_unit_price'] = $request->per_unit_price ? $request->per_unit_price : $product_stock_log->per_unit_price;
            $log_data['quantity'] = $request->quantity ? $request->quantity : $product_stock_log->quantity;
            $log_data['cost'] = $request->cost ? $request->cost : $product_stock_log->cost;
            $product_stock_log->product_stock_id = $product_stock->id;

            if ($product_stock_log->update($log_data))
            {
                return Helper::response_with_data(ProductStock::with('product_stock_log', 'product')->find($product_stock->id), false);
            }
        }
        return Helper::response_with_data(null, true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ProductStock $productStock
     * @return JsonResponse
     */
    public function destroy(ProductStock $productStock)
    {
        $productStockLog = ProductStockLog::query()->where('product_stock_id', '=', $productStock->id)->get();
        if ($productStock->delete()) {
            foreach ($productStockLog as $value) {
                $value->delete();
            }
            return Helper::response_with_data(null, false);
        }

        return Helper::response_with_data(null, true);
    }

    public function updateIfSameProduct(Request $request, ProductStock $productStock)
    {
        $data['branch_id'] = $request->branch_id;
        $data['selling_price'] = $request->selling_price;
        $data['vat'] = $request->vat;
        $data['discount'] = $request->discount;
        $data['type'] = $request->type;
        $data['buying_price'] = $request->per_unit_price;

        $data['damaged'] = $request->damaged ? $request->damaged : $productStock->damaged;

        $total = $request->selling_price;
        $vat = 0;
        $discount = 0;
        if (!empty($request->vat) && $request->vat > 0) {
            $vat = ($request->vat * $request->selling_price) / 100;
        }
        if (!empty($request->discount) && $request->discount > 0) {
            $discount = ($request->discount * $request->selling_price) / 100;
        }

        $data['final_price'] = ($total + $vat) - $discount;

        $quantity = $productStock->available_quantity + $request->quantity;
        $data['available_quantity'] = $quantity;
        if ($productStock->update($data)) {
            $product_stock_log = new ProductStockLog();
            $product_stock_log->product_stock_id = $productStock->id;
            $product_stock_log->branch_id = $request->branch_id;
            $product_stock_log->product_id = $request->product_id;
            $product_stock_log->unit_id = $request->unit_id;
            $product_stock_log->supplier_id = $request->supplier_id;
            $product_stock_log->received_by = auth()->user()->id;
            $product_stock_log->sku = "SKU-" . rand(00000, 99999);
            $product_stock_log->per_unit_price = $request->per_unit_price;
            $product_stock_log->quantity = $request->quantity;
            $product_stock_log->cost = $request->cost;
            if ($product_stock_log->save()) {
                return Helper::response_with_data($productStock->load('product_stock_log', 'product'), false);
            }
        }
        return Helper::response_with_data(null, true);
    }

    public function generateSKU()
    {
        $skuCode = "SKU-".rand(000000, 999999);
        $sku = ProductStockLog::query()->where('sku','=', $skuCode)->first();
        if ($sku)
        {
            return $this->generateSKU();
        }
        return $skuCode;
    }
}
