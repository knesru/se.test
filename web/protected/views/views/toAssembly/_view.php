<?php
/* @var $this ToAssemblyController */
/* @var $data Extcomponents */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('partnumberid')); ?>:</b>
	<?php echo CHtml::encode($data->partnumberid); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('partnumber')); ?>:</b>
	<?php echo CHtml::encode($data->partnumber); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('amount')); ?>:</b>
	<?php echo CHtml::encode($data->amount); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('userid')); ?>:</b>
	<?php echo CHtml::encode($data->userid); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('purpose')); ?>:</b>
	<?php echo CHtml::encode($data->purpose); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('created_at')); ?>:</b>
	<?php echo CHtml::encode($data->created_at); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('delivered')); ?>:</b>
	<?php echo CHtml::encode($data->delivered); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('assembly_to')); ?>:</b>
	<?php echo CHtml::encode($data->assembly_to); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('install_to')); ?>:</b>
	<?php echo CHtml::encode($data->install_to); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('deficite')); ?>:</b>
	<?php echo CHtml::encode($data->deficite); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('description')); ?>:</b>
	<?php echo CHtml::encode($data->description); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('install_from')); ?>:</b>
	<?php echo CHtml::encode($data->install_from); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('priority')); ?>:</b>
	<?php echo CHtml::encode($data->priority); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('requestid')); ?>:</b>
	<?php echo CHtml::encode($data->requestid); ?>
	<br />

	*/ ?>

</div>