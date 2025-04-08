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

    public function update(Request $request, $id = 0)
    {
        try {
            $request->validate([
                'name_product' => 'required|unique:products|min:6|max:60',
                'description_product' => 'required|min:10|max:180',
                'price' => 'required|integer',
                'stock' => 'required|integer',
                'amount' => 'required|integer',
                'product_status' => 'required|integer',
                'imagen_prod' => 'image|max:10240',
                'fk_category' => 'required|integer',
                'fk_brand' => 'required|integer',
                'fk_variant' => 'required|integer',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $products = products::find($id);
        if (is_null($products)) {
            return response()->json([
                'error' => 'could not be performed correctly with this id ' . $id . ''
            ], 404);
        }

        $imagen_prod = $request->hasFile('imagen_prod');

        if ($imagen_prod) {

            $productImage = product_images::find($products->fk_image);
            if (is_null($productImage)) {
                return response()->json([
                    'error' => 'could not be performed correctly with this id ' . $id . ''
                ], 404);
            }

            $urlImagenDelete = $productImage->image_url;
            $this->deleteImage($urlImagenDelete);

            $url = 'storage/products';
            $image = $request->file('imagen_prod');

            $imageUrl = $this->savePdfImage($url, $image);

            $productImage->image_url = $imageUrl;
            $productImage->description = $request->input('description_product');
            $productImage->save();
        }

        $products->name_product = $request->input('name_product');
        $products->description_product = $request->input('description_product');
        $products->price = $request->input('price');
        $products->stock = $request->input('stock');
        $products->amount = $request->input('amount');
        $products->product_status = $request->input('product_status');
        $products->fk_category = $request->input('fk_category');
        $products->fk_brand = $request->input('fk_brand');
        $products->fk_variant = $request->input('fk_variant');
        $products->save();

        return response()->json([
            'ok' => 'Updated product successfully'
        ], 201);
    }

    public function destroy(Request $request, int $id = 0)
    {
        if ($id <= 0) {
            return response()->json([
                'error' => 'You must send the product ID'
            ], 404);
        }

        $products = products::find($id);
        if (is_null($products)) {
            return response()->json([
                'error' => 'could not be performed correctly with this id ' . $id . ''
            ], 404);
        }

        $productImage = product_images::find($products->fk_image);
        if (is_null($productImage)) {
            return response()->json([
                'error' => 'could not be performed correctly with this id ' . $id . ''
            ], 404);
        }

        $urlImagenDelete = $productImage->image_url;
        $this->deleteImage($urlImagenDelete);

        $products->delete();
        $productImage->delete();

        return response()->json([
            'ok' => 'product successfully removed'
        ], 204);
    }
}