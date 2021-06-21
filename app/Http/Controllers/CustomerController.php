<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return Helper::response_with_data(Customer::all(), false);
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
            'image' => 'nullable|image|max:1999',
        ]);
        $User = new User();
        $User->name = request('name');
        $User->email = request('email');
        $User->mobile = request('mobile');
        $User->role = request('role');
        $User->password = bcrypt(request('password'));
        if ($request->hasFile('image')) {
            $image = Helper::save_file($request->image, 'user');
            $User->image = $image;
        }
        if ($User->save()) {
            $data['user_id'] = $User->id;
            $data['address'] = $request->address;
            $data['isActive'] = $request->isActive;
            $Customer = Customer::query()->create($data);
            if ($Customer) {
                return Helper::response_with_data($Customer->load('user'), false);
            }
        }
        return Helper::response_with_data(null, true);
    }

    /**
     * Display the specified resource.
     *
     * @param Customer $Customer
     * @return JsonResponse
     */
    public function show(Customer $Customer)
    {
        return Helper::response_with_data($Customer->load('user'), false);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Customer $Customer
     * @return JsonResponse
     */
    public function update(Request $request, Customer $Customer)
    {
        $request->validate([
            'name' => 'required|max:255',
            'image' => 'nullable|image|max:1999'
        ]);


        $user_id = $Customer->user_id;
        $user = User::query()->find($user_id);
        $user->mobile = $request->mobile ? $request->mobile : $user->mobile;
        $user->name = $request->name ? $request->name : $user->name;
        $user->country = $request->country ? $request->country : $user->country;
        $user->role = $request->role ? $request->role : $user->role;

        if ($request->hasFile('image')) {
            $image = Helper::save_file($request->image, 'user');
            $user->image = $image;
            File::delete($user->image);
        }

        $Customer->address = $request->address ? $request->address : $Customer->address;
        $Customer->isActive = $request->isActive ? $request->isActive : $Customer->isActive;
        if ($user->save() && $Customer->save()) {
            return Helper::response_with_data(Customer::with('user')->find($Customer->id), false);
        }
        return Helper::response_with_data(null, true);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Customer $Customer
     * @return Response
     */
    public function destroy(Customer $Customer)
    {

    }
}
