<?php

namespace App\Http\Controllers\merchant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)->get();
    

        $formattedOrders = $orders->map(function ($order) {
            return [
                'order' => $order,
                'invoice_id' => $order->invoice_id,
                'total_quantity' => $order->orderItems->sum('quantity'),
                'total_amount' => $order->total_price,
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
                'images' => $order->orderItems->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'title' => $item->product->title,
                        'image' => $item->product->image, // Adjust this based on your actual image field
                    ];
                }),
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
        'order_items.*.quantity' => 'nullable|integer|min:1',
        'order_items.*.weight' => 'nullable|integer|min:1',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        

        $data = $validator->validated();
        $orderAmount = 0;

        $user = Auth::user();
        $data['user_id'] = $user->id;
        // dd($data);
        foreach ($data['order_items'] as $item) {
            $product = Product::find($item['product_id']);
    
            $orderAmount += $product['price'] * $item['quantity'];
    
            // Check if there is enough stock before creating the order
            // if ($product['qty'] < $item['quantity']) {
            //     return response()->json(['error' => 'Not enough stock for product ' . $product['name']], 400);
            // }
            
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
            $orderItem->weight = $item['weight'];
            $orderItem->price = $product['price'];
            // $product->qty -= $item['quantity'];
            // $product->save();
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

    public function getOrderHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'required|integer|min:1900',
            'month' => 'nullable|integer|between:1,12',
            'day' => 'nullable|integer|between:1,31',
            ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }
        $year = $request->input('year');
        $month = $request->input('month');
        $day = $request->input('day');

        $query = Order::whereYear('created_at', $year);

        if (!is_null($month)) {
            $query->whereMonth('created_at', $month);
        }

        if (!is_null($day)) {
            $query->whereDay('created_at', $day);
        }

        $orders = $query->orderBy('created_at')->get();

        // Calculate total quantity, total price, and total orders
        $totalQuantity = $orders->flatMap(function ($order) {
            return $order->orderItems->pluck('quantity');
        })->sum();

        $totalPrice = $orders->sum('total_price');
        $totalOrders = $orders->count();

        return response()->json([
            'data' => $orders,
            'total_quantity' => $totalQuantity,
            'total_price' => $totalPrice,
            'total_orders' => $totalOrders,
        ]);
    }
    public function getTodaysData(){
        $today = Carbon::now()->toDateString();
        $user = Auth::user();
        
        $orders = Order::whereDate('created_at', $today)
                    ->where('user_id', $user->id)
                    ->get();

        $totalQuantity = $orders->flatMap(function ($order) {
            return $order->orderItems->pluck('quantity');
        })->sum();

        $totalPrice = $orders->sum('total_price');
        $totalOrders = $orders->count();

        return response()->json([
            'data' => $orders,
            'total_quantity' => $totalQuantity,
            'total_price' => $totalPrice,
            'total_orders' => $totalOrders,
        ]);
    }

}
