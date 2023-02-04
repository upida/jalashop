<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ResponseHelper;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class PurchaseOrderController extends Controller
{
    use ResponseHelper;

    public function show()
    {
        $purchase_orders = PurchaseOrder::where('admin', auth()->user()->id)->get()->toArray();

        foreach($purchase_orders as $key_po => $po) {

            $detail = PurchaseOrderDetail::with('product')->where('purchase_id', $po['number'])->get()->toArray();

            $purchase_orders[$key_po]['detail'] = array_map(function ($data) {
                return [
                    'sku' => $data['sku'],
                    'title' => $data['product']['title'],
                    'price' => $data['product']['price'],
                    'purchase_quantity' => $data['quantity'],
                    'product_quantity' => $data['product']['quantity']
                ];
            }, $detail);
        }

        return $this->onSuccess($purchase_orders);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data' => ['required', 'array']
        ]);
    
        if (count($validator->errors()) > 0) {
            return $this->onError(400, '', $validator->errors());
        }

        $data = $request->get('data');

        if (!count($data)) {
            return $this->onError(400, 'data required');
        }

        $admin = auth()->user()->id;

        foreach($data as $detail) {
            $validator = Validator::make($detail, [
                'sku' => ['required', 'integer', 'min:1'],
                'quantity' => ['required', 'integer', 'min:1'],
            ]);
    
            if (count($validator->errors()) > 0) {
                return $this->onError(400, '', $validator->errors());
            }
            
            $checkownerproduct = Product::where('sku', $detail['sku'])->where('admin', $admin)->first();
            if (!$checkownerproduct) {
                return $this->onError(404, 'product not found');
            }
        }

        $po = new PurchaseOrder;
        $po->admin = $admin;
        $po->save();

        $details = [];
        foreach ($data as $detail) {
            $details[] = [
                'purchase_id' => $po->number,
                'sku' => $detail['sku'],
                'quantity' => $detail['quantity']
            ];
        }

        PurchaseOrderDetail::insert($details);

        $response = [
            'number' => $po->number,
            'products' =>  []
        ];

        $products = PurchaseOrderDetail::with('product')->where('purchase_id', $po->number)->get()->toArray();

        foreach ($products as $product) {
            $response['products'][] = [
                'sku' => $product['sku'],
                'title' => $product['product']['title'],
                'price' => $product['product']['price'],
                'purchase_quantity' => $product['quantity'],
                'product_quantity' => $product['product']['quantity']
            ];
        }

        return $this->onSuccess($response);
    }
}
