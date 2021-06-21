<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Bitfumes\Multiauth\Model\Admin;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Symfony\Component\VarDumper\Cloner\Data;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;


    protected function sendResetLinkResponse(Request $request, $response)
    {
        return response(['message' => $response]);

    }


    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return response(['error' => $response], 422);

    }

    public function sendPasswordResetLinkForMobileDevice(Request $request)
    {
        $this->validateEmail($request);

        $token = rand(111111, 999999);

        $admin = Admin::query()->where('email', '=', $request->email)->first();

        if (empty($admin)) {
            return response()->json([
                "error" => false,
                "message" => "Credential don't match"
            ]);
        }
        $admin->sendPasswordResetNotificationForMobileDevice($token, $admin);

        $checkIfExist = DB::table('password_resets')->where('email', '=', $request->email)->delete();

        $insert = DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Date::now()
        ]);
        if ($insert) {
            return response()->json([
                "error" => false,
                "message" => 'Code sent successfully'
            ]);
        }

        return response()->json([
            "error" => true,
            "message" => 'Something went wrong ! Try again.'
        ]);
    }

    public function checkCodeValidity(Request $request)
    {
        $validatedData = $request->validate([
            'email' => "required",
            'code' => 'required'
        ]);

        return $this->checkCode($request->email, $request->code);
    }

    public function updateAdminPassword(Request $request)
    {
        $validatedData = $request->validate([
            'email' => "required",
            'password' => 'required|min:6',
            'code' => 'required'
        ]);


        $removeEmailWhiteSpace = preg_replace('/\s+/', '', $request->email);
        $admin = Admin::query()->where('email', '=', $removeEmailWhiteSpace)->first();

        if (empty($admin)) {
            return response()->json([
                "error" => true,
                "message" => "Credential don't match"
            ]);
        }

        $checkCredentialsValidity = $this->checkCode($removeEmailWhiteSpace, $request->code);
        if ($checkCredentialsValidity->getData()->error == true) {
            return $checkCredentialsValidity;
        }

        $password =  bcrypt($request->password);
        if ($admin->update(['password' => $password])) {
            DB::table('password_resets')->where('email', '=', $removeEmailWhiteSpace)->delete();
            return response()->json([
                "error" => false,
                "message" => "Password has been updated"
            ]);
        }

        return response()->json([
            "error" => true,
            "message" => "Something went wrong"
        ]);
    }

    protected function checkCode($email, $code)
    {
        $check = DB::table('password_resets')
            ->where('email', '=', $email)
            ->where('token', '=', $code)
            ->first();


        if (empty($check)) {
            return response()->json([
                "error" => true,
                "message" => "Invalid Code"
            ]);
        }
        $checkTime = Date::now()->diffInMinutes($check->created_at);

        if ($checkTime > 60) {
            DB::table('password_resets')->where('email', '=', $email)->delete();
            return response()->json([
                "error" => true,
                "message" => "Invalid Code"
            ]);
        }
        return response()->json([
            "error" => false,
            "message" => "Code matched successfully."
        ]);
    }
}
