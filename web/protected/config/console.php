<?php
$db = include dirname(__FILE__).DIRECTORY_SEPARATOR.'/db.php';
if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'/db-local.php')){
    $db = include dirname(__FILE__).DIRECTORY_SEPARATOR.'/db-local.php';
}
// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Console Application',
    'components'=>array(
        'db'=>$db
    ),
);