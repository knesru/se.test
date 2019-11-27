<?php
/* @var $this ComponentController */
/* @var $model Component */

$this->breadcrumbs=array(
	'Components'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Component', 'url'=>array('index')),
	array('label'=>'Manage Component', 'url'=>array('admin')),
);
?>

<h1>Create Component</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>