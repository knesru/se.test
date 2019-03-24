<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
  'connectionString' => 'pgsql:host=se.test;port=5432;dbname=stms',
  'emulatePrepare' => true,
  'username' => 'postgres',
  'password' => 'postgres',
  'tablePrefix' => 't',
  'enableProfiling' => true
);