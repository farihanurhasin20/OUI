<?php

namespace App\Http\Controllers\merchant;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MerchantAuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'mobile' => 'required|unique:users,mobile',
            'store'=>'required',
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
        $merchant->store_name =  $request->input('store');
        $merchant->slug =  $request->input('store');
        $merchant->save();


        $token = $user->createToken('MyApp')->plainTextToken;

        return response()->json(['message' => 'User registered successfully', 'user' => $user, 'merchant' => $merchant, 'token' => $token], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        if (Auth::attempt(['mobile' => $request->input('mobile'), 'password' => $request->input('password')])) {
            $user = Auth::user();
            $token = $user->createToken('MyApp')->plainTextToken;

            return response()->json(['token' => $token, 'user_id' => $user->id, 'name' => $user->name, 'message' => 'Login successful'], 200);
        } else {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout successful']);
    }
}
