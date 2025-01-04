<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Model;

/**
 * @method static latest()
 * @method static find(string $id)
 * @property mixed|string $slug
 * @property false|mixed|resource|string|null $content
 * @property mixed $title
 * @property mixed|string|null $image
 */
class Blog extends Model
{
    protected $guarded = [];
=======
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Blog extends Model
{
    
    use HasFactory;

    protected $fillable = [
        'title',
        'blog_category',
        'slug',
        'content',
        'image',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

>>>>>>> 6d8083fa8e0dd2279f7db1cb40c7d7b423c086b7
}
