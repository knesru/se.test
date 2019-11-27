<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
  'connectionString' => 'pgsql:host=localhost;port=5432;dbname=stms_prod',
  //'emulatePrepare' => false,
  'username' => 'stms_dev',
  'password' => 'p@ssword',
  'tablePrefix' => 't',
  'enableProfiling' => true
);