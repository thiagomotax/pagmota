<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\CartProducts;
use App\Cart;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        //synchronize cookies cart with db cart, like in register
        if (isset($_COOKIE["products_cart"])) {
            $cartJson = json_decode(utf8_encode($_COOKIE["products_cart"]));
            $cart = Cart::where('user_id', '=', auth()->user()->id)->get();

            foreach ($cartJson as $cartLocal) { //iterate
                CartProducts::create([
                    'product_id' => $cartLocal->product_id,
                    'cart_id' => $cart[0]->id,
                    'quantity' => $cartLocal->quantity,
                ]);
            }
            setcookie("products_cart", "", time() - 3600, '/');
        }
    }
}
