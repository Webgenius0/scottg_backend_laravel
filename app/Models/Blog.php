<?php

namespace App\Models;

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
}
