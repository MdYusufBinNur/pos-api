<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Http\Resources\BranchResourceCollection;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use PHPUnit\TextUI\Help;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return BranchResourceCollection
     */
    public function index()
    {
        return new BranchResourceCollection(Brand::all());
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
                'name' => 'required',
                'shop_id' => 'nullable|exists:shops,id'
            ]
        );

        $data['name'] = $request->name;
        $data['shop_id'] = $request->shop_id;
        $data['details'] = $request->details;
        $data['origin'] = $request->origin;

        if ($request->hasFile('image')) {
            $image = Helper::save_file($request->image, 'brand');
            $data['image']  = $image;
        }
        $brand = Brand::query()->create($data);
        if ($brand){
            return Helper::response_with_data($brand, false);
        }

        return Helper::response_with_data(null, true);
    }

    /**
     * Display the specified resource.
     *
     * @param Brand $brand
     * @return JsonResponse
     */
    public function show(Brand $brand)
    {
        return Helper::response_with_data($brand, false);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Brand $brand
     * @return JsonResponse
     */
    public function update(Request $request, Brand $brand)
    {
        $request->validate(
            [
                'name' => 'nullable',
                'shop_id' => 'nullable|exists:shops,id'
            ]
        );

        $data['name'] = $request->name ? $request->name : $brand->name;
        $data['shop_id'] = $request->shop_id ? $request->shop_id : $brand->shop_id;
        $data['details'] = $request->details ? $request->details : $brand->details;
        $data['origin'] = $request->origin ? $request->origin : $brand->origin;
        $data['isActive'] = !empty($request->isActive) ? $request->isActive : $brand->isActive;

        if ($request->hasFile('image')) {
            $image = Helper::save_file($request->image, 'brand');
            $data['image']  = $image;
            File::delete($brand->image);
        }

        if ($brand->update($data)){
            return Helper::response_with_data(Brand::query()->find($brand->id), false);
        }

        return Helper::response_with_data(null, true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Brand $brand
     * @return JsonResponse
     */
    public function destroy(Brand $brand)
    {
        File::delete($brand->image);
        if ($brand->delete()) {
            return Helper::response_with_data(null, false);
        }
        return Helper::response_with_data(null, true);
    }
}
