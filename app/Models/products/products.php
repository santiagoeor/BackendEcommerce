<?php

namespace App\Models\products;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class products extends Model
{
    use HasFactory;
    protected $primaryKey = 'pk_product';

    public function categories()
    {
        return $this->belongsTo(categories::class, 'fk_category', 'pk_category');
    }

    public function brands()
    {
        return $this->belongsTo(brands::class, 'fk_brand', 'pk_brand');
    }

    public function productVariants()
    {
        return $this->hasMany(product_variants::class, 'fk_variant', 'pk_variant');
    }

    public function productImages()
    {
        return $this->hasMany(product_images::class, 'fk_image', 'pk_image_product');
    }
}