<?php

namespace Modules\Url\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Url\database\factories\UrlFactory;


class Url extends Model
{
    use HasFactory;

    protected $table = 'urls';
    protected $primaryKey = 'id';

    protected $fillable = [
        'long_url',
        'short_code',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return UrlFactory::new();
    }
}
