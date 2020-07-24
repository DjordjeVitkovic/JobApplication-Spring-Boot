<?php
    function outputEmailFields($form, $fields, $pref = ""){
        $offs                                               = false;
        $hasOut                                             = false;
        foreach ($fields as $field) {
            if( !($field instanceof formsPlusDataTypeInterface) ){
                $field                                      = $form->getField($field);
            }
            if( !$field || !$field->inGroup($groups) ){
                continue;
            }
            if( $offs ){
                echo "\n";
                $offs                                       = false;
            }
            if( ($field instanceof formsPlusParentDataTypeInterface) && !$field->isSolidValue() ){
                if( $field->isEmpty() ){
                    continue;
                }
                if( $hasOut ){
                    echo "\n";
                    $hasOut                                 = false;
                }
                echo $pref . $field->getTitle() . "\n";
                outputEmailFields($form, $field->getFields(), $pref . "\t");
                $offs                                       = true;
            }else{
                $hasOut                                     = true;
                echo $pref . $field->getTitle() . ': ' . $field->getOutputValue('Not Specified') . "\n";
            }
        }
    }
    echo "Job Number: " . $data['extraData']['jobNumber'] . "\n\n"
    ;
    outputEmailFields($form, array('client'));
    echo "\n";
    outputEmailFields($form, array('contact'));
    echo "\n\n";
    echo "User IP: " . $data['ip']['value'];

    if( isset($data['serviceData']['storeFilesZip']['path']) ){
        echo "\n\n";
        echo "Files archive: " .
            $_SERVER['SERVER_NAME'] .
            pathinfo($_SERVER['REQUEST_URI'], PATHINFO_DIRNAME) .
            '/files/archives/' .
            pathinfo($data['serviceData']['storeFilesZip']['path'], PATHINFO_BASENAME) 
        ;
    }
?>

Generated with: templates/email/job-brief-admin.php