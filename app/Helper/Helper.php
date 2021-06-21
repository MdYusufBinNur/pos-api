<?php


namespace App\Helper;


use http\Env\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class Helper
{
    const ROLE_SUPER = 'super';
    const ROLE_ADMIN = 'admin';
    const ROLE_SUPPLIER = 'supplier';
    const ROLE_CUSTOMER = 'customer';
    const ROLE_BRANCH = 'branch';
    const ROLE_BRANCH_MANAGER = 'branch_manager';
    const ROLE_SHOP = 'shop';
    const ROLE_SELLER = 'seller';

    public static function success_response()
    {
        return response()->json(
            [
                'error' => false,
                'message' => 'Successfully Created'
            ]
        );
    }
    public static function success_response_with_data($data)
    {
        return response()->json(
            [
                'error' => false,
                'message' => 'Success',
                'data' => $data
            ]
        );
    }

    public static function error_response()
    {
        return response()->json(
            [
                'error' => true,
                'message' => 'Something went wrong.'
            ]
        );
    }

    public static function save_file($image,  $directory)
    {
        $path = "$directory";
        if(!File::exists($path)) {
            File::makeDirectory($path,false, false);
        }

        $fileType    = $image->getClientOriginalExtension();
        $imageName   = rand().'.'.$fileType;
        $path_info = pathinfo($imageName, PATHINFO_EXTENSION);
        $directory   = $path."/";
       if ( $path_info == "png" || $path_info == 'jpeg' || $path_info == "jpg" || $path_info == "PNG" || $path_info == "JPEG" || $path_info == "JPG"){
            $imageUrl    = $directory.$imageName;
            Image::make($image)->save($imageUrl);
        }
        else{
            $imageUrl = "No Valid File";
        }

        return $imageUrl;
    }

    public static function response_with_data($data, $error)
    {
        return response()->json(
            [
                'error' => $error,
                'message' => $error == true ? 'Something went wrong' : 'Success',
                'data' => $error == true ? [] : $data
            ]
        );
    }


}
