<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ResponseHelper;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    use ResponseHelper;

    public function show()
    {
        $orders = Order::with('admin')->with('customer');
        if (Gate::allows('isAdmin')) $orders = $orders->where('admin', auth()->user()->id);
        else $orders = $orders->where('customer', auth()->user()->id);
        $orders = $orders->get();
        $orders = $orders->toArray();

        foreach($orders as $keyorder => $order) {
            $products = Cart::with('product')->where('order_id', $order['id'])->get()->toArray();
            $orders[$keyorder]['products'] = [];
            foreach($products as $product) {
                $orders[$keyorder]['products'][] = [
                    'sku' => $product['sku'],
                    'title' => $product['product']['title'],
                    'price' => $product['product']['price'],
                    'quantity' => $product['quantity']
                ];
            }
        }

        return $this->onSuccess($orders);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer' => ['integer', 'min:1'],
            'admin' => ['integer', 'min:1'],
            'products' => ['required', 'array']
        ]);
    
        if (count($validator->errors()) > 0) {
            return $this->onError(400, '', $validator->errors());
        }
        
        $status = (Gate::allows('isAdmin')) ? 'approved' : 'pending';
        $admin = $request->get('admin');
        $customer = $request->get('customer');
        $products = $request->get('products');
        
        if (Gate::allows('isAdmin')) {
            
            if (!$customer) return $this->onError(400, 'customer required');
            
            $checkcustomer = User::where('id', $customer)->where('role', 'user')->first();
            if(!$checkcustomer) return $this->onError(404, 'customer not found');
            
            $admin = auth()->user()->id;
        } else {
            if (!$admin) return $this->onError(400, 'admin required');
            
            $checkadmin = User::where('id', $admin)->where('role', 'admin')->first();
            if(!$checkadmin) return $this->onError(404, 'admin not found');
            
            $customer = auth()->user()->id;
        }

        if (!count($products)) {
            return $this->onError(400, 'products required');
        }

        foreach($products as $product) {
            $validator = Validator::make($product, [
                'sku' => ['required', 'integer', 'min:1'],
                'quantity' => ['required', 'integer', 'min:1'],
            ]);
    
            if (count($validator->errors()) > 0) {
                return $this->onError(400, '', $validator->errors());
            }
            
            $checkproduct = Product::where('sku', $product['sku'])->where('admin', $admin)->first();
            if (!$checkproduct) {
                return $this->onError(404, 'product not found');
            } else if ($checkproduct['quantity'] < $product['quantity']) {
                return $this->onError(416, 'product stock unavailable');
            }
        }

        $order = new Order;
        $order->status = $status;
        $order->admin = $admin;
        $order->customer = $customer;
        $order->save();

        $carts = [];
        foreach ($products as $product) {
            $carts[] = [
                'order_id' => $order->id,
                'sku' => $product['sku'],
                'quantity' => $product['quantity']
            ];
        }

        Cart::insert($carts);

        $response = [
            'id' => $order->id,
            'status' => $order->status,
            'admin' => $order->admin,
            'customer' => $order->customer,
            'products' =>  []
        ];

        $products = Cart::with('product')->where('order_id', $order->id)->get()->toArray();

        foreach ($products as $product) {
            $response['products'][] = [
                'sku' => $product['sku'],
                'title' => $product['product']['title'],
                'price' => $product['product']['price'],
                'quantity' => $product['quantity']
            ];
        }

        return $this->onSuccess($response);
    }

    public function update(Request $request, $id = null)
    {
        $validator = Validator::make($request->all(), [
            'status' => ['in:pending,approved']
        ]);
    
        if (count($validator->errors()) > 0) {
            return $this->onError(400, '', $validator->errors());
        }
        
        $admin = auth()->user()->id;
        $status = $request->get('status');
        
        $order = Order::with('customer')->with('cart')->where('id', $id)->where('admin', $admin)->first();
        
        if (!$order) {
            return $this->onError(404, 'order not found');
        }

        $order->update(['status' => $status]);
        $order = $order->whereHas('cart', function($query) {
            $query->with('product');
        });
        $order = $order->first();

        $response = [
            'id' => $order['id'],
            'status' => $order['status'],
            'admin' => $order['admin'],
            'customer' => $order['customer'],
            'products' =>  []
        ];

        $products = $order['cart'];

        foreach ($products as $product) {
            $response['products'][] = [
                'sku' => $product['sku'],
                'title' => $product['product']['title'],
                'price' => $product['product']['price'],
                'quantity' => $product['quantity']
            ];
        }

        return $this->onSuccess($response);
    }
}
