<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\SupplierCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SupplierCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return Helper::response_with_data(SupplierCategory::with('supplier','category')->get(), false);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'category_id' => 'required|exists:categories,id'
        ]);


        $supplierCategory = SupplierCategory::query()->create($request->all());
        if ($supplierCategory)
        {
            return Helper::response_with_data($supplierCategory, false);
        }
        return Helper::response_with_data(null, true);
    }

    /**
     * Display the specified resource.
     *
     * @param SupplierCategory $supplierCategory
     * @return JsonResponse
     */
    public function show(SupplierCategory $supplierCategory)
    {
        return Helper::response_with_data($supplierCategory, false);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param SupplierCategory $supplierCategory
     * @return JsonResponse
     */
    public function destroy(SupplierCategory $supplierCategory)
    {
        if ($supplierCategory->delete())
        {
            return Helper::response_with_data(null, false);
        }
        return Helper::response_with_data(null, true);
    }
}
