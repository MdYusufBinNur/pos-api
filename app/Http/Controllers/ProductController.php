<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return Helper::response_with_data(Product::all(), false);
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
                'sub_category_id' => 'required|exists:sub_categories,id',
                'brand_id' => 'nullable|exists:brands,id',
                'name' => 'required|max:255|unique:products,name',
                'description' => 'required',
                'file' => 'required|max:255',
            ]
        );
        if ($request->hasFile('file'))
        {
            $image = Helper::save_file($request->file, 'products');

            $request['image'] = $image;
        }

        $data = collect($request);
        if ($request->hasFile('file')) {
            $data->forget('file');
        };
        $product = Product::query()->create($data->toArray());
        if ($product)
        {
            return Helper::response_with_data($product,false);
        }
        return Helper::response_with_data(null, true);
    }

    /**
     * Display the specified resource.
     *
     * @param Product $product
     * @return JsonResponse
     */
    public function show(Product $product)
    {
        return Helper::response_with_data($product,false);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Product $product
     * @return JsonResponse
     */
    public function update(Request $request, Product $product)
    {
        $request->validate(
            [
                'category_id' => 'nullable|exists:categories,id',
                'sub_category_id' => 'nullable|exists:sub_categories,id',
                'unit_id' => 'nullable|exists:units,id',
                'brand_id' => 'nullable|exists:units,id',
                'name' => 'nullable|max:255|unique:products,name',
                'description' => 'nullable',
                'image' => 'nullable|max:255',
            ]
        );
        if ($request->hasFile('file'))
        {
            $image = Helper::save_file($request->file, 'products');

            $request['image'] = $image;
        }

        $data = collect($request);
        if ($request->hasFile('file')) {
            $data->forget('file');
        };

        if ($product->update($data->toArray()))
        {
            return Helper::response_with_data(Product::query()->find($product->id),false);
        }
        return Helper::response_with_data(null, true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Product $product
     * @return JsonResponse
     */
    public function destroy(Product $product)
    {
        $product_id = $product->id;

        if ($product->delete())
        {
            File::delete($product_id);
            return Helper::response_with_data(null, false);
        }

        return Helper::response_with_data(null, true);
    }
}
