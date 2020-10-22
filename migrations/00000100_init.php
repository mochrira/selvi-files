<?php 

return function ($schema, $direction) {

    if($direction == 'up') {
        $schema->create('files', [
            'idFile' => 'INT(11) PRIMARY KEY AUTO_INCREMENT',
            'path' => 'TEXT',
            'name' => 'VARCHAR(150)',
            'mimeType' => 'VARCHAR(150)',
            'size' => 'INT(11)'
        ]);
    }

    if($direction == 'down') {
        $schema->drop('files');
    }

};