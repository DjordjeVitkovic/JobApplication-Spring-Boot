<?php

//Forms Plus class itself
require_once('./formsPlus/formsPlusBasic.php');

//Data Proxy class for getting data via $_POST
require_once('./formsPlus/dataProxy/formsPlusPostData.php');
//Data Proxy class for getting uploaded files via $_FILES
require_once('./formsPlus/dataProxy/formsPlusFileData.php');

//Data type class for doing stuff with 'text'(is default field type) field types
require_once('./formsPlus/dataType/formsPlusTextDataType.php');
//Data type class for doing stuff with 'email' field types
require_once('./formsPlus/dataType/formsPlusEmailDataType.php');
//Data type class for doing stuff with 'captcha' field types
require_once('./formsPlus/dataType/formsPlusCaptchaDataType.php');
//Data type class for doing stuff with 'array' field types
require_once('./formsPlus/dataType/formsPlusArrayDataType.php');
//Data type class for doing stuff with 'array' field types, does not allow custom values, only from 'valuesList'
require_once('./formsPlus/dataType/formsPlusChoiceDataType.php');
//Data type class for doing stuff with date and/or time
require_once('./formsPlus/dataType/formsPlusDateTimeDataType.php');
//Data type class for doing stuff with files
require_once('./formsPlus/dataType/formsPlusFileDataType.php');


//Service class for sending emails via sendmail(with mail() function)
require_once('./formsPlus/service/formsPlusSendmailService.php');

//Swiftmailer library
require_once('./swiftmailer/lib/swift_required.php');

//Service class for sending emails with Swiftmailer library(smtp, sendmail)
require_once('./formsPlus/service/formsPlusSwiftmailerService.php');

//Service class for storing data in database(MySQLi)
require_once('./formsPlus/service/formsPlusSimpleMySQLiService.php');

//Service class for storing data in CSV file
require_once('./formsPlus/service/formsPlusCSVService.php');

//Service class for storing data in txt or log file
require_once('./formsPlus/service/formsPlusFileService.php');

//Autoload Google API classes
require_once('GoogleApi/src/Google/autoload.php');
//Service class for storing data in Google Sheets
require_once('formsPlus/service/formsPlusGoogleSheetsService.php');


//Creating new form
$form                                                       = new formsPlusBasic(array(
    /*
        Error, success and other message templates

        Dynamic values can be set via {propertyName}, examples:
            {name}                      - simple property access
            {fields:email:value}        - accessing fields['email']['value']
            {messages:error:__join}     - accessing messages['errors'] and joining it with '', there is also other sub functions available:
                __join                  - joins array with ''
                __join:,                - joins array with ',', you can change ',' on anything except ':'
                    {messages:error:__join:,}   {messages:error:__join:;}   {messages:error:__join:, }
                __first                 - gets first array element
                    {messages:error:__first}
                __last                  - gets last array element
                    {messages:error:__last}
    */
    'msgTemplates'                                              => array(
        //Core
            //interfaces not found, properties: {interface}
            'interfaceNotFound'                                 => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Please include <strong>{interface}</strong> interface to your php file.</div>",
            //class not implements required interface, properties: {class}, {interface}
            'notImplements'                                     => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{class}</strong> class not found or does not implements <strong>{interface}</strong>.</div>",
            //failed to set up content block, properties: {name}
            'failedSetContentBlock'                             => "<div class=\"alert alert-warning\"><strong><i class=\"fa fa-times\"></i> Warning:</strong> failed to set up <strong>{name}</strong> content block.</div>",
            //failed to set up service, properties: {name}
            'failedSetService'                                  => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> failed to set up <strong>{name}</strong> service.</div>",
            /*
                Result content templates
                properties: {messages}, {fields}
                check result of $form->getNiceData
            */
            'success'                                           => "<div class=\"alert alert-valid\"><strong><i class=\"fa fa-check\"></i> Thank you</strong>, your message has been submitted to us.</div>",
            'error'                                             => "{messages:error:__join}",

        //Data type messages
            //All datatypes
                'fieldRequired'                                 => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{name}</strong> is required.</div>",

            //Array and Choice data types
                //properties: {name}, {min}
                'arrayMinError'                                 => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{name}</strong> - please choose {min} or more.</div>",
                //properties: {name}, {max}
                'arrayMaxError'                                 => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{name}</strong> - please choose {max} or less.</div>",
            //Captcha data type
                //wrong captcha code, properties: -
                'captchaError'                                  => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Please enter correct captcha code.</div>",
            //DateTime data type
                //date-time is less than 'minTime', properties: {name}, {time}
                'fieldDateTimeMinError'                         => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{name}</strong> -  should not be less than {time}.</div>",
                //date-time is greater than 'maxTime', properties: {name}, {time}
                'fieldDateTimeMinError'                         => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{name}</strong> -  should not be greater than {time}.</div>",
            //Email data type
                //invalid email, properties: {name}
                'emailError'                                    => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{name}</strong> - please enter a valid email address.</div>",
            //File data type
                //directory to save file does not exists or can't be written, properties: {name}
                'fieldDirectoryError'                           => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{name}</strong> - upload directory error.</div>",
                //file upload error, properties: {name}
                'fieldFileError'                                => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{name}</strong> - file upload error.</div>",
                //no file uploaded, properties: {name}
                'fieldFileNoFile'                               => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{name}</strong> - no file uploaded.</div>",
                //file max size error, properties: {name}
                'fieldFileMaxSizeError'                         => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{name}</strong> - file max size error.</div>",
                //file min size error, properties: {name}
                'fieldFileMinSizeError'                         => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{name}</strong> - file min size error.</div>",
                //file extension/type is not in 'fileTypes' list, properties: {name}
                'fieldFileTypeError'                            => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{name}</strong> - file type is not allowed.</div>",
                //file mime type is not in 'fileMimeTypes' list, properties: {name}
                'fieldFileMimeTypeError'                        => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{name}</strong> - file mime type is not allowed.</div>",
                //failed to move file to upload directory, properties: {name}
                'fieldFileStoreError'                           => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{name}</strong> - failed to store file.</div>",
        
        //Services messages
            //All services
                //service is not available, properties: {name}
                'serviceNotAvailable'                           => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> {name} service is not available.</div>",
                //failed to store data, properties: {name} {msg}
                'serviceStoreError'                             => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> {name} service failed to store data.</div>",
            //CSV and File services
                //path to file is not set, properties: {name}
                'servicePathNotSet'                             => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> File path not set (<strong>{name}</strong> service).</div>",
                //directory doesn't exists, properties: {name}
                'serviceDirectoryNotExists'                     => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Directory does not exists (<strong>{name}</strong> service).</div>",
                //file extension is not set or not allowed, properties: {name}
                'servicePathExtension'                          => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Wrong file extension (<strong>{name}</strong> service).</div>",
                //failed to create file, properties: {name}
                'serviceFailedCreateFile'                       => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Failed to create file (<strong>{name}</strong> service).</div>",
                //can't write to file (file permissions error, etc.), properties: {name}
                'serviceNotWritableFile'                        => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> File is not writable (<strong>{name}</strong> service).</div>",
                //can't set file permissions, properties: {name}
                'serviceFailedSetPermissions'                   => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Failed to set file permissions (<strong>{name}</strong> service).</div>",
            //Sendmail and Swiftmailer services
                //invalid reciever email, properties: {name}
                'notValidRecieverEmail'                         => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Not valid reciever email in <strong>{name}</strong> service.</div>",
                //invalid sender email, properties: {name}
                'notValidSenderEmail'                           => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Not valid sender email in <strong>{name}</strong> service.</div>",
                //failed to send email, properties: {name}
                'failedSendEmail'                               => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Failed to send email (<strong>{name}</strong> service).</div>",
            //Swiftmailer service
                //rejected email addresses, properties: {name}, {emails}
                'notSentEmails'                                 => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Failed to send email (<strong>{name}</strong> service) to: {emails:__join:, }.</div>",
            //Google API: Google Sheets service
                //authFile is not set, properties: {name}
                'googleAPIAuthorizationFileNotSet'              => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Google API Authorization File is not specified (<strong>{name}</strong> service).</div>",
                //keyFile is not set, properties: {name}
                'googleAPIKeyFileNotSet'                        => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Google API key File is not specified (<strong>{name}</strong> service).</div>",
                //appName is not set, properties: {name}
                'googleAPIAppNameNotSet'                        => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Google API Application Name is not specified (<strong>{name}</strong> service).</div>",
                //credentialsFile is not set, properties: {name}
                'googleAPICredentialsFileNotSet'                => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Google API Credentials File Path is not specified (<strong>{name}</strong> service).</div>",
                //clientId is not set, properties: {name}
                'googleAPIClientIdNotSet'                       => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Google API OAuth Client Id is not specified (<strong>{name}</strong> service).</div>",
                //serviceEmail is not set, properties: {name}
                'googleAPIServiceEmailNotSet'                   => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Google API Service Email is not specified (<strong>{name}</strong> service).</div>",
                //failed to get credentials is not set, properties: {name}, {msg}
                'googleAPICredentialsError'                     => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Google API failed to get credentials (<strong>{name}</strong> service) - {msg}.</div>",
                //autorization required, properties: {name}, {url}, {path}
                'googleAPICredentialsRequired'                  => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Google API Authorization required (<strong>{name}</strong> service).<br/>Please open this <a href=\"{url}\" target=\"_blank\"><strong>link</strong></a> in your browser, follow authorization and save code at <strong>{path}</strong></div>",

    ),
    //Data proxy class name, or class object, for getting uploaded files ($_FILES)
    'fileProxy'                                                 => 'formsPlusFileData',
    //Data proxy class name, or class object, for getting user data ($_POST)
    'dataProxy'                                                 => 'formsPlusPostData',
    //Default fields data type
    'defaultDataType'                                           => 'text',
    /*
        Data types list:
            'name' => 'class'
        Where 'name' - type property used in fields, 'class' - data type class name, or class object, used for doing stuff with fields
    */
    'dataTypes'                                                 => array(
        'text'                                                      => 'formsPlusTextDataType',
        'email'                                                     => 'formsPlusEmailDataType',
        'captcha'                                                   => 'formsPlusCaptchaDataType',
        'array'                                                     => 'formsPlusArrayDataType',
        'choice'                                                    => 'formsPlusChoiceDataType',
        'dateTime'                                                  => 'formsPlusDateTimeDataType',
        'file'                                                      => 'formsPlusFileData',
    ),
    /*
        Fields list. Properties depends on it's 'type' property.
        Type can be any of dataTypes name's, if not set or there is no such data type falls to defaultDataType
    */
    'fields'                                                    => array(
        /*** Text dataType - simple text field; all properties are optional  ***/
        'name'                                                      => array(
            // field dataType name, if not set or there is no such dataType falls to defaultDataType
            'type'                                                      => 'text',
            // name of field, by which it will get data, e.g. $_POST[ 'fieldName' ]; by default takes key value, for this field - 'name'
            'name'                                                      => 'fieldName',
            // title used in emails, error message templates; by default takes key value, for this field - 'name'
            'title'                                                     => 'Name',
            // allow empty or not; by default - false
            'required'                                                  => true,
            // list of 'Nice values', when is set used in result, replaces value with 'Nice value' from list if any; by default - null
            'valuesList'                                                => null, // array('value1'=> 'Nice value 1', 'value2'=> 'Nice value 2')
            // store value from 'valuesList' or actual value (for storing in database)
            'storeNice'                                                 => true,
            // output it in default email template or not; by default - false
            'ignore'                                                    => false,
            // default value, if empty; by default - null
            'default'                                                   => null,
            // step to jump on error, should be set on forms with steps only; by default - null
            'step'                                                      => null,
            // list of message templates, to override for current field; by default - null
            'msgTemplates'                                              => array(
                'fieldRequired'                                             => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Please input Your Name.</div>",
            )
        ),
        /*** Email dataType - email field, can have same features as Text dataType; if not empty - checks it to be valid email; all properties are optional  ***/
        'email'                                                     => array(
            'type'                                                      => 'email',
            'title'                                                     => 'Email',
            'required'                                                  => true,
            //Email dataType validation messages override
            'msgTemplates'                                              => array(
                'emailError'                                                => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Please input valid main email.</div>",
            )
        ),
        /*** Array dataType - for array like fields, can have same features as Text dataType; all properties are optional  ***/
        'products'                                                  => array(
            'type'                                                      => 'array',
            'title'                                                     => 'Selected products',
            'required'                                                  => true,
            // min size required, should be integer; by default - null, e.g. any size or empty
            'min'                                                       => 2,
            // max size required, should be integer; by default - null, e.g. any size or empty
            'max'                                                       => 4,
            // string to join array elements for output, should be string; by default - ', '
            'join'                                                      => '; ',
            // array dataType validation messages override
            'msgTemplates'                                              => array(
                'arrayMinError'                                         => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Please select at least {min} products.</div>",
                'arrayMaxError'                                         => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Please select no more than {max} products.</div>",
            )
        ),
        /*** Choice dataType - for array like fields, does not allow custom values, only from 'valuesList'; can have same features as Array dataType; all properties are optional  ***/
        'delivery'                                              => array(
            'type'                                                      => 'choice',
            'title'                                                     => 'Delivery type',
            'required'                                                  => true,
            // if is false 'max' is forced to 1, and 'min' if is set will be not greater than 1; default - false
            'multiple'                                                  => false,
            'join'                                                      => '; ',
            // list of 'Nice values', only keys from this array will be accepted as value; used in result, replaces value with 'Nice value' from list if any; by default - null
            'valuesList'                                                => array(
                'DHL'                                                       => 'DHL',
                'FedEx'                                                     => 'FedEx Express',
            ),
            // array dataType validation messages override
            'msgTemplates'                                              => array(
                'arrayMaxError'                                         => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Please select no more than {max} delivery method.</div>",
            )
        ),
        /*** DateTime dataType - for date and/or time fields, can have same features as Text dataType; all properties are optional  ***/
        'deliveryDate'                                          => array(
            'type'                                                      => 'dateTime',
            'title'                                                     => 'Delivery date',
            //Date-time format, check out http://php.net/manual/en/datetime.createfromformat.php for format; default 'd.m.Y h:i a' - 26.05.2016 12:00 am
            'format'                                                    => 'd.m.y',
            //Database store format, remember MySQL has limited formats that it accepts; default - 'Y-m-d H:i:s'
            'storeFormat'                                               => 'Y-m-d',
            //List of formats for parsing, also 'format' and 'storeFormat' are added automaticaly at the end of it; default - null
            'parseFormats'                                              => array(
                'd.m.Y',
                'd-m-Y'
            ),
            //Min date allowed, can be DateTime object, or string in any of 'parseFormats', 'format' or 'storeFormat'; default - null
            'minTime'                                                   => new DateTime(),
            //Max date allowed, can be DateTime object, or string in any of 'parseFormats', 'format' or 'storeFormat'; default - null
            'maxTime'                                                   => '12.05.2019',
            // array dataType validation messages override
            'msgTemplates'                                              => array(
                'fieldDateTimeMinError'                                     => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{name}</strong> - aorre, but we don't travel in time.</div>",
            )
        ),
        /*** File dataType - for file uploading/storing, can have same features as Text dataType; all properties are optional  ***/
        'photo'                                                     => array(
            'type'                                                      => 'file',
            'title'                                                     => 'Photo',
            // directory to store files; by default - './files/'
            'dir'                                                       => './files/',
            // store file or not, i.e. it can be send directly to your email without moving it to 'dir'; default - true
            'storeFile'                                                 => true,
            // file min size, can be integer, '<number>', '<number>KB', '<number>MB', '<number>GB', '<number>TB'; default - null
            'minSize'                                                   => '1KB',
            // file max size, can be integer, '<number>', '<number>KB', '<number>MB', '<number>GB', '<number>TB'; default - null
            'maxSize'                                                   => '1MB',
            // allowed file types; default - null
            'fileTypes'                                                 => array(
                'png', 'jpg'
            ),
            // allowed file mime types; default - null
            'fileMimeTypes'                                             => array(
                'image/png', 'image/jpg'
            ),
            // array dataType validation messages override
            'msgTemplates'                                              => array(
                'fieldFileMimeTypeError'                                    => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Whoops...</strong> your photo mime type is not allowed.</div>",
            )
        ),
        /***
            Captcha dataType - for captcha validation, can have same features as Text dataType
            Is always ignored for output. Check for valid captcha code.
        ***/
        'captcha'                                                   => array(
            'type'                                                      => 'captcha',
            'required'                                                  => true,
            //name of captcha hash field, by which it will get data, e.g. $_POST[ 'captchaHash' ]; by default takes 'name' property and adds 'Hash' to it, for this field - 'captcha'.'Hash'
            'hashField'                                                 => 'captchaHash',
            // captcha dataType validation messages override
            'msgTemplates'                                              => array(
                'captchaError'                                          => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Invalid captcha code.</div>",
            )
        ),
        /*** example of dataType defined field with minimum settings ***/
        'extraEmail'                                                => array(
            'type'                                                      => 'email',
        ),
        /*
            Field example with minimum settings, equal to
            'subject'                                               => array(
                'title'                                                 => 'Subject'
            ),
        */
        'subject'                                                   => 'Subject',
    ),
    /*
        Content blocks settings list:
        'template' property will be proccessed as template with {messages}, {fields} properties available in it
        if 'templateFile' is set it will try to load file (only txt, html, php files allowed) and:
            - if it's txt or html file - use it as 'template'
            - if it's php file use it's content
        check result from $form->getNiceData for detailed {messages}, {fields} example
    */
    'contentBlocks'                                             => array(
        'error'                                                     => array(
            'templateFile'                                              => './templates/contentBlock/error.html',
            'block'                                                     => 'errorContentBlock'
        ),
        'success'                                                   => 'successContentBlock',
        'test'                                                      => array(
            'block'                                                     => 'testContentBlock',
            'template'                                                  => 'Hi there test message'
        ),
        'withtemplate'                                              => array(
            'block'                                                     => 'testContentBlock',
            'template'                                                  => 'Hi there test message'
        )
    ),
    /*
        Services list:
            'name' => 'class'
        Where 'name' - type property used in services settings, 'class' - service class name, or class object, used for doing stuff with data
    */
    'serviceTypes'                                              => array(
        'sendmail'                                                  => 'formsPlusSendmailService',
        'swiftmailer'                                               => 'formsPlusSwiftmailerService',
        'simpleMySQLi'                                              => 'formsPlusSimpleMySQLiService',
        'csv'                                                       => 'formsPlusCSVService',
        'file'                                                      => 'formsPlusFileService',
        'googleSheets'                                              => 'formsPlusGoogleSheetsService',
    ),
    /*
        Services settings list. Properties depends on it's 'type' property.
    */
    'services'                                                  => array(
        /***
            SimpleMySQLi service - used to store data in database(MySQLi)

            This example will store data in 'order' table of 'formsplus' database
        ***/
        'storeData'                                              => array(
            // Optional; service name, used in errors messages; default - same as key, i.e. - 'sendAdminEmail'
            'name'                                                      => 'My greate service',
            // Required; service type definition
            'type'                                                      => 'simpleMySQLi',
            // Optional; database server name; default - 'localhost'
            'serverName'                                                => 'localhost',
            // Optional; database user name; default - 'root'
            'userName'                                                  => 'root',
            // Optional; database user password; default - ''
            'userPassword'                                              => '',
            // Required; database name
            'databaseName'                                              => 'formsplus',
            // Required; database table name
            'tableName'                                                 => 'orders',
            /*
                Optional; default - null
                Possible values:
                    'update'    - will update database table row(s)
                    null        - will insert row to database table
            */
            'storingType'                                               => null,
            // Optional; defines if result of service should be tracked; default - false
            'trackData'                                                 => true,
            // Optional; charset of your database; default - false - will follow default php mysqli functionality
            'charset'                                                   => false,
            /*
                Optional; default - 'id'
                ID column name, used only with trackData enabled
                Should be used only when ID is generated for an AUTO_INCREMENT column,
                if not should be set to false
            */
            'idColumName'                                               => 'id',
            /*
                Required
                fields settings, can be set in few variations:
                1.  array(
                        'fieldName1' => array(
                            'name'      => 'columnNameN',
                            'fieldKey'  => 'formFieldName1',
                            ...
                        ),
                        ...
                        'fieldNameN' => array(
                            'name'      => 'columnNameN',
                            'fieldKey'  => 'formFieldNameN',
                            ...
                        ),
                    )
                2.  array(
                        'fieldName1' => 'columnName1',
                        ...
                        'fieldNameN' => 'columnNameN',
                    )
                    ------------
                    is equal to
                    ------------
                    array(
                        'fieldName1' => array(
                            'name'      => 'columnNameN',
                            'fieldKey'  => 'fieldName1',
                            ...
                        ),
                        ...
                        'fieldNameN' => array(
                            'name'      => 'columnNameN',
                            'fieldKey'  => 'fieldName1',
                            ...
                        ),
                    )
                3.  array(
                        'fieldName1'
                        ...
                        'fieldNameN'
                    )
                    ------------
                    is equal to
                    ------------
                    array(
                        'fieldName1' => 'fieldName1',
                        ......
                        'fieldNameN' => 'fieldNameN',
                    )
                
                Value which will be send to database is tooked from next properties in order:
                template --> byString --> value --> fieldKey
            */
            'fields'                                                    => array(
                'id'                                                    => array(
                    // Optional; if value should be checked on update; default - false
                    'checkOnUpdate'                                         => true,
                    // Optional; if can be updated; default - false
                    'canUpdate'                                             => false,
                    // Optional; if can be inserted/added; default - false
                    'canInsert'                                             => false,
                    // Optional; if set - used for filtering in SQL WHERE Clause
                    //'fetchWhere'                                            => '22'
                    // Optional; if can be fetched/selected; default - true
                    'canFetch'                                              => true
                ),
                'name',
                'custEmail'                                                 => array(
                    // Optional; database column name; default - same as key, i.e. - 'email'
                    'name'                                                      => 'customer_email',
                    // Optional; field title, used in error messages; default - same as name, i.e. - 'customer_email'
                    'title'                                                     => 'Customer email',
                    // Optional; stored value; field key from form fields list - will get stringified data from it; default - same as key, i.e. - 'email'
                    'fieldKey'                                                  => 'email',
                    // Optional; stored value; template which will be parsed(from $form->getNiceData); default - null
                    'template'                                                  => null,
                    // Optional; stored value; will parse and get value(from $form->getNiceData), if failed to get data - will return null; default - null
                    'byString'                                                  => null,
                    // Optional; stored value; default - null
                    'value'                                                     => null,
                    // Optional; defines if result of this field should be tracked, only when trackData is enabled(true) for this service; default - false
                    'trackData'                                                 => false,
                    // Optional; will fail this service if stored value is empty(false, null, ''); default - false
                    'required'                                                  => false
                ),
                'subject'                                                   => array(
                    // Optional; stored value; template which will be parsed(from $form->getNiceData); default - null
                    'template'                                                  => "Order from {fields:name:value}",
                ),
                'products'                                                  => array(
                    // Optional; stored value; will parse and get value(from $form->getNiceData), if failed to get data - will return null; default - null
                    'byString'                                                  => "fields:products:raw:__join:, "
                ),
                'date'                                                      => array(
                    // Optional; stored value; default - null
                    'value'                                                     => date("Y-m-d H:i:s")
                )
            ),
            // Optional; defines if service should drop errors to form or can be skiped on error; default - false; if true - service can be skiped
            'canIgnore'                                                 => false,
            // Optional; list of message templates, to override for current service; by default - null
            'msgTemplates'                                              => array(
                'serviceNotAvailable'                                       => 'Error - sendmail servie not available'
            )
        ),
        /*
            SimpleMySQLi service example 2
            This example will store data in 'order_status' table of 'formsplus' database, if storeData service was succesfull.
            Will get inserted row ID from storeData service, i.e. example of relation creation
        */
        'storeDataStatus'                                           => array(
            'type'                                                      => 'simpleMySQLi',
            'databaseName'                                              => 'formsplus',
            'tableName'                                                 => 'order_status',
            'canIgnore'                                                 => true,
            'fields'                                                    => array(
                'order'                                                     => array(
                    'name'                                                      => 'order_id',
                    'byString'                                                  => "serviceData:storeData:id",
                    'required'                                                  => true
                ),
                'status'                                                    => array(
                    'value'                                                     => 1
                )
            )
        ),
        /***
            Sendmail service - used to send emails via sendmail(with mail() function)
            Next properties will be proccessed as templates:
                'to', 'cc', 'bcc', 'from', 'replyTo', 'subject', 'template'
            with {messages}, {fields} properties available in it; in 'template' you can also use {service}
            'to', 'cc', 'bcc' can be set as email string or array of emails
            check result from $form->getNiceData
        ***/
        'sendAdminEmail'                                            => array(
            // Optional; service name, used in errors messages; default - same as key, i.e. - 'sendAdminEmail'
            'name'                                                      => 'My greate service',
            // Required; service type definition
            'type'                                                      => 'sendmail',
            // Required; reciever email address string or array of emails 
            'to'                                                        => 'admin@mail.com',
            // Required; copy reciever email address string or array of emails
            'cc'                                                        => array(
                'cc-reciever1@mail.com',
                'cc-reciever2@mail.com' => 'Reciever Name',
            ),
            // Required; blind copy reciever email address string or array of emails
            'bcc'                                                       => array(
                'bcc-reciever1@mail.com',
                'bcc-reciever2@mail.com' => 'Reciever Name',
            ),
            // Required; sender email address
            'from'                                                      => 'admin.services@mail.com',
            // Optional; reply to email address; fall to 'from' if empty or not valid
            'replyTo'                                                   => '{fields:email:value}',
            // Optional; email subject; by default - '<no subject>'
            'subject'                                                   => 'New message from {fields:name:value}',
            // Optional; email content type; by default - 'text/plain'
            'contentType'                                               => 'text/plain',
            // Optional; email charset; by default - 'utf-8'
            'charset'                                                   => 'utf-8',
            /*
                Optional; email content template; by default - false
                If not set - forces contentType to 'text/plain', and send all fields data in format:
                    field1Title: field1Value
                    field2Title: field2Value
                    ------------------------
                    fieldNTitle: fieldNValue
            */
            'template'                                                  => false,
            // Optional; header template, put before content; default - false 
            'header'                                                    => false,
            // Optional; footer template, put after content; default - false 
            'footer'                                                    => false,
            // Optional; defines if service should drop errors to form or can be skiped on error; default - false; if true - service can be skiped
            'canIgnore'                                                 => false,
            // Optional; list of message templates, to override for current service; by default - null
            'msgTemplates'                                              => array(
                'serviceNotAvailable'                                       => 'Error - sendmail servie not available'
            )
        ),
        //sendmail example for customer email with templateFile and file attachement
        'sendCustomerEmail'                                         => array(
            'type'                                                      => 'sendmail',
            'to'                                                        => '{fields:email:value}',
            'from'                                                      => 'noreply@mail.com',
            'subject'                                                   => 'Thank You, {fields:name:value}',
            /*
                if 'templateFile' is set it will try to load file (only txt, html, php files allowed) and:
                    - if it's txt or html file - use it as 'template'
                    - if it's php file use it's content
                by default - false
            */
            'templateFile'                                              => "./templates/email/simple.txt"
        ),
        /***
            Swiftmailer service - used to send emails with Swiftmailer library(smtp, sendmail)
            All properties from Sendmail service are used here too, check 'sendAdminEmail'

            Current example:
                Will try to send email with 'coolSMTP' transport, because of useTransport property value,
                if failed will try to send email with 'backup' transport, because of useNextTransport property value.
        ***/
        'sendAdminSwiftEmail'                                       => array(
            'type'                                                      => 'swiftmailer',
            'to'                                                        => 'admin@mail.com',
            'from'                                                      => 'admin.services@mail.com',
            'replyTo'                                                   => '{fields:email:value}',
            'subject'                                                   => 'New message from {fields:name:value}',
            'contentType'                                               => 'text/plain',
            'charset'                                                   => 'utf-8',
            /*
                Optional; by default - null
                Defines which transports to use
                Possible values examples:
                    1.  null                    - send email by every transport
                    2.  false                   - ignore this setting, won't send any email be this service setting
                    3.  true                    - will use first transport for email sending
                    4.  'coolSMTP2'             - will use 'coolSMTP2' transport for email sending
                    5.  array(                  - will send emails with 'coolSMTP' and 'coolSMTP2' transports
                            'coolSMTP',
                            'coolSMTP2'
                        );
            */
            'useTransport'                                              => true,
            /*
                Optional; by default - false
                Defines if next transport should be used if the one in use failed.
                Should be used only with useTransport property value like in 3-5 values examples, to prevent transport multiple use

                Possible values examples:
                    false                   - ignored
                    true                    - next transport, if any, will be used if the one in use failed
            */
            'useNextTransport'                                          => true,
            /*
                Optional; list of transports for email sending; by default uses PHP mail() function to send emails
                Possible values examples:
                    1.  array(                                      - adds single transport
                            -- transport setting properties --
                        )
                    2.  array(                                      - adds multiple transports
                            'transport_1' => array(
                                -- transport setting properties --
                            ),
                            --------------------------------------
                            'transport_N' => array(
                                -- transport setting properties --
                            )
                        )
                    3. any other                                    - will use PHP mail() function to send emails
            */
            /*
                will attach files from this list to email, it can be path or template to get path - '{fields:<your file data type field name>:raw:filePath}';
                files that does not exists will be skipped;
                by default - null
            */
            'files'                                                     => array(
                './email-files/thank-you.txt',
                '{fields:photo:raw:filePath}'
            ),
            'transport'                                                 => array(
                // SMTP transport example
                'coolSMTP'                                                  => array(
                    /*
                        Optional; by default - 'sendmail'
                        Possible values examples:
                            'smtp'          - send email via smtp
                            'sendmail'      - send email via sendmail
                            other           - for now any other value will result in 'sendmail'
                    */
                    'type'                                                      => 'smtp',
                    // Required; server address, e.g. 'smtp.mailserver.com'
                    'server'                                                    => 'smtp.noexist.mailserver.com', // should be changed to your smtp server
                    // Optional; port to use; by default - 25
                    'port'                                                      => 25,
                    // Optional; user name; by default - not set(empty)
                    'username'                                                  => '<user name>',
                    // Optional; user password; by default - not set(empty)
                    'password'                                                  => '<user password>',
                    /*
                        Optional; by default - null (no encryption)
                        Possible values examples:
                            'ssl'           - ssl encryption
                            'tls'           - tls encryption
                            null            - not used

                        For SSL or TLS encryption to work your PHP installation must have appropriate OpenSSL transports wrappers.
                        You can check if "tls" and/or "ssl" are present in your PHP installation by using the PHP function stream_get_transports()
                    */
                    'encryption'                                                => null,
                    /*
                        Optional; by default - true
                        Is used only when 'useNextTransport' is true, defines which transport to use next if this failed
                        Possible values examples:
                            false           - don't go to next transport
                            true            - go to next from list
                            'backup'        - go to 'backup' transport
                    */
                    'nextTransport'                                             => 'backup'
                ),
                // Sendmail transport example
                'coolSendmail'                                              => array(
                    // Preferably to set 'type' to 'sendmail', but for current version it can be skipped
                    'type'                                                      => 'sendmail',
                    /*
                        Optional; by default - null
                        Possible values examples:
                            '/usr/sbin/sendmail -bs'        - will send emails with this command
                            null                            - will use PHP mail() function to send emails
                    */
                    'command'                                                   => '/usr/sbin/sendmail -bs'
                ),
                // Sendmail transport example - will use PHP mail() function to send emails
                'backup'                                                    => null
            )
        ),
        /***
            Swiftmailer service example 2
            Current example:
                Will send email with SMTP transport.
                Minimal SMTP settings:
                    1. username and password are empty
                    2. used default port - 25
                    3. no encryption
        ***/
        'sendCustomerSwiftEmail'                                    => array(
            'type'                                                      => 'swiftmailer',
            'to'                                                        => '{fields:email:value}',
            'from'                                                      => 'noreply@mail.com',
            'subject'                                                   => 'Thank You, {fields:name:value}',
            'template'                                                  => "Thank You, {fields:name:value},".PHP_EOL.
                "for your feedback.".PHP_EOL.PHP_EOL.
                "{fields:name:title}: {fields:name:value}".PHP_EOL.
                "{fields:email:title}: {fields:email:value}".PHP_EOL.
                "{fields:products:title}: {fields:products:value}".PHP_EOL.
                PHP_EOL.
                "Best regards!".PHP_EOL.PHP_EOL.PHP_EOL.
                "Created with Forms Plus",
            'transport'                                                 => array(
                'type'                                                      => 'smtp',
                'server'                                                    => 'smtp.noexist.mailserver.com', // should be changed to your smtp server
            )
        ),
        /***
            Swiftmailer service example 3
            Current example:
                No transport setting - will use PHP mail() function to send emails
        ***/
        'sendCustomerSwiftEmail2'                                   => array(
            'type'                                                      => 'swiftmailer',
            'to'                                                        => '{fields:email:value}',
            'from'                                                      => 'noreply@mail.com',
            'subject'                                                   => 'Thank You, {fields:name:value}',
            'template'                                                  => "Thank You, {fields:name:value},".PHP_EOL.
                "for your feedback.".PHP_EOL.PHP_EOL.
                "{fields:name:title}: {fields:name:value}".PHP_EOL.
                "{fields:email:title}: {fields:email:value}".PHP_EOL.
                "{fields:products:title}: {fields:products:value}".PHP_EOL.
                PHP_EOL.
                "Best regards!".PHP_EOL.PHP_EOL.PHP_EOL.
                "Created with Forms Plus"
        ),
        /***
            File service - used to write to 'txt', 'log' file
            Next properties will be proccessed as templates:
                'header', 'footer', 'template'
            with {messages}, {fields}, {service} properties available in it
            check result from $form->getNiceData
        ***/
        'writeDataFile'                                             => array(
            'type'                                                      => 'file',
            // Optional; service name, used in errors messages; default - same as key, i.e. - 'sendAdminEmail'
            'name'                                                      => 'My greate file service',
            // Optional; separator beetween user submitions; default - PHP_EOL - new line
            'separator'                                                 => PHP_EOL,
            // Required; path to file, will create file if not exists (won't create directories)
            'path'                                                      => 'file/data.txt',
            // Optional; file permissions to set, if file should be be created; default - '0644' - read and write for creator, only read for others
            'createPermissions'                                         => '0644',
            // Optional; override file or push content to it; default - false - push content to the end of file
            'override'                                                  => false,
            /*
                Optional; content template; by default - false
                If not set - creates all fields data in format:
                    field1Title: field1Value
                    field2Title: field2Value
                    ------------------------
                    fieldNTitle: fieldNValue
            */
            'template'                                                  => false,
            // Optional; header template, put before content; default - false 
            'header'                                                    => false,
            // Optional; footer template, put after content; default - false 
            'footer'                                                    => false,
            // Optional; defines if service should drop errors to form or can be skiped on error; default - false; if true - service can be skiped
            'canIgnore'                                                 => false,
            // Optional; list of message templates, to override for current service; by default - null
            'msgTemplates'                                              => array(
                'serviceNotAvailable'                                       => 'Error - file servie not available'
            )
        ),
        /***
            File service example 2
            Current example:
                Will add single line with data from template
        ***/
        'writeLogFile'                                             => array(
            'type'                                                      => 'file',
            'path'                                                      => 'files/data.log',
            'template'                                                  => date("M d Y H:i:s")." - {fields:name:value} {fields:email:value} {ip:value}"
        ),
        /***
            CSV service - used to write to 'csv' file
        ***/
        'writeDataFile'                                             => array(
            'type'                                                      => 'file',
            // Optional; service name, used in errors messages; default - same as key, i.e. - 'sendAdminEmail'
            'name'                                                      => 'My greate file service',
            // Optional; separator beetween user submitions; default - PHP_EOL - new line
            'separator'                                                 => PHP_EOL,
            // Required; path to file, will create file if not exists (won't create directories)
            'path'                                                      => 'file/data.txt',
            // Optional; file permissions to set, if file should be be created; default - '0644' - read and write for creator, only read for others
            'createPermissions'                                         => '0644',
            // Optional; override file or push content to it; default - false - push content to the end of file
            'override'                                                  => false,
            // Optional; defines if result of service should be tracked; default - false
            'trackData'                                                 => true,
            /*
                Required
                fields settings, can be set in few variations:
                1.  array(
                        'fieldName1' => array(
                            'name'      => 'columnNameN',
                            'fieldKey'  => 'formFieldName1',
                            ...
                        ),
                        ...
                        'fieldNameN' => array(
                            'name'      => 'columnNameN',
                            'fieldKey'  => 'formFieldNameN',
                            ...
                        ),
                    )
                2.  array(
                        'fieldName1' => 'columnName1',
                        ...
                        'fieldNameN' => 'columnNameN',
                    )
                    ------------
                    is equal to
                    ------------
                    array(
                        'fieldName1' => array(
                            'name'      => 'columnNameN',
                            'fieldKey'  => 'fieldName1',
                            ...
                        ),
                        ...
                        'fieldNameN' => array(
                            'name'      => 'columnNameN',
                            'fieldKey'  => 'fieldName1',
                            ...
                        ),
                    )
                3.  array(
                        'fieldName1'
                        ...
                        'fieldNameN'
                    )
                    ------------
                    is equal to
                    ------------
                    array(
                        'fieldName1' => 'fieldName1',
                        ......
                        'fieldNameN' => 'fieldNameN',
                    )
                
                Value which will be send to database is tooked from next properties in order:
                template --> byString --> value --> fieldKey
            */
            'fields'                                                    => array(
                'name',
                'custEmail'                                                 => array(
                    // Optional; database column name; default - same as key, i.e. - 'email'
                    'name'                                                      => 'customer_email',
                    // Optional; field title, used in error messages and file headers; default - same as name, i.e. - 'customer_email'
                    'title'                                                     => 'Customer email',
                    // Optional; stored value; field key from form fields list - will get stringified data from it; default - same as key, i.e. - 'email'
                    'fieldKey'                                                  => 'email',
                    // Optional; stored value; template which will be parsed(from $form->getNiceData); default - null
                    'template'                                                  => null,
                    // Optional; stored value; will parse and get value(from $form->getNiceData), if failed to get data - will return null; default - null
                    'byString'                                                  => null,
                    // Optional; stored value; default - null
                    'value'                                                     => null,
                    // Optional; defines if result of this field should be tracked, only when trackData is enabled(true) for this service; default - false
                    'trackData'                                                 => false,
                    // Optional; will fail this service if stored value is empty(false, null, ''); default - false
                    'required'                                                  => false
                ),
                'subject'                                                   => array(
                    // Optional; stored value; template which will be parsed(from $form->getNiceData); default - null
                    'template'                                                  => "Order from {fields:name:value}",
                ),
                'products'                                                  => array(
                    // Optional; stored value; will parse and get value(from $form->getNiceData), if failed to get data - will return null; default - null
                    'byString'                                                  => "fields:products:raw:__join:, "
                ),
                'date'                                                      => array(
                    // Optional; stored value; default - null
                    'value'                                                     => date("Y-m-d H:i:s")
                )
            ),
            // Optional; fields separator; default - ','
            'fieldsSeparator'                                           => ';',
            // Optional; defines if headers should be added to file, if file should be created, text for it is tooked from field title attribute; default - true
            'createHeaders'                                             => true,
            // Optional; defines if service should drop errors to form or can be skiped on error; default - false; if true - service can be skiped
            'canIgnore'                                                 => false,
            // Optional; list of message templates, to override for current service; by default - null
            'msgTemplates'                                              => array(
                'serviceNotAvailable'                                       => 'Error - csv servie not available'
            )
        ),
        /***
            GoogleSheets service - used to store data in Google Sheets with help of Google API https://developers.google.com/sheets/

            You will need to:
                1) create Google Account if you don't have one
                2) create project, if you don't have one, at console.developers.google.com
                3) enable Google Sheets API for project
                4) at Credentials page create OAuth 2.0 client ID
                5) if you set authType to 'service', you will also need to:
                    1) create Service account key with p12 key
                    2) share your Spreedsheet with created Service account ID/email

        ***/
        'storeWorksheet'                                              => array(
            // Optional; service name, used in errors messages; default - same as key, i.e. - 'sendAdminEmail'
            'name'                                                      => 'My Google Sheets service',
            // Required; service type definition
            'type'                                                      => 'googleSheets',
            // Required; Google application name
            'appName'                                                   => 'Your app name',
            /*
                Optional; autentification type; default - null
                Values:
                    'service' - autorizes with Goggle Service ID
                        Should be used only if your server has configured openssl!!!
                        You will need to create at Google console both OAuth 2.0 client ID and Service account key with p12 key
                        Requires 'serviceEmail' and 'clientId' to be set.

                    any other or null - autorizes with OAuth 2.0 client ID
            */
            'authType'                                                  => 'Your app name',
            /*
                Only for authType = 'service'
                Required; your Google OAuth 2.0 client ID
            */
            'clientId'                                                  => '<your OAuth 2.0 client ID>', //<code>.apps.googleusercontent.com
            /*
                Only for authType = 'service'
                Required; your Google Service account ID/Email

                Remember to share your Spreadsheet with this email!!! without sharing you won't be able to add data to it
            */
            'serviceEmail'                                              => '<service account ID/email>', //<service>@<service>.iam.gserviceaccount.com
            /*
                Only for authType = 'service'
                Required; Path to your .p12 key file for Google Service account
            */
            'keyFile'                                                   => './private-data/Google/.key/key.p12',
            //Path to your json file from/for OAuth 2.0 client ID
            'authFile'                                                  => './private-data/Google/.secret/client_secret.json',
            //Path where it will save credentials file for Google API authorization. The directory should exist and be writable, but don't create file itself, only if asked.
            'credentialsFile'                                           => './private-data/Google/.credentials/credentials.json',
            // Optional; defines if headers should be added at start, if worksheet is empty, text for it is tooked from field title attribute; default - true
            'createHeaders'                                             => true,
            /*
                Required; Google Spreadsheet Id
                Is taken from link: docs.google.com/spreadsheets/d/<Google Spreadsheet Id>/edit#gid=0
            */
            'spreadsheetId'                                             => '14S9PRToNegRXKHfZsTj5eazo0yAxepHgvSxgQ3sFmSw', //docs.google.com/spreadsheets/d/14S9PRToNegRXKHfZsTj5eazo0yAxepHgvSxgQ3sFmSw/edit#gid=0
            // Required; Woorksheet ID
            'worksheetId'                                               => 'Livepreview',
            // Optional; defines if result of service should be tracked; default - false
            'trackData'                                                 => true,
            /*
                Required
                fields settings, can be set in few variations:
                1.  array(
                        'fieldName1' => array(
                            'name'      => 'columnNameN',
                            'fieldKey'  => 'formFieldName1',
                            ...
                        ),
                        ...
                        'fieldNameN' => array(
                            'name'      => 'columnNameN',
                            'fieldKey'  => 'formFieldNameN',
                            ...
                        ),
                    )
                2.  array(
                        'fieldName1' => 'columnName1',
                        ...
                        'fieldNameN' => 'columnNameN',
                    )
                    ------------
                    is equal to
                    ------------
                    array(
                        'fieldName1' => array(
                            'name'      => 'columnNameN',
                            'fieldKey'  => 'fieldName1',
                            ...
                        ),
                        ...
                        'fieldNameN' => array(
                            'name'      => 'columnNameN',
                            'fieldKey'  => 'fieldName1',
                            ...
                        ),
                    )
                3.  array(
                        'fieldName1'
                        ...
                        'fieldNameN'
                    )
                    ------------
                    is equal to
                    ------------
                    array(
                        'fieldName1' => 'fieldName1',
                        ......
                        'fieldNameN' => 'fieldNameN',
                    )
                
                Value which will be send to database is tooked from next properties in order:
                template --> byString --> value --> fieldKey
            */
            'fields'                                                    => array(
                'name',
                'custEmail'                                                 => array(
                    // Optional; database column name; default - same as key, i.e. - 'email'
                    'name'                                                      => 'customer_email',
                    // Optional; field title, used in error messages; default - same as name, i.e. - 'customer_email'
                    'title'                                                     => 'Customer email',
                    // Optional; stored value; field key from form fields list - will get stringified data from it; default - same as key, i.e. - 'email'
                    'fieldKey'                                                  => 'email',
                    // Optional; stored value; template which will be parsed(from $form->getNiceData); default - null
                    'template'                                                  => null,
                    // Optional; stored value; will parse and get value(from $form->getNiceData), if failed to get data - will return null; default - null
                    'byString'                                                  => null,
                    // Optional; stored value; default - null
                    'value'                                                     => null,
                    // Optional; defines if result of this field should be tracked, only when trackData is enabled(true) for this service; default - false
                    'trackData'                                                 => false,
                    // Optional; will fail this service if stored value is empty(false, null, ''); default - false
                    'required'                                                  => false
                ),
                'subject'                                                   => array(
                    // Optional; stored value; template which will be parsed(from $form->getNiceData); default - null
                    'template'                                                  => "Order from {fields:name:value}",
                ),
                'products'                                                  => array(
                    // Optional; stored value; will parse and get value(from $form->getNiceData), if failed to get data - will return null; default - null
                    'byString'                                                  => "fields:products:raw:__join:, "
                ),
                'date'                                                      => array(
                    // Optional; stored value; default - null
                    'value'                                                     => date("Y-m-d H:i:s")
                )
            ),
            // Optional; defines if service should drop errors to form or can be skiped on error; default - false; if true - service can be skiped
            'canIgnore'                                                 => false,
            // Optional; list of message templates, to override for current service; by default - null
            'msgTemplates'                                              => array(
                'serviceNotAvailable'                                       => 'Error - servie not available'
            )
        ),
    ),

    /***
        IP detect settings:
        be default takes IP from $_SERVER['REMOTE_ADDR'],
        can also be set to check it from proxy header
    ***/
    // defines if proxy header can be checked for detecting IP; default - false
    'useProxy'                                                      => true,
    // list of trusted proxy IPs; default - empty
    'trustedProxies'                                                => array(
        '192.168.0.1',
        '192.168.0.2'
    ),
    // proxy header; default - 'HTTP_X_FORWARDED_FOR'
    'proxyHeader'                                                   => 'HTTP_X_FORWARDED_FOR',
));


/* Gets value from data proxy class attached to form */
$name                       = "fieldName";
$default                    = "some default value";
$result                     = $form->getProxyValue($name, $default);
var_dump($result);
// Will return $name data from data proxy, or $default if there is no such data


/* Static method to build $template with $params */
$params                     = array('name' => 'World');
$template                   = "<div>Hello {name}</div>";
/*
    results
        $result = "<div>Hello World</div>"

    for more $template examples check 'msgTemplates'
*/
$result                     = formsPlusBasicCore::buildTemplate($template, $params);
var_dump($result);


/* Static method for getting sub values */
$variable                   = array('a', 'b', 'c');
/*
    __join                  - joins array
    __first                 - gets first array element
    __last                  - gets last array element
*/
$funcName                   = '__join';
$result                     = formsPlusBasicCore::getSubVariable($variable, $funcName);
var_dump($result);
// $result = "abc"
$funcName                   = array('test', '__join', ', ');
$i                          = 1;
//takes $funcName[$i]
$result                     = formsPlusBasicCore::getSubVariable($variable, $funcName, $i);
var_dump($result);
// $result = "a, b, c"; $i = 2


/* Static method for checking email */
$result                     = formsPlusBasic::isEmail('not email'); // false
var_dump($result);
$result                     = formsPlusBasic::isEmail('my@gmail.qwertys'); // false
var_dump($result);
$result                     = formsPlusBasic::isEmail('my@gmail.com'); // true
var_dump($result);


/* Set|override form message */
$form->setMessage("myMessage", "<div>Greate message</div>");
// override 'fieldRequired' message
$form->setMessage('fieldRequired', "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{name}</strong> is required.</div>");


/* Set|override dataType */
$form->setDataType('textCopy', 'formsPlusTextDataType');
$form->setDataType('emailCopy', new formsPlusEmailDataType());


/* Set|override field */
$form->setField('anotherEmail', array(
    'type'                  => 'emailCopy'
));
$form->setField('title', 'Title');


/* Set|override content block */
$form->setContentBlock('myblock', array(
    'block'                 => 'myblockName',
    'template'              => "Greate work {fields:name:value}" //check $form->getNiceData for fields property detailed example
));


/* Set|override service */
$form->setServiceType('sendmailCopy', 'formsPlusSendmailService');


/* Set|override service settings */
$form->setService('myCustomEmail', array(
    'type'                  => 'sendmailCopy', //new service we setted up with setServiceType
    'to'                    => 'editor@mail.com',
    'from'                  => 'editor.services@mail.com',
    'subject'               => 'Mail copy from {fields:name:value}'
));


/* Adds message */
$type                       = 'test';
$msg                        = "Thank You {name}";
$name                       = "thankYouMessage";
$params                     = array(
    'name'                      => 'Test User'
);
/*
    Will add message to 'test' messages list with key "thankYouMessage"
    Message will be the same as result from:
        formsPlusBasicCore::buildTemplate($msg, $params);
        e.g. "Thank You Test User"
*/
$form->addMessage($type, $msg, $name, $params);
/* Following two lines will do the same - push "Thank You Test User" message to 'test' messages list */
$form->addMessage($type, $msg, false, $params);
$form->addMessage($type, $msg, '__push', $params);
// Following line push "Thank You {name}" to 'test' messages list, without formating it
$form->addMessage($type, $msg);

/* Set useProxy property - check proxy header when detecting sender IP or not */
$form->setUseProxy(false);  // set useProxy to false
$form->setUseProxy();       // set useProxy to true
/* Get useProxy property */
$result                     = $form->getUseProxy(); // true
var_dump($result);

/* Set proxyHeader property - proxy header name */
$form->setProxyHeader();                    // set proxyHeader to 'HTTP_X_FORWARDED_FOR'
$form->setProxyHeader('X-Forwarded-For');   // set proxyHeader to 'HTTP_X_FORWARDED_FOR'
/* Get proxyHeader property */
$result                     = $form->getProxyHeader();  // 'HTTP_X_FORWARDED_FOR'
var_dump($result);

/* Set trustedProxies property - list of IPs */
$form->setTrustedProxies(false);                                // proxy header will be ignored
$form->setTrustedProxies('192.168.0.1');                        // single proxy IP
$form->setTrustedProxies(array('192.168.0.1', '192.168.0.2'));  // multiple proxy IPs

// Add proxy IP(s) to trustedProxies
$form->addTrustedProxies('192.168.0.3');                        // add single proxy IP
$form->addTrustedProxies(array('192.168.0.4', '192.168.0.5'));  // add multiple proxy IPs

/* Get trustedProxies list */
$result                     = $form->getTrustedProxies();   // array('192.168.0.1', '192.168.0.2', '192.168.0.3', '192.168.0.4', '192.168.0.5')
var_dump($result);

/* Get IP */
$result                     = $form->getIP(); // sender IP address
var_dump($result);


/* Checks if data has been parsed */
$result                     = $form->hasData(); // false
var_dump($result);


/* Gets raw field data */
$validate                   = false;
$result                     = $form->getData($validate); //if $validate = true will also run validation
var_dump($result);
/*
    $result :
        array(
            'name'          => <value>,
            'email'         => <value>,
            'products'      => array(<value1>, <value2>, <...>, <valueN>),
            'captcha'       => array(
                'code'          => <value>,
                'hash'          => 'valueHash'
            ),
            'extraEmail'    => <value>,
            'subject'       => <value>,
            'anotherEmail'  => <value>,
            'title'         => <value>
        );
*/
$result                     = $form->hasData(); // true
var_dump($result);


/* Return nice formated fields data */
$result                     = $form->getNiceData();
var_dump($result);
/*
    $result :
        array(
            'fields'        => array(
                'name'          => array(
                    'title'         => 'Name',
                    'raw'           => <value>,
                    'value'         => <formated value string or '-'> //result of $dataTypeClass->valueToString method
                ),
                'email'         => array(
                    'title'         => 'Name',
                    'raw'           => <value>,
                    'value'         => <formated value string>
                ),
                'products'      => array(
                    'title'         => 'Name',
                    'raw'           => array(<value1>, <value2>, <...>, <valueN>),
                    'value'         => "<value1>, <value2>, <...>, <valueN>"
                ),
                'captcha'       => array(
                    'title'         => 'Name',
                    'raw'           => array(
                        'code'          => <value>,
                        'hash'          => 'valueHash'
                    ),
                    'value'         => '-'
                ),
                'extraEmail'    => array(
                    'title'         => 'Name',
                    'raw'           => <value>,
                    'value'         => <formated value string>
                ),
                'subject'       => array(
                    'title'         => 'Name',
                    'raw'           => <value>,
                    'value'         => <formated value string>
                ),
                'anotherEmail'  => array(
                    'title'         => 'Name',
                    'raw'           => <value>,
                    'value'         => <formated value string>
                ),
                'title'         => array(
                    'title'         => 'Name',
                    'raw'           => <value>,
                    'value'         => <formated value string>
                )
            ),
            'messages'      => array(
                //Messages we've added above with $form->addMessage
                'test'          => array(
                    'thankYouMessage'   => "Thank You Test User",
                    0                   => "Thank You Test User",
                    1                   => "Thank You Test User",
                    2                   => "Thank You {name}",
                )
            ),
            'serviceData'   => array(
                'storeData'     => array(
                    'status'        => true,
                    'id'            => 1, // ID of inserted row
                    'trackData'     => array(
                        'raw'           => array(...),  // data which was send to database, not escaped
                        'columns'       => array(...)   // row data selected from database
                    )
                ),
                'storeDataStatus'   => array(
                    'status'        => true,
                    'id'            => 1 // ID of inserted row
                ),
                'sendAdminEmail'    => array(
                    'status'            => true
                ),
                -----------------------------------
                'sendCustomerSwiftEmail2'   => array(
                    'status'                    => true
                )
            ),
            'ip'            => array(
                'raw'           => '192.168.0.1' // sender IP address from header
                'value'         => '192.168.0.1' // sender IP address or '-' if empty
            )
        );
*/


/* Returns if form is valid */
$result                     = $form->isValid(); // false, if validation was not executed always return false
var_dump($result);
/* Runs validation, if there is some errors will put them in messages list */
$form->validate();
$result                     = $form->isValid(); //Depending on submited data validation returns true or false
var_dump($result);


/* sets step to jump on */
$step                       = 1;
$form->setStep($step);
/* returns step to jump to */
$result                     = $form->getStep($step); // 1
var_dump($result);


/* gets content block result */
$name                       = 'myblock'; // block we've added above with $form->setContentBlock
$result                     = $form->getContentBlockResult($name);
var_dump($result);
/*
    $result :
        array(
            'block'                 => 'myblockName',
            'content'               => "Greate work Name_Field_Value" //Name_Field_Value - data inputed by user in name field
        );
*/


/* Returns Forms Plus specic data array */
$result                     = $form->getResult();
var_dump($result);
/*
    $result :
        if form is valid will return result of $form->getContentBlockResult('success')
            array(
                'block'                 => 'successContentBlock',
                'content'               => "<div class=\"alert alert-valid\"><strong><i class=\"fa fa-check\"></i> Thank you</strong>, your message has been submitted to us.</div>"
            );
        if form is invalid will return result of $form->getContentBlockResult('error'), plus extra data if needed
            array(
                'errorData'             => array(
                    'block'                 => 'errorContentBlock',
                    'content'               => "<error message 1><error message 2><...><error message N>",
                    'step'                  => 1 //Step to jump on, if any was set with $form->setStep by validator or other way
                )
            )
*/
/* Return JSON string of $form->getResult */
$result                     = $form->getJSON();
var_dump($result);



/* Executes services by it's settings */
/*
    Will run all services:
        1) run sendmail service with sendAdminEmail settings - send email by sendmail to admin@mail.com
        2) run sendmail service with sendCustomerEmail settings - send email by sendmail to {fields:email:value} (email inputed by user)
        3) run sendmailCopy service with myCustomEmail settings - send email by sendmail to editor@mail.com
        -------------------------------------
        n) run last service
*/
$form->store();
/*
    Will run sendmailCopy service with myCustomEmail settings - send email by sendmail to editor@mail.com
*/
$form->store('myCustomEmail');
/*
    Will run all services:
        1) run sendmail service with sendAdminEmail settings - send email by sendmail to admin@mail.com
        2) won't run sendmailCopy service with myCustomEmail settings
*/
$form->store(array(
    'sendAdminEmail'        => true,
    'myCustomEmail'         => false
));


/*
    Do all stuff and returns data:
        $type - format of returned data
            'json'      - json string, equal to result of $form->getJSON()
            'result'    - formated form result array, equal to result of $form->getResult()
            'nice'      - formated fields and messages array, equal to result of $form->getNiceData()
            by default  - raw field data, equal to result of $form->getData()
        $services - list of services to execute
            equal to executing $form->store($services);
*/
$type                       = 'json';
$services                   = true;
$result                     = $form->proccess($type, $services);
var_dump($result);


/*
    adds error message, and makes form invalid
*/
$msg                        = "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Whoops... {messageText}.</div>";
$name                       = "somethingWentWrong";
$params                     = array(
    'messageText'                      => 'Something went wrong'
);
// message content will be "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Whoops... Something went wrong.</div>"
$form->addError($msg, $name, $params);
// same as runing $form->addMessage('error', $msg, $name, $params);
// but also makes form invalid
$result                     = $form->isValid(); //false
var_dump($result);
$result                     = $form->getResult();
var_dump($result);
/*
    $result :
        array(
            'errorData'             => array(
                'block'                 => 'errorContentBlock',
                'content'               => "<error messages if any>Something went wrong",
                'step'                  => 1 //Step to jump on, if any was set with $form->setStep by validator or other way
            )
        )
*/
