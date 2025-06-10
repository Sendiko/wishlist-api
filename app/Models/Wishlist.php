<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;
    protected $table = 'wishlist';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'categoryId',
        'price',
        'priority',
        'description',
        'picture'
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'categoryId');
    }  
}
