<?php
/* @var $this ActionhistoryController */
/* @var $data Actionhistory */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('initiatoruserid')); ?>:</b>
	<?php echo CHtml::encode($data->initiatoruserid); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('created_at')); ?>:</b>
	<?php echo CHtml::encode($data->created_at); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('partnumber')); ?>:</b>
	<?php echo CHtml::encode($data->partnumber); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('ext_id')); ?>:</b>
	<?php echo CHtml::encode($data->ext_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('requestid')); ?>:</b>
	<?php echo CHtml::encode($data->requestid); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('action')); ?>:</b>
	<?php echo CHtml::encode($data->action); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('description')); ?>:</b>
	<?php echo CHtml::encode($data->description); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('severity')); ?>:</b>
	<?php echo CHtml::encode($data->severity); ?>
	<br />

	*/ ?>

</div>