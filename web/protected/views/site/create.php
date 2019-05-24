<?php
/* @var $this SiteController */
/* @var $model Extcomponents */

$this->breadcrumbs=array(
	'Задания в производство'=>array('index'),
	'Создать',
);

$this->menu=array(
	array('label'=>'List Extcomponents', 'url'=>array('index')),
	array('label'=>'Manage Extcomponents', 'url'=>array('admin')),
);
?>

<h1>Create Extcomponents</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>