<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Unit;
use App\Models\Group;
use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{

    public function __construct()
    {
       $this->authorizeResource(Product::class, 'product');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $products = Product::with('unit', 'category', 'group')->select();

            return DataTables::of($products)
                  ->addColumn('action', function ($product) {
                      return view('products.buttons')->with(['product' => $product]);
                  })->make(true);
        }

        return view('products.index');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $units = Unit::orderBy('name')->pluck('name', 'id');
        // $groups = Group::orderBy('name')->pluck('name', 'id');
        $categories = Category::orderBy('name')->pluck('name', 'id');

        return view('products.create', compact('units',  'categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:products|max:100',
            'code' => 'required',
            // 'group_id' => 'required|integer',
            'category_id' => 'required|integer',
            'unit_id' => 'required|integer',
            'price' => 'required|integer',
            'description' => 'nullable|max:200',
            'reorder_level' => 'required|numeric',
        ]);

        $product = Product::create($validatedData);


        if ($request->filled('images.0')) {
            $product->createProductImages($request);
        }

        return  redirect()->route('products.index')->with('success', 'Product Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {

        $units = Unit::orderBy('name')->pluck('name', 'id');
        // $groups = Group::orderBy('name')->pluck('name', 'id');
        $categories = Category::orderBy('name')->pluck('name', 'id');

        return view('products.edit', compact('product', 'units', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {

        $validatedData = $request->validate([
            'name' => 'required|max:100|'.Rule::unique('products')->ignore($product),
            'code' => 'required',
            // 'group_id' => 'required|integer',
            'category_id' => 'required|integer',
            'unit_id' => 'required|integer',
            'price' => 'required|integer',
            'description' => 'nullable|max:200',
            'reorder_level' => 'required|numeric',
        ]);

        $product->update($validatedData);


        if ($request->filled('images.0')) {
            $product->createProductImages($request);
        }

        return  back()->with('success', 'Product Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->productImages()->delete();
        $product->delete();

        return back()->with('success', 'Product Deleted');
    }
}
