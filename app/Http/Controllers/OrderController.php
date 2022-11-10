<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Orders;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrdersInfo;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function addOrders(Request $request)
    {

        // $users = User::with('orders')->where('id',Auth::id())->get();
        $restaurant = Restaurant::where('store_name',$request->input('restaurant_name'))->first();
        $order = Order::create([
            'user_id' => Auth::id(),
            'restaurant_id' => $restaurant->id,
            'addition_request' => $request->input('request'),
            'delivery_fee' => $request->input('delivery_fee'),
            'payment_type' => $request->input('payment_type'),
            'created_at' => now()->format('Y-m-d H:i:s'),
        ]);

        $orderId = $order->id;
        $products = $request->input('product_details');
        foreach($products as $product){
            DB::table('orders_products')->insert([
                'order_id' => $orderId,
                'product_id' => $product['product_id'],
                'product_count' => $product['product_count'],
            ]);
        }

        $orders = Order::with(['products' => function($q){
            $q -> select('products.id', 'name', 'price');
        }])->find($orderId);

        $numberOfProducts = count($orders->products);
        $nameData = [];
        $priceData = [];
        foreach($orders->products as $order){
            array_push($nameData, $order->name);
            array_push($priceData, $order->price);
        }

        $userName = Auth::user()->name;
        $userEmail = Auth::user()->email;
        Mail::to($userEmail)->send(new OrdersInfo($userName, $restaurant ,$numberOfProducts ,$orders ,$order ,$priceData ,$nameData));
            return response()->json([
                'data' => $orderId
            ]);
    }

    public function getOrderStatus($id)
    {
        $order = Order::where('id',$id)->first();

        return response()->json([
            'data' => $order->status
        ]);
    }

    public function setOrderStatus(Request $request, $id)
    {
        $order = Order::where('id',$id)->first();

        $newSatatus = $request->input('status');
        $order->status = $newSatatus;
        $order->save();

        return response()->json([
            'data' => $newSatatus
        ]);
    }
}
