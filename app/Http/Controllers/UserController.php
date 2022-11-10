<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use App\Http\Requests\RegisterationRequest;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Token;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\Restaurant;

class UserController extends Controller
{
    public function register(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
            // 'image' => 'required|image|mimes:jpeg,png,jpg',
        ]);
        // Return errors if validation error occur.
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'error' => $errors
            ], 400);
        }

        if ($validator->passes()) {
            $name = time() . '.' . explode('/', explode(':', substr($request->image, 0, strpos($request->image, ';')))[1])[1];
                Image::make($request->image)->save(public_path('images/' . $name));

            if ($request->image) {
                $folderPath = "users/";
                $base64Image = explode(";base64,", $request->image);
                $explodeImage = explode("image/", $base64Image[0]);
                $imageName = $explodeImage[1];
                $image_base64 = base64_decode($base64Image[1]);
                $file = $folderPath . uniqid() . '.'.$imageName;

                try {

                    Storage::disk('s3')->put($file, $image_base64, 's3');
                } catch ( \Exception $e) {
                    Log::error($e);
                }
            }

            // saving data into users table
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'image' =>  'https://talabat-iti.s3.amazonaws.com/' . $file,
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'address_details' => $request->input('address_details'),
                'address_name' => $request->input('address_name'),
                'mobile' => $request->input('mobile'),
            ]);

            $token = $user->createToken(time())->plainTextToken;
            // return JSON API (User access token)
            return response()->json([
                'name' => $user->name,
                'email' => $user->email,
                'image' => $user->image,
                'token' => $token,
            ]);
        }
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',

            ]);
            // Return errors if validation error occur.
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validator->errors()
                ], 401);
            }

            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            } else {
                $user = User::where('email', $request->email)->first();
                $token = $user->createToken(time())->plainTextToken;
                return response()->json([
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'token' => $token
                ]);
            }

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
    }

    public function userAddress()
    {
        $id = Auth::id();
        $user = User::where('id',$id)->first(['address_details', 'address_name', 'mobile']);
        return response()->json([
            'address_details' => $user->address_details,
            'address_name' => $user->address_name,
            'mobile' => $user->mobile
        ]);
    }

    public function getOrders()
    {
        $user = auth('sanctum')->user();
        $userId = $user->id;

        $orders = Order::with(['restaurant' => function($q){
            $q->select('id', 'store_name', 'image');
        }])->
        with(['products' => function($q){
            $q->select('products.id', 'name', 'product_count', 'price');
        }])
        ->orderByDesc('orders.created_at')->where('user_id',$userId)->get(['id', 'delivery_fee', 'restaurant_id']);
        return response()->json([
            'data' => $orders
        ]);
    }

    public function userDetails()
    {
        $user = auth('sanctum')->user();
        $userId = $user->id;

        $userDetails = User::where('id',$userId)->get(['name' , 'email' , 'mobile']);

        return response()->json([
            'name' => $userDetails->name,
            'email' => $userDetails->email,
            'mobile' => $userDetails->mobile,
        ]);
    }
}
