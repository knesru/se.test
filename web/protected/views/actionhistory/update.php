<?php
/* @var $this ActionhistoryController */
/* @var $model Actionhistory */

$this->breadcrumbs=array(
	'Actionhistories'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Actionhistory', 'url'=>array('index')),
	array('label'=>'Create Actionhistory', 'url'=>array('create')),
	array('label'=>'View Actionhistory', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Actionhistory', 'url'=>array('admin')),
);
?>

<h1>Update Actionhistory <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>