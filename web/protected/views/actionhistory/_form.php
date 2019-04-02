<?php
/* @var $this ActionhistoryController */
/* @var $model Actionhistory */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'actionhistory-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'initiatoruserid'); ?>
		<?php echo $form->textField($model,'initiatoruserid'); ?>
		<?php echo $form->error($model,'initiatoruserid'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'created_at'); ?>
		<?php echo $form->textField($model,'created_at'); ?>
		<?php echo $form->error($model,'created_at'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'partnumber'); ?>
		<?php echo $form->textField($model,'partnumber',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'partnumber'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'ext_id'); ?>
		<?php echo $form->textField($model,'ext_id',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'ext_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'requestid'); ?>
		<?php echo $form->textField($model,'requestid',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'requestid'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'action'); ?>
		<?php echo $form->textField($model,'action',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'action'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textArea($model,'description',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'description'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'severity'); ?>
		<?php echo $form->textField($model,'severity',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'severity'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->