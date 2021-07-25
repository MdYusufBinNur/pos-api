<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Http\Resources\BranchResourceCollection;
use App\Models\Branch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use PHPUnit\TextUI\Help;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return BranchResourceCollection
     */
    public function index()
    {
        return new BranchResourceCollection(Branch::with('shop')->get());
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => "required",
            'shop_id' => 'required',
            'address' => '',
            'mobile' => '',
            'details' => ''
        ]);
        if ($request->hasFile('file')) {
            $image = Helper::save_file($request->file, 'branch');
            $request['image']  = $image;
        }
        $data = collect($request);
        $data->forget('file')->toArray();
        $branch = Branch::query()->create($data->toArray());
        if ($branch)
        {
            return Helper::response_with_data($branch, false);
        }
        return Helper::response_with_data(null, true);
    }

    /**
     * Display the specified resource.
     *
     * @param Branch $branch
     * @return JsonResponse
     */
    public function show(Branch $branch)
    {
        return Helper::response_with_data($branch, false);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Branch $branch
     * @return JsonResponse
     */
    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'name' => "nullable",
            'shop_id' => 'nullable|exists:shops,id',
            'address' => '',
            'mobile' => '',
            'details' => ''
        ]);
        if ($request->hasFile('file')) {
            $image = Helper::save_file($request->file, 'branch');
            $request['image']  = $image;
        }
        $data = collect($request);
        if ($request->hasFile('file')) {
            $data->forget('file');
        }
        if ($branch->update($data->toArray()))
        {
            return Helper::response_with_data(Branch::query()->find($branch->id), false);
        }
        return Helper::response_with_data(null, true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Branch $branch
     * @return JsonResponse
     */
    public function destroy(Branch $branch)
    {
        File::delete($branch->image);
        if ($branch->delete()){

            return Helper::response_with_data(null, false);
        }
        return Helper::response_with_data(null, true);
    }
}
