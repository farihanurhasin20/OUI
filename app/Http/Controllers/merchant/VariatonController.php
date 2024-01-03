<?php

namespace App\Http\Controllers\merchant;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Unit;
use App\Models\Category;
use Illuminate\Http\Request;


class VariatonController extends Controller
{
    public function color_index()
    {
        $colors = Color::all();

        return response()->json($colors, 200);
    }

    public function color_show($id)
    {
        $color = Color::find($id);

        if (!$color) {
            return response()->json(['message' => 'Color not found'], 404);
        }

        return response()->json(['color' => $color], 200);
    }

    public function unit_index()
    {
        $units = Unit::all();

        return response()->json($units, 200);
    }

    public function unit_show($id)
    {
        $unit = Unit::find($id);

        if (!$unit) {
            return response()->json(['message' => 'Unit not found'], 404);
        }

        return response()->json(['unit' => $unit], 200);
    }

    public function category_index()
    {
        $category = Category::all();

        return response()->json($category, 200);
    }

    public function category_show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return response()->json(['category' => $category], 200);
    }
}
