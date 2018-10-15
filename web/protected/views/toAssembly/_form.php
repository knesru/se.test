<?php
/* @var $this ToAssemblyController */
/* @var $model Extcomponents */
/* @var $form CActiveForm */
?>

<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'extcomponents-form',
        // Please note: When you enable ajax validation, make sure the corresponding
        // controller action is handling ajax validation correctly.
        // There is a call to performAjaxValidation() commented in generated controller code.
        // See class documentation of CActiveForm for details on this.
        'enableAjaxValidation' => false,
    )); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model, 'partnumber'); ?>
        <?php echo $form->dropDownList($model, 'partnumberid', CHtml::listData(Component::model()->findAll('1=1 limit 100'), 'partnumberid', 'partnumber')); ?>
        <?php echo $form->error($model, 'partnumberid'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'partnumberid'); ?>
        <?php echo $form->textField($model, 'partnumber', array('size' => 60, 'maxlength' => 255)); ?>
        <?php echo $form->error($model, 'partnumber'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'amount'); ?>
        <?php echo $form->numberField($model, 'amount'); ?>
        <?php echo $form->error($model, 'amount'); ?>
    </div>

    <!--<div class="row">
        <?php echo $form->labelEx($model, 'userid'); ?>
        <?php echo $form->textField($model, 'userid'); ?>
        <?php echo $form->error($model, 'userid'); ?>
    </div>-->

    <div class="row">
        <?php echo $form->labelEx($model, 'purpose'); ?>
        <?php echo $form->textArea($model, 'purpose', array('rows' => 6, 'cols' => 50)); ?>
        <?php echo $form->error($model, 'purpose'); ?>
    </div>

    <!--<div class="row">
        <?php echo $form->labelEx($model, 'created_at'); ?>
        <?php echo $form->dateField($model, 'created_at'); ?>
        <?php echo $form->error($model, 'created_at'); ?>
    </div>-->

    <!--<div class="row">
        <?php echo $form->labelEx($model, 'delivered'); ?>
        <?php echo $form->numberField($model, 'delivered'); ?>
        <?php echo $form->error($model, 'delivered'); ?>
    </div>-->

    <div class="row">
        <?php echo $form->labelEx($model, 'assembly_to'); ?>
        <?php echo $form->dateField($model, 'assembly_to'); ?>
        <?php echo $form->error($model, 'assembly_to'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'install_to'); ?>
        <?php echo $form->dateField($model, 'install_to'); ?>
        <?php echo $form->error($model, 'install_to'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'deficite'); ?>
        <?php echo $form->textArea($model, 'deficite', array('rows' => 6, 'cols' => 50)); ?>
        <?php echo $form->error($model, 'deficite'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'description'); ?>
        <?php echo $form->textArea($model, 'description', array('rows' => 6, 'cols' => 50)); ?>
        <?php echo $form->error($model, 'description'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'install_from'); ?>
        <?php echo $form->dateField($model, 'install_from'); ?>
        <?php echo $form->error($model, 'install_from'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'priority'); ?>
        <?php echo $form->dropDownList($model, 'priority',array(0=>'низкий',1=>'обычный',2=>'высокий',3=>'горит!',10=>'критический')); ?>
        <?php echo $form->error($model, 'priority'); ?>
    </div>

    <!--<div class="row">
        <?php echo $form->labelEx($model, 'requestid'); ?>
        <?php echo $form->textField($model, 'requestid'); ?>
        <?php echo $form->error($model, 'requestid'); ?>
    </div>-->

    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->
<script type="application/javascript">
    document.getElementById('Extcomponents_partnumberid').onchange = function () {
        document.getElementById('Extcomponents_partnumber').value = document.getElementById('Extcomponents_partnumberid').value;
    };
</script>