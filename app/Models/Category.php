<?php

namespace App\Models;

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


    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
