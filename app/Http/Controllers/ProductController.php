<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function addProducts(Request $request)
    {
            $restaurant = auth('sanctum')->user();
            $restaurantId = $restaurant->id;
            // convert base64 image to string
            $name = time() . '.' . explode('/', explode(':', substr($request->image, 0, strpos($request->image, ';')))[1])[1];


            if ($request->image) {
                $folderPath = "products/";
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

            $product = Product::create([
                'restaurant_id' => $restaurantId,
                'name' => $request->name,
                'price' => $request->price,
                // 'descreption' => $request->descreption,
                'image' => 'https://talabat-iti.s3.amazonaws.com/' . $file,
                'created_at' => now()->format('Y-m-d H:i:s'),
            ]);

            return response()->json([
                'name' => $product->name,
                'price' => $product->adress,
                'descreption' => $product->descreption,
                'image' => $request->image,
            ]);
    }

    public function updateProducts(Request $request, $id)
    {
        $product = Product::findorFail($id);

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
        ]);

        return response()->json([
            'name' => $product->name,
            'price' => $request->price,
        ]);
    }

    public function deleteProducts(Request $request, $id)
    {
        $product = Product::findorFail($id);

        $product->delete();
    }

    public function getProducts($id)
    {
        $products = Product::where('restaurant_id', $id)->get(['id', 'name', 'price', 'image']);
        return response()->json([
            'data' => $products
        ]);
    }

    public function getProduct($id)
    {
        $products = Product::where('id', $id)->first(['id', 'name', 'price', 'image']);
        return response()->json([
            'data' => $products
        ]);
    }

    public function productSearch(Request $request)
    {
        $search = $request->keyword;
        $products = Product::where('name', 'LIKE', '%'.$search.'%')->get();
        if(!!count($products)){
            return response()->json([
                'data' => $products,
            ]);
        }else{
            return response()->json([
                'data' => 'No such a products in our database!'
            ]);
        }
    }
}
