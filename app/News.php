<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class News extends Model
{
    protected $table = 'news';
    protected $fillable = [
        'id_category',
        'title',
        'author',
        'slug',
        'content',
        'poster',
        'created_at'
    ];
    public $timestamps = false;
    protected $primaryKey = 'id_news';
    public function category()
    {
        return $this->hasOne(Category::class, 'id_category', 'id_category');
    }
}
