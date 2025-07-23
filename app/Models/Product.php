<?php

namespace App\Models;

use App\Models\Settings\Brand;
use App\Models\Settings\Country;
use App\Models\Settings\Currency;
use App\Models\Settings\ProductType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'products';
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'deleted_at',
    ];


    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
    public function product_type()
    {
        return $this->belongsTo(ProductType::class);
    }
    public function origin()
    {
        return $this->belongsTo(Country::class);
    }
    public function manufacture()
    {
        return $this->belongsTo(Country::class);
    }
}
