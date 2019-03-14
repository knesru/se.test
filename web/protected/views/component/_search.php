<?php
/* @var $this ComponentController */
/* @var $model Component */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'partnumberid'); ?>
		<?php echo $form->textField($model,'partnumberid'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'partnumber'); ?>
		<?php echo $form->textField($model,'partnumber',array('size'=>60,'maxlength'=>64)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'type'); ?>
		<?php echo $form->textField($model,'type'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'pathid'); ?>
		<?php echo $form->textField($model,'pathid'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'updated'); ?>
		<?php echo $form->textField($model,'updated'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'is_assembly'); ?>
		<?php echo $form->textField($model,'is_assembly'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->