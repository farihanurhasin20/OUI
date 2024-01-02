<?php

namespace App\Http\Controllers\merchant;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{

    public function index()
{
    $user = Auth::user();
    
    $products = Product::where('user_id', $user->id)->get();
    
    return response()->json(['data' => $products], 200);
}


    public function show($id)
{
    $product = Product::find($id);

    if (!$product) {
        return response()->json(['message' => 'Product not found'], 404);
    }

    return response()->json(['data' => $product], 200);
}

    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'title' => 'required|string',
        'barcode' => 'required|string',
        'qty' => 'nullable|string',
        'size' => 'nullable|string',
        'type' => 'nullable|string',
        'price' => 'required|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        'unit_id' => 'nullable|integer',
        'color_id' => 'nullable|integer',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 400);
    }

    $productData = $request->except('image');

    // dd($productData);

    // Process image if provided
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();

        $image->move(public_path('storage/images'), $imageName);

        $productData['image'] = "storage/images/{$imageName}";

    }

    $userId = Auth::id();
    $productData['user_id'] = $userId;
    

    $product = Product::create($productData);


    return response()->json(['message' => 'Product created successfully', 'product' => $product], 201);
}

public function update(Request $request, $id)
{
    $product=Product::find($id);
    if (!$product) {
        return response()->json(['message' => 'Product not found'], 404);
    }

    $validator = Validator::make($request->all(), [
        'title' => 'required|string',
        'barcode' => 'required|string',
        'qty' => 'nullable|string',
        'size' => 'nullable|string',
        'type' => 'nullable|string',
        'price' => 'required|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        'unit_id' => 'nullable|integer',
        'color_id' => 'nullable|integer',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 400);
    }
    
    $productData = $request->all();

    // Process image if provided
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();

        $image->move(public_path('storage/images'), $imageName);

        $productData['image'] = "storage/images/{$imageName}";

    }


    $product->update($productData);
    


    return response()->json(['message' => 'Product updated successfully', 'product' => $product], 200);
}

public function destroy($id)
{
    $product = Product::find($id);

    if (!$product) {
        return response()->json(['message' => 'Product not found'], 404);
    }

    // Delete associated image file if it exists
    if ($product->image) {
        Storage::delete('public/images/' . $product->image);
    }

    $product->delete();

    return response()->json(['message' => 'Product deleted successfully'], 200);
}

}
