<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class Order extends Model
{
    protected $fillable = [
        'shop_id',
        'status',
        'mode',
        'total_price',
        'delivery_charge',
        'delivery_address',
        'latitude',
        'longitude',
        'items',
        'user_id',
        'discount_amount',
    ];

    protected $casts = [
        'total_price' => 'float',
        'delivery_charge' => 'float',
        'discount_amount' => 'float',
        'latitude' => 'float',
        'longitude' => 'float',
        'items' => 'array',
    ];

    public static function checkTable()
    {
        if (Schema::hasTable('orders')) {
            if (!Schema::hasColumn('orders', 'latitude')) {
                Schema::table('orders', function (Blueprint $table) {
                    $table->decimal('latitude', 10, 8)->nullable();
                    $table->decimal('longitude', 11, 8)->nullable();
                });
            }
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }
}
