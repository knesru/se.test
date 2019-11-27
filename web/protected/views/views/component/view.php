<?php
/* @var $this ComponentController */
/* @var $model Component */

$this->breadcrumbs=array(
	'Components'=>array('index'),
	$model->partnumberid,
);

$this->menu=array(
	array('label'=>'List Component', 'url'=>array('index')),
	array('label'=>'Create Component', 'url'=>array('create')),
	array('label'=>'Update Component', 'url'=>array('update', 'id'=>$model->partnumberid)),
	array('label'=>'Delete Component', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->partnumberid),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Component', 'url'=>array('admin')),
);
?>

<h1>View Component #<?php echo $model->partnumberid; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'partnumberid',
		'partnumber',
		'type',
		'pathid',
		'updated',
		'is_assembly',
	),
)); ?>
