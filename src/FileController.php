<?php 

namespace Selvi\Files;
use Selvi\Exception;
use Selvi\Resource;
use Selvi\Route;
use Selvi\Database\Manager as Database;
use Selvi\Files\Models\File;

class FileController extends Resource {

    public static $path;
    public static $schema;

    public static function setup($config = []) {
        if(isset($config['path'])) {
            self::$path = $config['path'];
        }
        if(isset($config['schema'])) {
            self::$schema = $config['schema'];
            Database::get(self::$schema)->addMigration(__DIR__.'/../migrations');
        }
        Route::apiResource('files', '\\Selvi\\Files\\FileController', ['get', 'post', 'delete']);
    }

    protected $modelClass = File::class;
    protected $modelAlias = 'File';
    
    function validateData($data, $file = null) {
        if($this->input->method() == 'POST') {
            $file = $this->input->file('file');
            $data = [
                'path' => '/',
                'name' => $file['name'],
                'mimeType' => mime_content_type($file['tmp_name']),
                'size' => filesize($file['tmp_name'])
            ];

            if(!is_dir(self::$path)) {
                mkdir(self::$path);
            }
            move_uploaded_file($file['tmp_name'], self::$path.$data['path'].$file['name']);
        }
        return $data;
    }

    function afterInsert($file, &$response = null) {
        $response->setContent(json_encode([
            'idFile' => $file->idFile,
            'fileUrl' => base_url().'files/'.$file->name
        ]));
    }

    function get() {
        $file = self::$path.str_replace('/files', '', urldecode($this->uri->getUri()));
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