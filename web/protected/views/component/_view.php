<?php
/* @var $this ComponentController */
/* @var $data Component */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('partnumberid')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->partnumberid), array('view', 'id'=>$data->partnumberid)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('partnumber')); ?>:</b>
	<?php echo CHtml::encode($data->partnumber); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('type')); ?>:</b>
	<?php echo CHtml::encode($data->type); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('pathid')); ?>:</b>
	<?php echo CHtml::encode($data->pathid); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('updated')); ?>:</b>
	<?php echo CHtml::encode($data->updated); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('is_assembly')); ?>:</b>
	<?php echo CHtml::encode($data->is_assembly); ?>
	<br />


</div>