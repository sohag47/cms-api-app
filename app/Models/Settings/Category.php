<?php

namespace App\Models\Settings;

use App\Enums\CategoryStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'categories';
    protected $guarded = [];

    public static function dropdown()
    {
        return self::query()
            ->select('id', 'name')
            ->where('status', CategoryStatus::ACTIVE)
            ->orderBy('order', 'DESC')
            ->get()
            ->map(fn($category) => [
                'value' => $category->id,
                'label' => $category->name,
            ]);
    }


    public function productTypes()
    {
        return $this->belongsToMany(ProductType::class, 'product_categories', 'category_id', 'product_type_id');
    }
}
