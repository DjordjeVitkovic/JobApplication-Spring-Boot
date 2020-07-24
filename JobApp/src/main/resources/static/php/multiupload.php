<?php

/*
    The library from https://github.com/blueimp/jQuery-File-Upload is used for all stuff
    Check https://github.com/blueimp/jQuery-File-Upload/wiki or multiupload/UploadHandler.php for more details
*/

require_once('./multiupload/UploadHandler.php');
$upload_handler                                             = new UploadHandler(array(
    'delete_type'       => 'POST',
    'param_name'        => 'multiupload',
    //Remove next line, for no max file size
    'max_file_size'     => 1024 * 1024, //1MB limit
    'image_versions'    => array(
        // The empty image version key defines options for the original image:
        ''              => array(
            // Automatically rotate images based on EXIF meta data:
            'auto_orient'   => true
        ),
        'thumbnail'     => array(
            'max_width'     => 500,
            'max_height'    => 500
        )
    )
));