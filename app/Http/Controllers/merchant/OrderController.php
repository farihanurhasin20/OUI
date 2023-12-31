<?php

namespace App\Http\Controllers\merchant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::all();

        $formattedOrders = $orders->map(function ($order) {
            return [
                'order' => $order,
                // Add other fields as needed
            ];
        });

        return response()->json(['data' => $formattedOrders]);
    }

    public function show($id)
    {
        $order = Order::with('orderItems')->find($id);

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return response()->json([
            'data' => [
                'order' => $order,
                'invoice_id' => $order->invoice_id,
                'total_quantity' => $order->orderItems->sum('quantity'),
                'total_amount' => $order->total_price,
            ],
        ]);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
        'user_id' => 'nullable|exists:users,id',
        'order_items' => 'required|array',
        'order_items.*.product_id' => 'required|exists:products,id',
        'order_items.*.quantity' => 'required|integer|min:1',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
    
        $data = $validator->validated();
        $orderAmount = 0;
    
        foreach ($data['order_items'] as $item) {
            $product = Product::find($item['product_id']);
    
            $orderAmount += $product['price'] * $item['quantity'];
    
            // Check if there is enough stock before creating the order
            if ($product['qty'] < $item['quantity']) {
                return response()->json(['error' => 'Not enough stock for product ' . $product['name']], 400);
            }
            
        }
    
        if (isset($data['shipping_charge'])) {
            $totalAmount = $orderAmount + $data['shipping_charge'];
        } else {
            $totalAmount = $orderAmount;
        }
    
        // Generate a unique invoice ID
        $invoiceId = substr(str_replace('-', '', Str::uuid()->toString()), 0, 12);
    
    
        $data['invoice_id'] = $invoiceId;
        $data['total_price'] = $totalAmount;
        $data['order_amount'] = $orderAmount;
    
        $order = Order::create($data);
    
        foreach ($data['order_items'] as $item) {
            $product = Product::find($item['product_id']);
            $orderItem = new OrderItem();
            $orderItem->product_id = $product['id'];
            $orderItem->quantity = $item['quantity'];
            $orderItem->price = $product['price'];
            $product->qty -= $item['quantity'];
            $product->save();
            $order->orderItems()->save($orderItem);
        }


    
        return response()->json([
            'message' => 'Order created successfully',
            'data' => [
                'order' => $order,
                'total_quantity' => $order->orderItems->sum('quantity'),
            ],
        ], 201);
    }

    public function destroy($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Increase stock quantity for each product in the order
        foreach ($order->orderItems as $orderItem) {
            $product = $orderItem->product;
            $product->qty += $orderItem->quantity;
            $product->save();
        }

        // Delete the order and associated order items
        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }

}