<?php
/* @var $this SiteController */
/* @var $model Extcomponents */

$this->breadcrumbs=array(
	'Extcomponents'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Extcomponents', 'url'=>array('index')),
	array('label'=>'Create Extcomponents', 'url'=>array('create')),
	array('label'=>'Update Extcomponents', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Extcomponents', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Extcomponents', 'url'=>array('admin')),
);
?>

<h1>View Extcomponents #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'partnumberid',
		'partnumber',
		'amount',
		'userid',
		'purpose',
		'created_at',
		'delivered',
		'assembly_to',
		'install_to',
		'deficite',
		'description',
		'install_from',
		'priority',
		'requestid',
	),
)); ?>
