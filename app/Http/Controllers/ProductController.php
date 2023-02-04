<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ResponseHelper;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use ResponseHelper;

    public function show()
    {
        $products = Product::with('admin');
        if (Gate::allows('isAdmin')) $products = $products->where('admin', auth()->user()->id);
        $products = $products->get()->toArray();

        return $this->onSuccess($products);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
        ]);

        if (count($validator->errors()) > 0) {
            return $this->onError(400, '', $validator->errors());
        }

        $admin = auth()->user()->id;
        $title = $request->get('title');
        $price = $request->get('price');
        
        $product = new Product;
        $product->admin = $admin;
        $product->title = $title;
        $product->price = $price;
        $product->save();

        return $this->onSuccess($product);
    }

    public function update(Request $request, $id = null)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['string', 'max:255'],
            'price' => ['integer', 'min:0'],
        ]);

        if (count($validator->errors()) > 0) {
            return $this->onError(400, '', $validator->errors());
        }

        $admin = auth()->user()->id;
        $title = $request->get('title');
        $price = $request->get('price');
        
        $product = Product::where('sku', $id)->where('admin', $admin)->first();

        if (!$product) {
            return $this->onError(404, 'product not found');
        }

        if ($title) $product->title = $title;
        if ($price) $product->price = (int) $price;
        $product->save();

        return $this->onSuccess($product);
    }

    public function destroy($id = null)
    {
        $admin = auth()->user()->id;

        $product = Product::where('sku', $id)->where('admin', $admin)->first();
        if (!$product) {
            return $this->onError(404, 'product not found');
        }

        $product->delete();
        return $this->onSuccess([], '', 204);
    }
}
