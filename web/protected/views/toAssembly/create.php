<?php
/* @var $this ToAssemblyController */
/* @var $model Extcomponents */

$this->breadcrumbs=array(
	'Extcomponents'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Extcomponents', 'url'=>array('index')),
	array('label'=>'Manage Extcomponents', 'url'=>array('admin')),
);
?>

<h1>Create Extcomponents</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>