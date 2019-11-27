<?php
/* @var $this ActionhistoryController */
/* @var $model Actionhistory */

$this->breadcrumbs=array(
	'Actionhistories'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Actionhistory', 'url'=>array('index')),
	array('label'=>'Manage Actionhistory', 'url'=>array('admin')),
);
?>

<h1>Create Actionhistory</h1>
<?php
print_r($model->attributes);
?>
<?php $this->renderPartial('_form', array('model'=>$model)); ?>