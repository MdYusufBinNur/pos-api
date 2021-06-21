<?php

namespace App\Http\Controllers\Api\v1;

use App\Helper\Helper;
use App\Http\Controllers\Controller;

use App\Http\Resources\UserResource;
use App\Http\Resources\UserResourceCollection;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return UserResourceCollection
     */
    public function index()
    {
        $Users = User::all();
        return new UserResourceCollection($Users);
    }

    /**
     * Display the specified resource.
     *
     * @param User $User
     * @return UserResource
     */
    public function show(User $User)
    {
        return new UserResource($User);
    }

    /**
     * Registration for new User
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => "required|email|max:255|unique:users,email",
            'mobile' => "nullable|unique:Users,mobile",
            'password' => 'required|min:6',
            'image' => 'nullable|image|max:1999'
        ]);


        $User = new User();
        $User->name = request('name');
        $User->email = request('email');
        $User->mobile = request('mobile');
        $User->role = request('role');
        $User->password = bcrypt(request('password'));
        //$User->active = 1;
        if ($User->save()) {
            return \response()->json(array('error' => false, 'success' => "New User Created"), 200);
        }
        return \response()->json(array('error' => "Something went wrong !!"), 400);

    }

    /**
     * Mobile Application Authentication Login.
     *
     * @param Request $request
     * @return Response
     */
    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => "required",
            'password' => 'required|min:6'
        ]);

        $User = User::where('email', $request->email)->first();

        if (!$User || !Hash::check($request->password, $User->password)) {
            return response([
                'message' => ['The provided credentials are incorrect.'],
            ], 200);
        }

        $token = $User->createToken('authToken')->accessToken;

        $response = [
            'error' => false,
            'user' => $User,
            'token' => $token
        ];

        return response($response, 200);
    }

    /**
     * Mobile Application Authentication Logout.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        $validatedData = $request->validate([
            'User_id' => "required",
            'device_name' => 'required'
        ]);

        $User = User::find($request->User_id);

        $User->tokens()->where('name', $request->device_name)->delete();

        return response()->json(null, 204);
    }

    /**
     * User List
     * Mode : Public
     */
    public function lists()
    {
        $lists = User::all();
        return new UserResourceCollection($lists);
    }

    /**
     * Get Current User Profile
     */
    public function profile()
    {
        if (auth()->user()) {
            $User_ad = auth()->user()->id;
            $data = User::with('roles', 'details')->find($User_ad);
            return new UserResource($data);
        }
        return response()->json(array('message' => 'Something Went Wrong', 'error' => true), 500);

    }


    /**
     * Update User Profile
     * @param Request $request
     * @return JsonResponse
     */
    public function updateProfile(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'mobile' => "nullable|unique:Users,mobile",
            'image' => 'nullable|image|max:1999'
        ]);


        if (auth()->user()) {
            $User_ad = auth()->user()->id;
            $user = User::query()->find($User_ad);
            $user->mobile = $request->mobile;
            $user->name = $request->name;
            $user->country = $request->country;


            if ($request->hasFile('image')) {
                $image = Helper::save_file($request->image, 'user');
                $user->image = $image;
            }

            if ($user->save()) {
                return response()->json(array('message' => 'Profile Updated', 'error' => false), 500);

            }


        }
        return response()->json(array('message' => 'Something Went Wrong', 'error' => true), 500);

    }

}
