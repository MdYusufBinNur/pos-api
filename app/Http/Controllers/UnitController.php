<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return Helper::response_with_data(Unit::all(), false);
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
                'name' => 'required|unique:units,name'
            ]
        );

        $unit = Unit::query()->create($request->all());
        if ($unit) {
            return Helper::response_with_data($unit, false);
        }
        return Helper::response_with_data(null, true);
    }

    /**
     * Display the specified resource.
     *
     * @param Unit $unit
     * @return JsonResponse
     */
    public function show(Unit $unit)
    {
        return Helper::response_with_data($unit, false);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, Unit $unit)
    {
        $request->validate(
            [
                'name' => 'nullable|unique:units,name'
            ]
        );

        if ($unit->update($request->all())) {
            return Helper::response_with_data($unit, false);
        }
        return Helper::response_with_data(null, true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Unit $unit
     * @return JsonResponse
     */
    public function destroy(Unit $unit)
    {
        if ($unit->delete()) {
            return Helper::response_with_data(null, false);
        }
        return Helper::response_with_data(null, true);
    }
}
