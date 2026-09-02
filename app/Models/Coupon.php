<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'is_active',
        'max_uses',
        'used_count'
    ];

    protected $casts = [
        'value' => 'float',
        'min_order_amount' => 'float',
        'is_active' => 'boolean',
        'max_uses' => 'integer',
        'used_count' => 'integer'
    ];

    // Ensure table exists dynamically if database migration wasn't run
    public static function checkTable()
    {
        if (!Schema::hasTable('coupons')) {
            Schema::create('coupons', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->enum('type', ['flat', 'percent'])->default('flat');
                $table->decimal('value', 10, 2);
                $table->decimal('min_order_amount', 10, 2)->default(0);
                $table->boolean('is_active')->default(true);
                $table->integer('max_uses')->nullable();
                $table->integer('used_count')->default(0);
                $table->timestamps();
            });
        }
    }
}
