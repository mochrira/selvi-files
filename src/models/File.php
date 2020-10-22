<?php 

namespace Selvi\Files\Models;
use Selvi\Model;
use Selvi\Files\FileController;

class File extends Model {

    protected $table = 'files';
    protected $primary = 'idFile';
    protected $increment = true;

    function __construct() {
        $this->schema = FileController::$schema;
        parent::__construct();
    }

}