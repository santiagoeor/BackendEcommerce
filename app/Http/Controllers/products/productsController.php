<?php

namespace App\Http\Controllers\products;

use App\Traits\SavePdfImageTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use App\Models\products\products;
use App\Models\products\product_images;

class productsController extends Controller
{
    use SavePdfImageTrait;

    public function index(Request $request)
    {

        $products = products::query()
            ->with('categories', 'brands', 'productVariants', 'productImages')
            ->orderBy('name_product', 'asc')
            ->paginate(50);

        return response()->json($products);
    }

    public function create(Request $request)
    {
        try {
            $request->validate([
                'name_product' => 'required|unique:products|min:6|max:60',
                'description_product' => 'required|min:10|max:180',
                'price' => 'required|integer',
                'stock' => 'required|integer',
                'amount' => 'required|integer',
                'product_status' => 'required|integer',
                'imagen_prod' => 'required|image|max:10240',
                'fk_category' => 'required|integer',
                'fk_brand' => 'required|integer',
                'fk_variant' => 'required|integer',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $url = 'storage/products';
        $image = $request->file('imagen_prod');

        $imageUrl = $this->savePdfImage($url, $image);

        $productImage = new product_images();
        $productImage->image_url = $imageUrl;
        $productImage->description = $request->input('description_product');
        $productImage->save();

        $products = new products();
        $products->name_product = $request->input('name_product');
        $products->description_product = $request->input('description_product');
        $products->price = $request->input('price');
        $products->stock = $request->input('stock');
        $products->amount = $request->input('amount');
        $products->product_status = $request->input('product_status');
        $products->fk_category = $request->input('fk_category');
        $products->fk_brand = $request->input('fk_brand');
        $products->fk_image = $productImage->pk_image_product;
        $products->fk_variant = $request->input('fk_variant');
        $products->save();

        return response()->json([
            'ok' => 'Product created successfully',
            'url' => $imageUrl
        ], 201);
    }
}