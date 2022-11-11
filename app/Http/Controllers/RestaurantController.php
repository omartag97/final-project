<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterationRequest;
use App\Models\Branch;
use App\Models\Order;
use App\Models\Product;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Token;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RestaurantController extends Controller
{
    public function restaurantRegister(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'store_name' => 'required|string|max:255',
            'email' => 'required|email|unique:restaurants|max:255',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
        ]);

        // Return errors if validation error occur.
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'error' => $errors
            ], 400);
        }

        if ($validator->passes()) {

                $name = time() . '.' . explode('/', explode(':', substr($request->input('logo'), 0, strpos($request->image, ';')))[1])[1];

                if ($request->image) {
                    $folderPath = "restaurants/";

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
            $restaurant = Restaurant::create([
                'store_name' => $request->input('store_name'),
                'type' => $request->input('type'),
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'mobile' => $request->input('phone'),
                'email' => $request->input('email'),
                'password' => $request->input('password'),
                'region' => $request->input('address'),
                'descreption' => $request->input('descreption'),
                'working_hours' => $request->input('working_hours'),
                'delivery_time' => $request->input('delivery_time'),
                'min_order' => $request->input('min_order'),
                'image' =>  'https://talabat-iti.s3.amazonaws.com/' . $file ,
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
            ]);

            $token = $restaurant->createToken(time())->plainTextToken;

            // return JSON API (restaurant access token)
            return response()->json([
                'name' => $restaurant->name,
                'email' => $restaurant->email,
                'image' => $request->image,
                'token' => $token,
            ]);
        }
    }

    public function restaurantLogin(Request $request)
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

            $restaurants = Restaurant::where('email',$request->email)->first();
            if (!$restaurants || ! Hash::check($request->password , $restaurants->password)) {
                return response()->json([
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            } else {
                $restaurant = Restaurant::where('email', $request->email)->first();
                $token = $restaurant->createToken(time())->plainTextToken;
                return response()->json([
                    'id' => $restaurant->id,
                    'name' => $restaurant->name,
                    'email' => $restaurant->email,
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

    public function restaurantLogout(Request $request)
    {
        $request->user()->token()->revoke();
    }

    public function getAllRestaurants()
    {
        $restaurant = Restaurant::all(['id' ,'store_name' ,'description' ,'image'])->toArray();
        return response()->json([
            'data' => $restaurant
        ]);
    }

    public function getRestaurant($id)
    {

        $restaurant = Restaurant::findorFail($id);
        return response()->json([
            'id' => $restaurant->id,
            'store_name' => $restaurant->name,
            'description' => $restaurant->description,
            'image' => $restaurant->image,
        ]);
    }

    public function restInfo($id)
    {
        $restaurants = Restaurant::where('id',$id)->first(['id', 'store_name', 'type', 'image', 'region', 'min_order', 'working_hours', 'delivery_time', 'delivery_fee', 'type', 'online_tracking']);
        return response()->json([
            'data' => [
                'id' => $restaurants->id,
                'name' => $restaurants->store_name,
                'type' => $restaurants->type,
                'image' => $restaurants->image,
                'region' => $restaurants->region,
                'minOrder' => $restaurants->min_order,
                'workingHours' => $restaurants->working_hours,
                'deliveryTime' => $restaurants->delivery_fime,
                'deliveryFee' => $restaurants->delivery_fee,
                'description' => $restaurants->type,
                'onlineTracking' => $restaurants->online_tracking,
            ]
        ]);
    }

    public function restaurantSearch(Request $request)
    {
        $search = $request->keyword;
        $restaurants = Restaurant::where('store_name', 'LIKE', '%'.$search.'%')->get();
        if(!!count($restaurants)){
            return response()->json([
                'data' => $restaurants
            ]);
        }else{
            return response()->json([
                'data' => 'No such a restaurant in our database!'
            ]);
        }
    }

    public function getPendedOrders()
    {
        $restaurant = auth('sanctum')->user();
        $restaurantId = $restaurant->id ;
        $orders = Order::with(['products' => function($q){
            $q -> select('products.id', 'name', 'product_count', 'price');
        }])->orderByDesc('orders.created_at')->where('restaurant_id',$restaurantId)->Where('status','pending')->get(['id', 'delivery_fee','created_at']);
        return response()->json([
            'data' => $orders
        ]);
    }

    public function getAcceptedOrders()
    {
        $restaurant = auth('sanctum')->user();
        $restaurantId = $restaurant->id ;
        $orders = Order::with(['products' => function($q){
            $q -> select('products.id', 'name', 'product_count', 'price');
        }])->orderByDesc('orders.created_at')->where('restaurant_id',$restaurantId)->Where('status','accepted')->get(['id', 'delivery_fee','created_at']);
        return response()->json([
            'data' => $orders
        ]);
    }

    public function getRestaurantProducts()
    {
        $restaurant = auth('sanctum')->user();
        $restaurantId = $restaurant->id;
        $products = Product::where('restaurant_id',$restaurantId)->get();

        return response()->json([
            'data' => $products
        ]);
    }

    public function nearByRestaurants()
    {
        $user = auth('sanctum')->user();
        $latitude = $user->latitude;
        $longitude = $user->longitude;

        $restaurants = Restaurant::select("*", DB::raw("6371 * acos(cos(radians(" . $latitude . "))
        * cos(radians(latitude)) * cos(radians(longitude) - radians(" . $longitude . "))
        + sin(radians(" .$latitude. ")) * sin(radians(latitude))) AS distance"))

        ->having('distance', '<', 20)
        ->orderBy("distance",'asc')
        ->offset(0)
        ->limit(20)
        ->get();

        return response()->json([
            'data' => $restaurants
        ]);

    }

}

