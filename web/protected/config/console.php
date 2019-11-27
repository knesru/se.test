<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Console Application',
    'components'=>array(
        'db'=>array(
            'connectionString' => 'pgsql:host=localhost;port=5432;dbname=stms_prod',
            //'emulatePrepare' => true,
            'username' => 'stms_dev',
            'password' => 'p@ssword',
            'tablePrefix' => 't',
            'enableProfiling'=>true,
        ),
    ),
);