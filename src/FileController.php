<?php 

namespace Selvi\Files;
use Selvi\Exception;
use Selvi\Controller;
use Selvi\Route;

class FileController extends Controller {

    public static $basePath;

    /**
     * Setup API to handle file upload
     * @param ArrayObject $config
     */
    public static function setup($config = []) {
        if(isset($config['basePath'])) {
            self::$basePath = $config['basePath'];
        }
        Route::post('/files', '\\Selvi\\Files\\FileController@upload');
        Route::get('/files/(:any)', '\\Selvi\\Files\\FileController@download');
    }
    
    /**
     * POST files
     * 
     * Request Body (multipart/form-data)
     * File : File to upload
     */
    function upload() {
        $file = $this->input->file('file');
        if(!is_dir(self::$basePath)) {
            mkdir(self::$basePath);
        }
        move_uploaded_file($file['tmp_name'], self::$basePath.'/'.$file['name']);
        return jsonResponse([
            'fileUrl' => base_url().'files/'.$file['name']
        ]);
    }

    /**
     * GET files/{filePath}
     * 
     * URI Parameters
     * filePath : path to file relative to basepath
     */
    function download() {
        $file = self::$basePath.str_replace('/files', '', urldecode($this->uri->getUri()));
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