<?php

namespace App\Services\Gallery;

/** Service for handling file uploaded file */
class FileService
{
    protected string $directory;
    protected string $fileName;

    /** Sets directory.
     * @param string $directory
     * @return void
     */
    public function setFileDirectory(string $directory): void
    {
        $this->directory = $directory;
    }

    /** Takes file name from file path and sets to object field.
     * @param string $fileName
     * @return void
     */
    public function setFileName(string $fileName): void
    {
        $fileNameItems = explode('/', $fileName);
        end($fileNameItems);
        $this->fileName = current($fileNameItems);
    }

    /** Gets directory of file.
     * @return string
     */
    public function getFileDirectory(): string
    {
        return $this->directory;
    }

    /** Gets file name.
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /** Renames extension of file, renames fileName value.
     * @param string $extension
     * @return void
     */
    public function renameFileExtension(string $extension): void
    {
        $fileNameItems = explode('.', $this->fileName);
        $fileNameItems[1] = $extension;
        $newFileName = implode('.', $fileNameItems);
        rename($this->directory . '/' . $this->fileName, $this->directory . '/' . $newFileName);
        $this->fileName = $newFileName;
    }

    /** Checks if extension of file is jpg or jpeg.
     * @return bool
     */
    public function hasJpgOrJpegExtension(): bool
    {
        $lowerFileExtension = strtolower(explode('.', $this->fileName)[1]);
        return $lowerFileExtension == 'jpg' || $lowerFileExtension == 'jpeg';
    }
}
