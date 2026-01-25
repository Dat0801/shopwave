<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image_path',
        'mobile_image_path',
        'link',
        'duration',
        'description',
        'button_text',
        'start_date',
        'end_date',
        'placement',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'duration' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];
}
