<?php
    /* Simple example of selection */

    // send headers first; let it know it's JSON
    header('Content-Type: application/json');

    // some random data with available options
    $shopData = array(
        'car1' => array(
            '6495ED',
            'FFD700',
            'F8F8FF',
            '006400',
            'F5DEB3',
            '000080',
            'B0E0E6'
        ),
        'car2' => array(
            '708090',
            'B22222',
            '000000',
            '6495ED',
            'FFD700',
            'B0E0E6'
        ),
        'car3' => array(
            '708090',
            'B22222',
            '000000',
            '6495ED',
            'FFD700',
            'F8F8FF',
            '006400',
            'F5DEB3',
            '000080',
            'B0E0E6'
        ),
    );

    // data returned by  script
    $data = array();

    // check if keys were submited and if there is options for keys
    if( isset($_GET['keys']) ){
        if(is_array($_GET['keys'])){
            foreach ($_GET['keys'] as $key) {
                if( isset($shopData[$key]) ){
                    $content = '';

                    foreach ($shopData[$key] as $color) {
                        $content .= "
                            <div class=\"p-radio-color\">
                                <label>
                                    <input type=\"radio\" name=\"carColor\" value=\"$color\" data-js-option-loaded data-js-option-keys=\"$color\">
                                    <span class=\"p-color-block\" style=\"background: #$color;\"></span>
                                </label>
                            </div>"
                        ;
                    }

                    $data['result'] = "<div class=\"p-form-colorpick p-sub-option\">" . $content . "</div>";
                    $data['found'] = true;
                    break;
                }
            }
        }
    }
    echo json_encode( $data );