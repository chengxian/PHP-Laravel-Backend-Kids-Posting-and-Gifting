<?php

return array(

    'appNameIOS'     => array(
        'environment' =>'development',
        'certificate' =>app_path().'/push_cert/aps_development.pem',
        'passPhrase'  =>'',
        'service'     =>'apns'
    ),
    'appNameAndroid' => array(
        'environment' =>'production',
        'apiKey'      =>'yourAPIKey',
        'service'     =>'gcm'
    )

);