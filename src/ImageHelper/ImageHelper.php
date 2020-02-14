<?php

namespace BackpackHelpers\ImageHelper;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

/**
 * Trait ImageHelper
 * @property array $attributes
 * @package BackpackHelpers\ImageHelper
 */
trait ImageHelper {

    /**
     * @param string Attribute name $attribute
     * @param string base64string $value
     * @param string $folder
     * @param string $disk
     * @return void
     */
    public function handleStoreImageAttribute(string $attribute, string $value = null, string $folder, string $disk)
    {
        if ($value == null) {
            Storage::disk($disk)->delete($this->{$attribute});
            $this->attributes[$attribute] = null;
        }

        if (starts_with($value, 'data:image')) {
            $image = Image::make($value);
            preg_match("/^data:image\/(.*);base64/i", $value, $match);
            $ext = explode('/', substr($value, 0, strpos($value, ';')))[1];
            $filename = md5($value . time()) . '.' . $ext;
            Storage::disk($disk)->put($folder . '/' . $filename, $image->stream());
            $this->attributes[$attribute] = "$folder/$filename";
        }

    }

}