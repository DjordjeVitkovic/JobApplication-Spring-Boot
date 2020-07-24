<?php
    /* Example of selection with multiple keys */

    // send headers first; let it know it's JSON
    header('Content-Type: application/json');

    // some random data with available options
    $shopData = array(
        'white' => array(
            'content' => 'White',
            'value' => 'white',
            'keys' => 'white'
        ),
        'black' => array(
            'content' => 'Black',
            'value' => 'black',
            'keys' => 'black'
        ),
        'red' => array(
            'content' => 'Red',
            'value' => 'red',
            'keys' => 'red'
        ),
        'green' => array(
            'content' => 'Green',
            'value' => 'green',
            'keys' => 'green'
        ),
        'blue' => array(
            'content' => 'Blue',
            'value' => 'blue',
            'keys' => 'blue'
        ),
        'purple' => array(
            'content' => 'Purple',
            'value' => 'purple',
            'keys' => 'purple'
        ),
        'brown' => array(
            'content' => 'Brown',
            'value' => 'brown',
            'keys' => 'brown'
        ),
    );

    // data returned by  script
    $data = array();

    // check if keys were submited and match options for keys
    if( isset($_GET['keys']) ){
        if(is_array($_GET['keys'])){
            $data['result'] = array();
            foreach ($_GET['keys'] as $key) {
                if( isset($shopData[$key]) ){
                    $data['result'][] = $shopData[$key];
                }
            }
        }
    }
    if( count($data['result']) ){
        $data['found'] = true;
    }
    echo json_encode( $data );