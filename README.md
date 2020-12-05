# Files library

This is library to upload and download files with selvi framework.

## Requirements

- php^7.4
- php_fileinfo module
- mochrira/selvi-framework^0.3.12

## Installing

```
composer require mochrira/selvi-files
```

## Usage

```
namespace App\Controllers;
use Selvi\Controller;
use Selvi\Files;

class UploadController extends Controller {

    function upload() {
        $this->load(Files::class, 'files');
        $result = $this->files->upload('file', [
            'allowedTypes' => ['image/jpg', 'image/jpeg', 'image/gif'],
            'path' => 'images',
            'maxSize' => 1000000
        ]);
        return jsonResponse($result);
    }

    function download() {
        $uri = $this->uri->getUri();
        if(strpos($uri, '/download') == 0) {
            $uri = preg_replace('/'.preg_quote('/download/', '/').'/', '', $uri, 1);
        }
        $this->load(Files::class, 'files');
        $this->files->download($uri);
    }

}

\Selvi\Files::setup([
    'basePath' => __DIR__.'/files'
]);
\Selvi\Route::post('/upload', 'UploadController@upload');
\Selvi\Route::post('/download', 'UploadController@download');
\Selvi\Framework::run();
```