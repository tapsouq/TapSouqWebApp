<?php

namespace App\Http\Controllers\Admin\ImageCaching;

use Intervention\Image\Image;
use Intervention\Image\Filters\FilterInterface;

class Same implements FilterInterface
{
    public function applyFilter(Image $image)
    {
        return $image;
    }
}