<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Http\Resources\ShopResource;
use App\Http\Resources\ShopResourceCollection;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return ShopResourceCollection
     */
    public function index()
    {
        return new ShopResourceCollection(Shop::all());
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
            'name' => 'required|max:255',
            'email' => "required|email|max:255|unique:users,email",
            'mobile' => "nullable|unique:Users,mobile",
            'password' => 'required|min:6',
            'image' => 'nullable|image|max:1999'
        ]);
        $user = new User();
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->name = $request->name;
        $user->role = Helper::ROLE_SHOP;
        $user->password = bcrypt($request->password);
        if ($request->hasFile('image')) {
            $image = Helper::save_file($request->image, 'user');
            $user->image = $image;
        }
        if ($user->save()) {
            $shop = Shop::query()->insert([
                'user_id' => $user->id,
                'address' => $request->address,
                'status' => $request->status ? $request->status : 'pending'
            ]);
            $userId = $user->id;
            $data = Shop::with('user')->where('user_id','=', $user->id)->first();
            return Helper::success_response_with_data(new ShopResource($data));
        }
        return Helper::error_response();
    }

    /**
     * Display the specified resource.
     *
     * @param Shop $shop
     * @return JsonResponse
     */
    public function show(Shop $shop)
    {
        return Helper::success_response_with_data(new ShopResource($shop));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Shop $shop
     * @return JsonResponse
     */
    public function update(Request $request, Shop $shop)
    {
        $request->validate([
            'name' => 'max:255',
            'mobile' => "nullable|unique:Users,mobile",
            'image' => 'nullable|image|max:1999'
        ]);
        $user = User::query()->where('id','=', $shop->user_id)->first();
        $user->mobile = $request->mobile;
        $user->name = $request->name ? $request->name : $user->name;
        $user->role = $request->role ? $request->role : $user->role;
        if ($request->hasFile('image')) {
            $image = Helper::save_file($request->image, 'user');
            $user->image = $image;
        }


        $shop->address = $request->address;
        $shop->status = $request->status;
        $shop->description = $request->description;
        if ($user->update() && $shop->update()) {
            $data = $shop->load('user');
            return Helper::success_response_with_data(new ShopResource($data));
        }
        return Helper::error_response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
