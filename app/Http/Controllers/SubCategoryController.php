<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\SubCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return Helper::response_with_data(SubCategory::all(), false);

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
                'category_id' => 'required|exists:categories,id',
                'name' => 'required'
            ]
        );

        $subCategory = SubCategory::query()->create($request->all());
        if ($subCategory)
        {
            return Helper::response_with_data($subCategory, false);
        }
        return Helper::response_with_data(null, false);
    }

    /**
     * Display the specified resource.
     *
     * @param SubCategory $subCategory
     * @return JsonResponse
     */
    public function show(SubCategory $subCategory)
    {
        return Helper::response_with_data($subCategory, false);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param SubCategory $subCategory
     * @return JsonResponse
     */
    public function update(Request $request, SubCategory $subCategory)
    {
        $request->validate(
            [
                'category_id' => 'nullable|exists:categories,id',
                'name' => ''
            ]
        );

        if ($subCategory->update($request->all()))
        {
            return Helper::response_with_data($subCategory, false);
        }
        return Helper::response_with_data(null, false);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param SubCategory $subCategory
     * @return JsonResponse
     */
    public function destroy(SubCategory $subCategory)
    {
        if ($subCategory->delete()){

            return Helper::response_with_data(null, false);
        }
        return Helper::response_with_data(null, true);
    }
}
