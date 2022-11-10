<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Use App\Http\Controllers\UserController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DriverController;
use App\Mail\OrdersInfo;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->get('/test', function () {
    return [
        'user' => auth()->user()
    ];
});

// -------------------------------- Users --------------------------------

Route::prefix('users')->middleware('auth:sanctum')->group(function () {
    // logout
    Route::post('/logout',             [UserController::class, 'logout']);

    // show user address
    Route::get('/user-address',             [UserController::class, 'userAddress']);

    // get user orders
    Route::get('/get-orders',             [UserController::class, 'getOrders']);


    Route::get('/user-details',             [UserController::class, 'userDetails']);
});

Route::prefix('users')->group(function () {
    // Registration
    Route::post('/register',          [UserController::class, 'register']);

    // login
    Route::post('/login',             [UserController::class, 'login'])->name('user.login');
});

// ------------------------------ Drivers ------------------------------------------

Route::prefix('drivers')->middleware('auth:sanctum')->group(function () {
    // get all drivers
    Route::get('/get-all-drivers' , [DriverController::class, 'getAllDrivers']);
});

Route::prefix('drivers')->group(function () {
    // Registration
    Route::post('/drivers-registeration' , [DriverController::class, 'driversRegisteration']);

    // login
    Route::post('/drivers-login' , [DriverController::class, 'driversLogin']);
});

// -------------------------------- Resturants --------------------------------

Route::prefix('restaurants')->middleware(['auth:sanctum' , 'CheckRestaurantToken:web-restaurant'])->group(function () {
    // update Resturants
    Route::patch('/update-restaurant',          [RestaurantController::class, 'register']);

    // get pended orders
    Route::get('/get-pended-orders',          [RestaurantController::class, 'getPendedOrders']);

    // get accepted orders
    Route::get('/get-accepted-orders',          [RestaurantController::class, 'getAcceptedOrders']);


    Route::get('/get-products',          [RestaurantController::class, 'getRestaurantProducts']);
});

Route::prefix('restaurants')->group(function () {
    // Add Resturants - register
    Route::post('/register',          [RestaurantController::class, 'restaurantRegister']);

    // Resturant login
    Route::post('/login',          [RestaurantController::class, 'restaurantLogin']);

    // Resturant logout
    Route::post('/logout',          [RestaurantController::class, 'restaurantLogout']);

    // show all Resturants
    Route::get('/get-all-restaurants',          [RestaurantController::class, 'getAllRestaurants']);

    // show Resturant
    Route::get('/get-restaurant/{id}',          [RestaurantController::class, 'getRestaurant']);

    // Resturant informations
    Route::get('/rest-info/{id}',          [RestaurantController::class, 'restInfo']);

    // Resturants search
    Route::get('/restaurant-search',          [RestaurantController::class, 'restaurantSearch']);
});

// -------------------------------------- Products ----------------------------------------
Route::prefix('products')->middleware(['auth:sanctum' ])->group(function () {
// -------------------------------------- CRUD ----------------------------------------
    // add product
    Route::post('/add-product',          [ProductController::class, 'addProducts']);

    // read product
    Route::get('/get-product/{id}',          [ProductController::class, 'getProduct']);

    // update product
    Route::patch('/update-product/{id}',          [ProductController::class, 'updateProducts']);

    // delete product
    Route::delete('/delete-product/{id}',          [ProductController::class, 'deleteProducts']);
});

Route::prefix('products')->middleware('auth:sanctum')->group(function () {
        // get product
        Route::get('/get-products/{id}',          [ProductController::class, 'getProducts']);

        // Products search
        Route::get('/product-search',          [ProductController::class, 'productSearch']);
    });

// -------------------------------------- Orders ---------------------------------------------

Route::prefix('orders')->middleware('auth:sanctum')->group(function () {

    // add order
    Route::post('/add-orders' , [OrderController::class, 'addOrders']);

    // get order status (pending - accepting - rejecting)
    Route::get('/get-order-status/{id}' , [OrderController::class, 'getOrderStatus']);

    // set order status (pending - accepting - rejecting)
    Route::post('/set-order-status/{id}' , [OrderController::class, 'setOrderStatus']);
});

// -------------------------------------- Socialite -----------------------------------------

// ------------------------------------------ Github ----------------------------------------

// redirect to github to sign up
Route::get('/auth/githup/redirect', function () {
    return Socialite::driver('github')->redirect();
})->name('auth.github');


// callback to index page
Route::get('/auth/github', function () {
    $githubUser = Socialite::driver('github')->user();
        $user = User::updateOrCreate([
            'email' => $githubUser->email,
        ],[
            'name' => $githubUser->name,
            'email' => $githubUser->email,
            'github_token' => $githubUser->token,
            'github_refresh_token' => $githubUser->refreshToken,
        ]);
        Auth::login($user);
        return redirect('/users');
    });

// ------------------------------------------ Google ----------------------------------------

// redirect to google to sign up
Route::get('/auth/google/redirect', function () {
    return Socialite::driver('google')->redirect();
})->name('auth.google');

// callback to index page
Route::get('/auth/google', function () {
    $googleUser = Socialite::driver('google')->user();
        $user = User::updateOrCreate([
            'email' => $googleUser->email,
        ],[
            'name' => $googleUser->name,
            'email' => $googleUser->email,
            'google_token' => $googleUser->token,
            'google_refresh_token' => $googleUser->refreshToken,
        ]);
        Auth::login($user);
        return redirect('/users');
});
