<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Http\Resources\CategoryResourceCollection;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return Helper::response_with_data(new CategoryResourceCollection(Category::all()), false);

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
                'name' => 'required|unique:categories,name'
            ]
        );
        $category = Category::query()->create($request->all());
        if ($category){
            return Helper::response_with_data($category, false);
        }
        return Helper::response_with_data(null, true);

    }

    /**
     * Display the specified resource.
     *
     * @param Category $category
     * @return JsonResponse
     */
    public function show(Category $category)
    {
        return Helper::response_with_data($category, false);

    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Category $category
     * @return JsonResponse
     */
    public function update(Request $request, Category $category)
    {
        $request->validate(
            [
                'name' => 'nullable|unique:categories,name'
            ]
        );

        if ($category->update($request->all())){
            return Helper::response_with_data(Category::query()->find($category->id), false);
        }
        return Helper::response_with_data(null, true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Category $category
     * @return JsonResponse
     */
    public function destroy(Category $category)
    {
        if ($category->delete()){

            return Helper::response_with_data(null, false);
        }
        return Helper::response_with_data(null, true);
    }
}
