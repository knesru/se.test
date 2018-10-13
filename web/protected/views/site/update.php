<?php
/* @var $this SiteController */
/* @var $model Extcomponents */

$this->breadcrumbs=array(
	'Extcomponents'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Extcomponents', 'url'=>array('index')),
	array('label'=>'Create Extcomponents', 'url'=>array('create')),
	array('label'=>'View Extcomponents', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Extcomponents', 'url'=>array('admin')),
);
?>

<h1>Update Extcomponents <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>