<?php
/* @var $this ToAssemblyController */
/* @var $model Extcomponents */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'id'); ?>
		<?php echo $form->textField($model,'id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'partnumberid'); ?>
		<?php echo $form->textField($model,'partnumberid'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'partnumber'); ?>
		<?php echo $form->textField($model,'partnumber',array('size'=>60,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'amount'); ?>
		<?php echo $form->textField($model,'amount'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'userid'); ?>
		<?php echo $form->textField($model,'userid'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'purpose'); ?>
		<?php echo $form->textArea($model,'purpose',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'created_at'); ?>
		<?php echo $form->textField($model,'created_at'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'delivered'); ?>
		<?php echo $form->textField($model,'delivered'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'assembly_to'); ?>
		<?php echo $form->textField($model,'assembly_to'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'install_to'); ?>
		<?php echo $form->textField($model,'install_to'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'deficite'); ?>
		<?php echo $form->textArea($model,'deficite',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'description'); ?>
		<?php echo $form->textArea($model,'description',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'install_from'); ?>
		<?php echo $form->textField($model,'install_from'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'priority'); ?>
		<?php echo $form->textField($model,'priority'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'requestid'); ?>
		<?php echo $form->textField($model,'requestid'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->