<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Console Application',
    'components'=>array(
        'db'=>array(
            'connectionString' => 'pgsql:host=se.test;port=5432;dbname=stms',
            'emulatePrepare' => true,
            'username' => 'postgres',
            'password' => 'postgres',
            'tablePrefix' => 't',
            'enableProfiling'=>true,
        ),
    ),
);