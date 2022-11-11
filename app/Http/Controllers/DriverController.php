<?php

namespace App\Http\Controllers;

use App\Models\Drivers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterationRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;



class DriverController extends Controller
{
    public function driversRegisteration(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:drivers|max:255',
            'password' => 'required|min:8',
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


            if ($request->image) {
                $folderPath = "users/";

                $base64Image = explode(";base64,", $request->image);
                $explodeImage = explode("image/", $base64Image[0]);
                $imageName = $explodeImage[1];
                $image_base64 = base64_decode($base64Image[1]);
                $file = $folderPath . uniqid() . '.' . $imageName;

                try {
                    Storage::disk('s3')->put($file, $image_base64, 's3');
                } catch (\Exception $e) {
                    Log::error($e);
                }
            }

            // saving data into users table
            $drivers = Drivers::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'mobile' => $request->mobile,
                'image' =>  'https://talabat-iti.s3.amazonaws.com/' . $file,
            ]);

            $token = $drivers->createToken(time())->plainTextToken;

            // return JSON API (driver$drivers access token)
            return response()->json([
                'name' => $drivers->name,
                'email' => $drivers->email,
                'image' => $drivers->image,
                'token' => $token,
            ]);
        }
    }

    public function driversLogin(Request $request)
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

            $drivers = Drivers::where('email', $request->email)->first();
            if (!$drivers || !Hash::check($request->password, $drivers->password)) {
                return response()->json([
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            } else {
                $driver = Drivers::where('email', $request->email)->first();
                $token = $driver->createToken(time())->plainTextToken;
                return response()->json([
                    'id' => $driver->id,
                    'name' => $driver->name,
                    'email' => $driver->email,
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

    public function getAllDrivers()
    {
        $drivers = Drivers::all(['id', 'name', 'mobile'])->toArray();

        return response()->json([
            'Available drivers' => $drivers,
        ]);
    }
}
