<?php

namespace App\Services\Gallery;

use App\Enums\ImageFileType;

class ImageService
{
    private ?object $gdImage = null;
    private ImageFileType $imageFileType;
    private string $path;
    private int $width;
    private int $height;

    public function setImageFromFilePath(string $path)
    {
        switch (exif_imagetype($path)) {
            case IMAGETYPE_PNG:
                $this->imageFileType = ImageFileType::png;
                $this->gdImage = imagecreatefrompng($path);
                break;
            case IMAGETYPE_JPEG:
                $this->imageFileType = ImageFileType::jpeg;
                $this->gdImage = imagecreatefromjpeg($path);
                break;
            default:
                throw new \Exception('File of image neither jpeg nor png format');
        }
        $this->path = $path;
        $this->width = imagesx($this->gdImage);
        $this->height = imagesy($this->gdImage);

        return $this;
    }

    private function cropAlign(
        object $image,
        int $cropWidth,
        int $cropHeight,
        string $horizontalAlign = 'center',
        string $verticalAlign = 'middle'
    ) {
        $horizontalAlignPixels = $this->calculatePixelsForAlign($this->width, $cropWidth, $horizontalAlign);
        $verticalAlignPixels = $this->calculatePixelsForAlign($this->height, $cropHeight, $verticalAlign);
        return imageCrop($image, [
            'x' => $horizontalAlignPixels[0],
            'y' => $verticalAlignPixels[0],
            'width' => $horizontalAlignPixels[1],
            'height' => $verticalAlignPixels[1]
        ]);
    }

    private function calculatePixelsForAlign(int $imageSize, int $cropSize, string $align)
    {
        switch ($align) {
            case 'left':
            case 'top':
                return [0, min($cropSize, $imageSize)];
            case 'right':
            case 'bottom':
                return [max(0, $imageSize - $cropSize), min($cropSize, $imageSize)];
            case 'center':
            case 'middle':
                return [
                    max(0, floor(($imageSize / 2) - ($cropSize / 2))),
                    min($cropSize, $imageSize),
                ];
            default:
                return [0, $imageSize];
        }
    }

    public function cropCenter(int $cropPercent = 70)
    {
        $cropWidth = $this->width * $cropPercent / 100;
        $cropHeight = $this->height * $cropPercent / 100;
        $this->gdImage = $this->cropAlign($this->gdImage, $cropWidth, $cropHeight);

        return $this;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function save()
    {
        switch ($this->imageFileType) {
            case ImageFileType::png:
                imagepng($this->gdImage, $this->path);
                break;
            case ImageFileType::jpeg:
                imagejpeg($this->gdImage, $this->path);
                break;
            default:
                throw new \Exception('Unknown file type');
        }
    }
}
