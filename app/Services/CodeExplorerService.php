<?php

namespace App\Services;

use App\Services\Helpers\ErrorMessage;
use App\Services\Helpers\ShortStr;
use App\Traits\LoggerTrait;
use Illuminate\Http\Request;

use function PHPUnit\TestFixture\returnsBoolOrIntOrNull;

class CodeExplorerService
{
    use LoggerTrait;

    private array $forbidden = [];

    public function __construct()
    {
        $this->setForbidden(config('code_explorer.forbidden'));
    }

    public function setForbidden(array $forbidden)
    {
        $this->forbidden = $forbidden;
    }

    public function getRootDir(int $levels): string
    {
        $foldersOfCatalog = explode('/', __DIR__);
        for ($i = 1; $i <= $levels; $i++) {
            end($foldersOfCatalog);
            unset($foldersOfCatalog[key($foldersOfCatalog)]);
        }

        return implode('/', $foldersOfCatalog);
    }

    public function explore($dir)
    {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if (!in_array($file, $this->forbidden)) {
                    if (is_dir($file)) {
                        if (($file != '.') && ($file != '..')) {
                            $isFile = is_file($dir . '/' . $file);
                            yield ['isFile' => $isFile, 'value' => $file];
                        }
                    } else {
                        $isFile = is_file($dir . '/' . $file);
                        yield ['isFile' => $isFile, 'value' => $file];
                    }
                }
            }
            closedir($dh);
        }
    }

    public function getDataForExplorerPage(
        string $dir,
        Request $request,
        string $routeString,
        ?string $path = null,
        bool $isDeniedFromMiddleware = false
    ) {
        $dirs = [];
        $files = [];

        try {
            foreach ($this->explore($dir) as $item) {
                if (!is_null($path)) {
                    $routeData['path'] = $path;

                    if ($item['isFile'] == true) {
                        $routeData['file'] = $item['value'];
                        $url = route($routeString, $routeData);
                        $files[] = [
                            'url' => $url,
                            'urlName' => ShortStr::IfLengthBiggerThen($item['value'], 35),
                        ];
                    } else {
                        $routeData['path'] = $path . '/' . $item['value'];
                        $url = route($routeString, $routeData);
                        $dirs[] = [
                            'url' => $url,
                            'urlName' => ShortStr::IfLengthBiggerThen($item['value'], 35),
                        ];
                    }

                    $routeData = [];
                } else {
                    if ($item['isFile'] == true) {
                        $routeData['file'] = $item['value'];
                        $url = route('code', $routeData);
                        $files[] = [
                            'url' => $url,
                            'urlName' => ShortStr::IfLengthBiggerThen($item['value'], 35),
                        ];
                    } else {
                        $url = route('code') . '/' . $item['value'];
                        $dirs[] = [
                            'url' => $url,
                            'urlName' => ShortStr::IfLengthBiggerThen($item['value'], 35),
                        ];
                    }
                }
            }

            usort($dirs, function ($currentItem, $nextItem) {
                return $currentItem['urlName'] <=> $nextItem['urlName'];
            });
            usort($files, function ($currentItem, $nextItem) {
                return $currentItem['urlName'] <=> $nextItem['urlName'];
            });

            array_push($dirs, ['url' => '', 'urlName' => '']);
            $dirTree = array_merge($dirs, $files);
        } catch (\Exception $exception) {
            $result['errorTitle'] = 'Error';
            $result['errorMessage'] = ErrorMessage::make($exception->getMessage());
            $this->writeErrorLog('Error with explorer executing', [
                'error' => $exception->getMessage()
            ], isAllowedSendToTlg: true);
        }

        $fileName = null;
        $result['dirTree'] = $dirTree ?? [];

        $this->addToResultDataForExplorePage($result, $fileName, $path);

        if (!is_null($fileName) && $isDeniedFromMiddleware === false) {
            $logData['file'] = str_replace('_', '-', $fileName);
            $this->writeInfoLog('Opened file in explorer', $logData, isAllowedSendToTlg: true);
        }

        return $result;
    }


    public function getHighLightedFileForExplorerPage(?string $path = null, ?string $fileName = null)
    {
        if (is_null($path)) {
            $rootDir = $this->getRootDir(2);
            $fullPath = $rootDir;
        } else {
            $rootDir = $this->getRootDir(2);
            $fullPath = $rootDir . '/' . $path;
        }

        if (!is_null($fileName)) {
            ini_set("highlight.default", "#7fb3d5");
            ini_set("highlight.keyword", "#52be80 ; font-weight: bold");
            ini_set("highlight.string", "#cd6155");
            ini_set("highlight.html", "#eb984e");
            ini_set("highlight.comment", "#eb984e");

            try {
                highlight_file($fullPath . '/' . $fileName);

                $logData['path'] = $path;
                $logData['file'] = str_replace('_', '-', $fileName);
                $this->writeInfoLog(
                    'Opened file in explorer, using frame',
                    $logData,
                    isAllowedSendToTlg: true
                );
            } catch (\Exception $exception) {
                $errorMessage = ErrorMessage::make($exception->getMessage());

                $this->writeErrorLog('Error with explorer executing', [
                    'path' => $path,
                    'file name' => $fileName,
                    'error' => $errorMessage
                ], isAllowedSendToTlg: true);

                $result['dirName'] = $path;
                $result['errorTitle'] = 'Error';
                $result['errorMessage'] = $errorMessage;

                return view('code.error-view', $result);
            }
        }
    }

    public function getExplorerView(
        Request $request,
        string $routeString,
        ?string $path = null,
        bool $isDeniedFromMiddleware = false
    ) {
        if (!is_null($path)) {
            $rootDir = $this->getRootDir(2);
            $dir = $rootDir . '/' . $path;
        } else {
            $dir = $this->getRootDir(2);
        }

        $result = $this->getDataForExplorerPage($dir, $request, $routeString, $path, $isDeniedFromMiddleware);

        $result['path'] = $path;
        $result['fileName'] = $request->get('file');

        return view('code.explorer-view', $result);
    }

    public function getNotFoundPage(Request $request)
    {
        $path = null;
        $rootDir = $this->getRootDir(2);
        $dir = $rootDir;

        return view(
            'code.notFound-view',
            $this->getDataForExplorerPage($dir, $request, 'code.common', $path)
        );
    }

    private function addToResultDataForExplorePage(
        array &$currentResult,
        ?string $fileName,
        ?string $path
    ) {
        if (!is_null($path)) {
            $path .= '/' . $fileName;
        }

        $currentUrlItems = explode('/', url()->current());
        end($currentUrlItems);
        $currentFolderName = current($currentUrlItems);
        unset($currentUrlItems[key($currentUrlItems)]);

        if (isset($currentUrlItems[3])) {
            $previousFolderUrl = implode('/', $currentUrlItems);
        } else {
            $previousFolderUrl = route('code');
            $currentFolderName = '/';
        }

        $currentResult['currentFolderName'] = $currentFolderName;
        $currentResult['previousFolderUrl'] = $previousFolderUrl;
        $currentResult['fileName'] = $fileName;
        $currentResult['dirName'] = $path;
    }
}
