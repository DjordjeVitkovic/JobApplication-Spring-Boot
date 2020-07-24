<?php
    /* Simple example of selection */

    // send headers first; let it know it's JSON
    header('Content-Type: application/json');

    // some random data with available options
    $shopData = array(
        'jacket' => array(
            array(
                'content' => 'Select jacket size',
                'value' => '',
            ),
            array(
                'content' => 'XS, Extra Small',
                'value' => 'xs',
                'keys' => 'xs,red,blue,brown',
            ),
            array(
                'content' => 'S, Small',
                'value' => 's',
                'keys' => 's,red,green',
            ),
            array(
                'content' => 'M, Medium',
                'value' => 'm',
                'keys' => 'm,purple,brown',
            ),
            array(
                'content' => 'L, Large',
                'value' => 'l',
                'keys' => 'l,blue,purple,brown',
            ),
            array(
                'content' => 'XL, Extra Large',
                'value' => 'xl',
                'keys' => 'xl,red,purple,brown',
            ),
            array(
                'content' => 'XXL',
                'value' => 'xxl',
                'keys' => 'xxl,red,green,brown',
            ),
        ),
        'shorts' => array(
            array(
                'content' => 'Select shorts size',
                'value' => '',
            ),
            array(
                'content' => 'XS, Extra Small',
                'value' => 'xs',
                'keys' => 'xs,red,blue,purple',
            ),
            array(
                'content' => 'S, Small',
                'value' => 's',
                'keys' => 's,red,green',
            ),
            array(
                'content' => 'M, Medium',
                'value' => 'm',
                'keys' => 'm,red,green,blue,purple,brown',
            ),
            array(
                'content' => 'L, Large',
                'value' => 'l',
                'keys' => 'l,red,brown',
            ),
            array(
                'content' => 'XL, Extra Large',
                'value' => 'xl',
                'keys' => 'xl,blue,purple,brown',
            ),
            array(
                'content' => 'XXL',
                'value' => 'xxl',
                'keys' => 'xxl,red,green,brown',
            ),
        ),
        'underwear' => array(
            array(
                'content' => 'Select underwear size',
                'value' => '',
            ),
            array(
                'content' => 'XS, Extra Small',
                'value' => 'xs',
                'keys' => 'xs,red,green,blue,brown',
            ),
            array(
                'content' => 'S, Small',
                'value' => 's',
                'keys' => 's,red',
            ),
            array(
                'content' => 'M, Medium',
                'value' => 'm',
                'keys' => 'm,blue',
            ),
            array(
                'content' => 'L, Large',
                'value' => 'l',
                'keys' => 'l,purple',
            ),
            array(
                'content' => 'XL, Extra Large',
                'value' => 'xl',
                'keys' => 'xl,brown',
            ),
            array(
                'content' => 'XXL',
                'value' => 'xxl',
                'keys' => 'xxl,red,green,blue,purple,brown',
            ),
        ),
        'boots' => array(
            array(
                'content' => 'Select boots size',
                'value' => '',
            ),
            array(
                'content' => '38',
                'value' => '38',
                'keys' => 'white,black,brown',
            ),
            array(
                'content' => '39',
                'value' => '39',
                'keys' => 'white,black',
            ),
            array(
                'content' => '40',
                'value' => '40',
                'keys' => 'white,black,brown',
            ),
            array(
                'content' => '41',
                'value' => '41',
                'keys' => 'red,green,blue',
            ),
            array(
                'content' => '41.5',
                'value' => '41.5',
                'keys' => 'blue,purple',
            ),
            array(
                'content' => '42',
                'value' => '42',
                'keys' => 'white,brown',
            ),
            array(
                'content' => '43',
                'value' => '43',
                'keys' => 'white,black,brown',
            ),
            array(
                'content' => '44',
                'value' => '44',
                'keys' => 'white,black,red,green,blue,purple,brown',
            ),
        ),
        'skirt' => array(
            array(
                'content' => 'Select skirt size',
                'value' => '',
            ),
            array(
                'content' => 'XS, Extra Small',
                'value' => 'xs',
                'keys' => 'xs,red,brown',
            ),
            array(
                'content' => 'S, Small',
                'value' => 's',
                'keys' => 's,red,green,brown',
            ),
            array(
                'content' => 'M, Medium',
                'value' => 'm',
                'keys' => 'm,purple,brown',
            ),
            array(
                'content' => 'L, Large',
                'value' => 'l',
                'keys' => 'l,red,green',
            ),
            array(
                'content' => 'XL, Extra Large',
                'value' => 'xl',
                'keys' => 'xl,brown',
            ),
            array(
                'content' => 'XXL',
                'value' => 'xxl',
                'keys' => 'xxl,red,green,blue,purple,brown',
            ),
        ),
        'suit' => array(
            array(
                'content' => 'Select suit size',
                'value' => '',
            ),
            array(
                'content' => 'XS, Extra Small',
                'value' => 'xs',
                'keys' => 'xs,brown',
            ),
            array(
                'content' => 'S, Small',
                'value' => 's',
                'keys' => 's,blue,purple',
            ),
            array(
                'content' => 'M, Medium',
                'value' => 'm',
                'keys' => 'm,red,blue,brown',
            ),
            array(
                'content' => 'L, Large',
                'value' => 'l',
                'keys' => 'l,red,green,blue,purple',
            ),
            array(
                'content' => 'XL, Extra Large',
                'value' => 'xl',
                'keys' => 'xl,red,blue,purple,brown',
            ),
            array(
                'content' => 'XXL',
                'value' => 'xxl',
                'keys' => 'xxl,red,green,blue,purple,brown',
            ),
        ),
    );

    // data returned by  script
    $data = array();

    // check if keys were submited and if there is options for keys
    if( isset($_GET['keys']) ){
        if(is_array($_GET['keys'])){
            foreach ($_GET['keys'] as $key) {
                if( isset($shopData[$key]) ){
                    $data['result'] = $shopData[$key];
                    $data['found'] = true;
                    break;
                }
            }
        }
    }
    echo json_encode( $data );