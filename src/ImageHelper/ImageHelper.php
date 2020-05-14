<?php

namespace BackpackHelpers\ImageHelper;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

/**
 * Trait ImageHelper
 * @property array $attributes
 */
trait ImageHelper {

    /**
     * @param string Attribute name $attribute
     * @param string | null base64string $value
     * @param string $folder
     * @param string $disk
     * @return void
     */
    public function handleStoreImageAttribute(string $attribute, $value, string $folder, string $disk) : void
    {
        if (is_null($value) && !is_null($this->{$attribute})) {
            $this->deleteFileFromDisk($this->{$attribute}, $disk);
            $this->attributes[$attribute] = null;
        }

        if (Str::of($value)->startsWith('data:image')) {
            $image = Image::make($value);
            preg_match("/^data:image\/(.*);base64/i", $value, $match);
            $ext = explode('/', substr($value, 0, strpos($value, ';')))[1];
            $filename = md5($value . time()) . '.' . $ext;
            Storage::disk($disk)->put($folder . '/' . $filename, $image->stream());
            $this->attributes[$attribute] = "$folder/$filename";
        }
    }

    /**
     * @param string $attribute
     * @param $files
     * @param string $folder
     * @param string $disk
     */
    public function handleStoreUploadMultipleField(string $attribute, $files, string $folder, string $disk): void
    {
        $removeFiles = request()->get('clear_gallery', false);

        $currentFiles = isset($this->attributes[$attribute]) ? json_decode(
            $this->attributes[$attribute], true
        ) : [];

        if ($removeFiles) {
            $filesLeft = array_diff($currentFiles, $removeFiles);
            $this->attributes['gallery'] = json_encode($filesLeft);
            foreach ($removeFiles as $removeFile) {
                $this->deleteFileFromDisk($removeFile, $disk);
            }
        }

        if (is_array($files) && class_basename($files[0]) == 'UploadedFile') {
            /* @var UploadedFile $file */
            foreach ($files as $file) {
                $currentFiles[] = $file->store($folder, $disk);
            }
            $this->attributes[$attribute] = json_encode($currentFiles);
        }
    }

    /**
     * @param string $filePath
     * @param string $disk
     */
    private function deleteFileFromDisk(string $filePath, string $disk): void {
        Storage::disk($disk)->delete($filePath);
    }

}