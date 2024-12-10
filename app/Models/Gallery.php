<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    public $table = 'gallery_items';

    protected $fillable = [
        'name',
        'description',
        'image_file_name',
        'image_file_name_header'
    ];
}
