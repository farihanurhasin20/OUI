<?php

namespace App\Http\Controllers\merchant;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MerchantAuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'mobile' => 'required|unique:users,mobile',
            'store_name'=>'required',
            // 'slug' => 'required|unique:merchants',
            'password' => 'required|min:6',
            'address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = User::create([
            'name' => $request->input('name'),
            'mobile' => $request->input('mobile'),
            'password' => Hash::make($request->input('password')),
            'address' => $request->input('address'),
        ]);

        $merchant= new Merchant();
        $merchant->user_id = $user->id;
        $merchant->organization_id = 1;
        $merchant->store_name =  $request->input('store_name');
        $merchant->slug =  $request->input('slug');
        $merchant->save();


        $token = $user->createToken('MyApp')->plainTextToken;

        return response()->json(['message' => 'User registered successfully', 'user' => $user, 'merchant' => $merchant, 'token' => $token], 201);
    }
}
