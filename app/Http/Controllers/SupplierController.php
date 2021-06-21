<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return Helper::response_with_data(Supplier::all(), false);
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
            $data['status'] = $request->status;
            $data['user_id'] = $User->id;
            $data['type'] = $request->type;
            $data['address'] = $request->address;
            $data['details'] = $request->details;
            $supplier = Supplier::query()->create($data);
            if ($supplier) {
                return Helper::response_with_data($supplier->load('user'), false);
            }
        }
        return Helper::response_with_data(null, true);
    }

    /**
     * Display the specified resource.
     *
     * @param Supplier $supplier
     * @return JsonResponse
     */
    public function show(Supplier $supplier)
    {
        return Helper::response_with_data($supplier->load('user'), false);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Supplier $supplier
     * @return JsonResponse
     */
    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|max:255',
            'image' => 'nullable|image|max:1999'
        ]);


        $user_id = $supplier->user_id;
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

        $supplier->address = $request->address ? $request->address : $supplier->address;
        $supplier->type = $request->type ? $request->type : $supplier->type;
        $supplier->status = $request->status ? $request->status : $supplier->status;
        $supplier->isActive = $request->isActive ? $request->isActive : $supplier->isActive;
        $supplier->details = $request->details ? $request->details : $supplier->details;

        if ($user->save() && $supplier->save()) {
            return Helper::response_with_data(Supplier::with('user')->find($supplier->id), false);
        }
        return Helper::response_with_data(null, true);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Supplier $supplier
     * @return Response
     */
    public function destroy(Supplier $supplier)
    {

    }
}
