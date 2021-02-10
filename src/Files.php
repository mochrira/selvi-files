<?php 

namespace Selvi;
use Selvi\Factory;
use Selvi\Input;
use Selvi\Exception;

class Files {

    private static $basePath;

    static function setup($config = []) {
        if(isset($config['basePath'])) {
            self::$basePath = $config['basePath'];
        }
    }

    function upload($name = 'file', $params = []) {
        try {
            $input = Factory::load(Input::class, [], 'input');
            $file = $input->file($name);

            $fileType = mime_content_type($file['tmp_name']);
            if(isset($params['allowedTypes']) && $params['allowedTypes'] > 0) {
                if(!in_array($fileType, $params['allowedTypes'])) {
                    Throw new Exception('Type not allowed', 'files/invalid-type', 500);
                }
            }

            $fileSize = filesize($file['tmp_name']);
            if(isset($params['maxSize'])) {
                if($fileSize > $params['maxSize']) {
                    Throw new Exception('File too large', 'files/file-too-large', 500);
                }
            }

            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filePath = $params['name'].'.'.$ext ?: $file['name'];
            $fullPath = self::$basePath.'/'.$filePath;
            if(isset($params['path']) && strlen($params['path']) > 0) {
                if(!is_dir(self::$basePath.'/'.$params['path'])) {
                    mkdir(self::$basePath.'/'.$params['path'], 0777, true);
                }
                $filePath = $params['path'].'/'.$filePath;
                $fullPath = self::$basePath.'/'.$filePath;
            }
            if(move_uploaded_file($file['tmp_name'], $fullPath)) {
                $fileInfo = pathinfo($fullPath);
                return [
                    'fileName' => $file['name'],
                    'rawName' => $fileInfo['filename'],
                    'fileExt' => $fileInfo['extension'],
                    'fileType' => $fileType,
                    'filePath' => $filePath,
                    'fullPath' => $fullPath,
                    'fileSize' => $fileSize
                ];
            } else {
                Throw new Exception('Failed to upload', 'files/unknown-error', 500);    
            }
        } catch(\Exception $e) {
            Throw new Exception($e->getMessage(), 'files/failed-to-upload', 500);
        }
    }

    function download($path) {
        $file = self::$basePath.'/'.$path;
        if(!is_file($file)) {
            return response('', 404);
        }
        $mimeType = mime_content_type($file);
        if (file_exists($file)) {
            header('Content-Type: '.$mimeType);
            header('Content-Disposition: attachment; filename="'.basename($file).'"');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            die();
        }
    }

}