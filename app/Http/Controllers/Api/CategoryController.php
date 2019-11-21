<?php

namespace App\Http\Controllers\Api;

use App\Category;
//use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function getCategories()
    {
        return Category::select('id as value', 'name as label')->orderBy('value')->get();
//        return response(['categories' => CategoryResource::collection(Category::all())]);
    }

    public function index()
    {
        return response(['categories' => $this->getCategories()]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:categories|max:255',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'user_id' => auth()->user()->id,
        ]);

        return response(['category' => ['label' => $category->name, 'value' => $category->id]]);
    }
}
