<?php 

namespace Selvi\Files;
use Selvi\Exception;
use Selvi\Controller;
use Selvi\Route;

class FileController extends Controller {

    public static $path;

    public static function setup($config = []) {
        if(isset($config['path'])) {
            self::$path = $config['path'];
        }
        Route::post('/files', '\\Selvi\\Files\\FileController@upload');
        Route::get('/files/(:any)', '\\Selvi\\Files\\FileController@download');
    }
    
    function upload() {
        $file = $this->input->file('file');
        if(!is_dir(self::$path)) {
            mkdir(self::$path);
        }
        move_uploaded_file($file['tmp_name'], self::$path.'/'.$file['name']);
        return jsonResponse([
            'fileUrl' => base_url().'files/'.$file['name']
        ]);
    }

    function download() {
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