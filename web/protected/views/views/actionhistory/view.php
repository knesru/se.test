<?php
/* @var $this ActionhistoryController */
/* @var $model Actionhistory */

$this->breadcrumbs=array(
	'Actionhistories'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Actionhistory', 'url'=>array('index')),
	array('label'=>'Create Actionhistory', 'url'=>array('create')),
	array('label'=>'Update Actionhistory', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Actionhistory', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Actionhistory', 'url'=>array('admin')),
);
?>

<h1>View Actionhistory #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'initiatoruserid',
		'created_at',
		'partnumber',
		'ext_id',
		'requestid',
		'action',
		'description',
		'severity',
	),
)); ?>
