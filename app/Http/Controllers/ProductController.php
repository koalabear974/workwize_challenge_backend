<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the products.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    /**
     * Display the specified product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|string',
            'stock' => 'required|integer',
            'price' => 'required|numeric',
            'supplier_id' => 'required|exists:users,id',
        ]);

        $product = Product::create($request->all());
        return response()->json($product, 201);
    }

    /**
     * Update the specified product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'image' => 'nullable|string',
            'stock' => 'sometimes|required|integer',
            'price' => 'sometimes|required|numeric',
            'supplier_id' => 'sometimes|required|exists:users,id',
        ]);

        $product = Product::findOrFail($id);
        $this->authorize('update', $product);
        $product->update($request->all());
        return response()->json($product);
    }

    /**
     * Remove the specified product from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $this->authorize('delete', $product);
        $product->delete();
        return response()->json(null, 204);
    }

    /**
     * Display a listing of the supplier's own products.
     *
     * @return \Illuminate\Http\Response
     */
    public function myProducts(Request $request)
    {
        $products = Product::where('supplier_id', $request->user()->id)->get();
        return response()->json($products);
    }

}
