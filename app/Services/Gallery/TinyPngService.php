<?php

namespace App\Services\Gallery;

class TinyPngService
{
    /** Resizes and handles image file using thumb algorithm.
     *
     * @param string $fileDirectory
     * @param string $fileName
     * @param int $width
     * @param int $height
     * @param ?string &$errorMessage
     * @return void|bool
     */
    public function fitPhotoByThumbAlgorithm(
        string $fileDirectory,
        string $fileName,
        int $width = 70,
        int $height = 70,
        ?string &$errorMessage = null
    ): bool {
        try {
            \Tinify\setKey(config('tiny_png.key'));
            $source = \Tinify\fromFile($fileDirectory . '/' . $fileName);
            $resized = $source->resize(array(
                "method" => "thumb",
                "width" => $width,
                "height" => $height
            ));
            $resized->toFile($fileDirectory . '/' . $fileName);
            return true;
        } catch (\Exception $exception) {
            $errorMessage = $exception->getMessage();
            return false;
        }
    }

    /** Converts format of image file to jpeg.
     *
     * @param string $fileDirectory
     * @param string $fileName
     * @param ?string &$errorMessage
     * @return string|bool
     */
    public function convertToJpeg(string $fileDirectory, string $fileName, ?string &$errorMessage = null): string|bool
    {
        try {
            \Tinify\setKey(config('tiny_png.key'));
            $source = \Tinify\fromFile($fileDirectory . '/' . $fileName);
            $converted = $source->convert(array("type" => ["image/jpeg"]));
            $extension = $converted->result()->extension();
            $converted->toFile($fileDirectory . '/' . $fileName);

            return $extension;
        } catch (\Exception $exception) {
            $errorMessage = $exception->getMessage();
            return false;
        }
    }
}
