<?php

namespace App\Services\Gallery;

use App\Models\Gallery;
use App\Services\ResponseService;
use App\Traits\LoggerTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class GalleryService
{
    use LoggerTrait;

    public function updateItemByPositionNumber(
        int $positionNumber,
        ResponseService $responseService,
        ImageService $imageService,
        FileService $fileService,
        TinyPngService $tinyPngService,
        ?UploadedFile $file = null,
        ?string $itemName = null,
        ?string $itemDescription = null
    ) {
        $updateConfig = [];
        $galleryModels = Gallery::all();

        if (isset($galleryModels[$positionNumber - 1])) {
            $galleryModel = $galleryModels[$positionNumber - 1];
        } else {
            return $responseService->errorResponseWithKeyValueData(
                [
                'data' => [
                    'item position number' => $positionNumber
                ]
                ],
                'Position not found',
                404
            );
        }

        if (!is_null($file)) {
            $fileStoreFolder = storage_path() . '/app/public/gallery/img';

            $filePath = $file->store('public/gallery/img');
            $fileName = $this->getFileNameFromPath($filePath);

            $headerFileName = explode('.', $fileName)[0] . '_header.' . $file->getClientOriginalExtension();
            copy($fileStoreFolder . '/' . $fileName, $fileStoreFolder . '/' . $headerFileName);

            $imageService->setImageFromFilePath($fileStoreFolder . '/' . $fileName);

            if ($imageService->getWidth() > 320) {
                $fileService->setFileDirectory($fileStoreFolder);
                $fileService->setFileName($headerFileName);

                if (!$fileService->hasJpgOrJpegExtension()) {

                    /** @var $errorMessage */
                    $extension = $tinyPngService->convertToJpeg(
                        $fileService->getFileDirectory(),
                        $fileService->getFileName(),
                        $errorMessage
                    );
                    if ($extension !== false) {
                        $fileService->renameFileExtension($extension);
                    } else {
                        $this->deleteAlreadyDownloadedFiles([$fileName, $headerFileName]);

                        return $responseService->errorResponseWithKeyValueData(
                            [
                            'data' => [
                                'item position' => $positionNumber,
                                'item id' => $galleryModel->id
                            ]
                            ],
                            'Tiny PNG service responsed: ' . $errorMessage,
                            409
                        );
                    }
                }

                /** @var $errorMessage */
                $result = $tinyPngService->fitPhotoByThumbAlgorithm(
                    $fileService->getFileDirectory(),
                    $fileService->getFileName(),
                    320,
                    320,
                    $errorMessage
                );
                if ($result) {
                    if ($galleryModel->image_file_name != 'No_Image_Available.jpg') {
                        Storage::disk('public')
                            ->delete('gallery/img/' . $galleryModel->image_file_name);
                    }
                    if ($galleryModel->image_file_name_header != 'No_Image_Available.jpg') {
                        Storage::disk('public')
                            ->delete('gallery/img/' . $galleryModel->image_file_name_header);
                    }

                    $updateConfig['image_file_name_header'] = $fileService->getFileName();
                } else {
                    $this->deleteAlreadyDownloadedFiles([$fileName, $headerFileName]);

                    return $responseService->errorResponseWithKeyValueData(
                        [
                        'data' => [
                            'item position' => $positionNumber,
                            'item id' => $galleryModel->id
                        ]
                        ],
                        'Tiny PNG service responsed: ' . $errorMessage,
                        409
                    );
                }
            } else {
                $updateConfig['image_file_name_header'] = $headerFileName;
            }

            $updateConfig['image_file_name'] = $fileName;
        }

        if (!is_null($itemName)) {
            $updateConfig['name'] = $itemName;
        }
        if (!is_null($itemDescription)) {
            $updateConfig['description'] = $itemDescription;
        }

        if (sizeof($updateConfig) > 0) {
            $galleryModel->update($updateConfig);

            return $responseService->successResponseWithKeyValueData(
                [
                'data' => [
                    'item position' => $positionNumber,
                    'item id' => $galleryModel->id
                ]
                ],
                'Gallery item updated'
            );
        } else {
            return $responseService->errorResponseWithKeyValueData(
                [
                'data' => [
                    'item position' => $positionNumber,
                    'item id' => $galleryModel->id
                ]
                ],
                'No data for update received',
                400
            );
        }
    }

    public function clearItemByPositionNumber(int $positionNumber, ResponseService $responseService)
    {
        $galleryModels = Gallery::all();

        if (isset($galleryModels[$positionNumber - 1])) {
            $galleryModel = $galleryModels[$positionNumber - 1];
        } else {
            return $responseService->errorResponseWithKeyValueData(
                [
                'data' => [
                    'item position number' => $positionNumber
                ]
                ],
                'Position not found',
                404
            );
        }

        if (
            $galleryModel->image_file_name != 'No_Image_Available.jpg' &&
            $galleryModel->image_file_name_header != 'No_Image_Available.jpg'
        ) {
            Storage::disk('public')->delete('gallery/img/' . $galleryModel->image_file_name);
            Storage::disk('public')->delete('gallery/img/' . $galleryModel->image_file_name_header);

            $galleryModel->update([
                'name' => '',
                'description' => '',
                'image_file_name' => 'No_Image_Available.jpg',
                'image_file_name_header' => 'No_Image_Available.jpg',
            ]);

            return $responseService->successResponseWithKeyValueData(
                [
                'data' => [
                    'item position number' => $positionNumber,
                    'item id' => $galleryModel->id
                ]
                ],
                'Gallery item cleared'
            );
        } else {
            return $responseService->errorResponseWithKeyValueData(
                [
                'data' => [
                    'item position number' => $positionNumber,
                    'item id' => $galleryModel->id
                ]
                ],
                'Gallery item image has not deleted. No image to delete',
                409
            );
        }
    }

    public function addItem(ResponseService $responseService)
    {
        $galleryModel = Gallery::create([
            'name' => '',
            'description' => '',
            'image_file_name' => 'No_Image_Available.jpg',
            'image_file_name_header' => 'No_Image_Available.jpg'
        ]);

        return $responseService->successResponseWithKeyValueData(
            [
            'data' => [
                'item id' => $galleryModel->id
            ]
            ],
            'New gallery item has created'
        );
    }

    public function deleteItemByPositionNumber(int $positionNumber, ResponseService $responseService)
    {
        $galleryModels = Gallery::all();

        if (isset($galleryModels[$positionNumber - 1])) {
            $galleryModel = $galleryModels[$positionNumber - 1];

            if ($galleryModel->image_file_name != 'No_Image_Available.jpg') {
                Storage::disk('public')
                    ->delete('gallery/img/' . $galleryModel->image_file_name);
            }
            if ($galleryModel->image_file_name_header != 'No_Image_Available.jpg') {
                Storage::disk('public')
                    ->delete('gallery/img/' . $galleryModel->image_file_name_header);
            }

            $galleryItemId = $galleryModel->id;
            $galleryModel->delete();

            return $responseService->successResponseWithKeyValueData(
                [
                'data' => [
                    'item position number' => $positionNumber,
                    'item id' => $galleryItemId
                ]
                ],
                'Gallery item has deleted'
            );
        } else {
            return $responseService->errorResponseWithKeyValueData(
                [
                'data' => [
                    'item position number' => $positionNumber
                ]
                ],
                'Position not found',
                404
            );
        }
    }

    public function getGalleryPage()
    {
        $this->writeInfoLog('Opened gallery page', [], isAllowedSendToTlg: true);
        return view('gallery.index', ['galleryItems' => Gallery::all()]);
    }

    private function deleteAlreadyDownloadedFiles(array $fileNames)
    {
        foreach ($fileNames as $fileName) {
            Storage::disk('public')->delete('gallery/img/' . $fileName);
        }
    }

    private function getFileNameFromPath(string $path)
    {
        $arrayOfFolders = explode('/', $path);
        end($arrayOfFolders);
        return current($arrayOfFolders);
    }
}
