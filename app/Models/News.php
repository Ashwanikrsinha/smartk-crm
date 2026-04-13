<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    protected $dates = ['published_at'];

    protected $casts = [
        'published_at' => 'datetime',
    ];
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function images()
    {
        return $this->hasMany(NewsImage::class, 'news_id');
    }

    public function image()
    {
        return $this->hasOne(NewsImage::class, 'news_id');
    }

    public function scopeActive($query)
    {

        return $query->where('is_active', 1);
    }

    public function scopeLatestFour($query)
    {

        return $query->select('id', 'title', 'published_at', 'event_id')
            ->active()
            ->orderBy('id', 'DESC')
            ->limit(4);
    }

    public function createNewsImages($request)
    {

        foreach ($request->images as $image) {
            $this->images()->create([
                'filename' => $image,
            ]);
        }
    }
}
