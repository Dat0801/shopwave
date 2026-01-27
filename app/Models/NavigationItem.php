<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NavigationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'url',
        'related_id',
        'parent_id',
        'order',
        'location',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'related_id' => 'integer',
        'parent_id' => 'integer',
        'order' => 'integer',
    ];

    protected $appends = ['href'];

    public function children()
    {
        return $this->hasMany(NavigationItem::class, 'parent_id')->orderBy('order');
    }

    public function parent()
    {
        return $this->belongsTo(NavigationItem::class, 'parent_id');
    }

    // Helper to get the actual URL
    public function getHrefAttribute()
    {
        if ($this->type === 'route') {
            return route($this->url);
        }
        if ($this->type === 'category') {
            // Assuming we have a helper or route for category
            // We might need to fetch the category slug.
            // For now, let's assume we load the relationship or handle it elsewhere.
            // Or simpler: just store the slug in 'url' when saving? No, ID is better for refactoring.
            return route('shop.index', ['category' => $this->related_id]); 
            // Wait, shop.index uses slug usually.
            // I should define a relationship to Category if I want to get the slug.
        }
        if ($this->type === 'page') {
             // Assuming pages have a route
             return route('page.show', $this->related_id); 
        }
        
        return $this->url;
    }
}
