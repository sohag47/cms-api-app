<?php

namespace App\Models;

use App\Models\Settings\Country;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'addresses';

    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'deleted_at',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function contactPerson()
    {
        return $this->belongsTo(ContactPerson::class);
    }
}
