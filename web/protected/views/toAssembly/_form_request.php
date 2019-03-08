<?php
/* @var $this ToAssemblyController */
/* @var $model Extcomponents */
/* @var $form CActiveForm */


$baseUrl = Yii::app()->baseUrl;
/** @var CClientScript $cs */
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile($baseUrl . '/js/jquery.js');
$cs->registerScriptFile($baseUrl . '/js/jquery-ui.min.js');
//$cs->registerScriptFile($baseUrl . '/js/pq/pqgrid.min.js');
//$cs->registerScriptFile($baseUrl . '/js/common.js');
//$cs->registerCssFile($baseUrl . '/js/pq/pqgrid.min.css');
//$cs->registerCssFile($baseUrl . '/js/themes/office/pqgrid.css');
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
        <?php echo $form->textField($model, 'partnumber', array('size' => 60, 'maxlength' => 255)); ?>
        <?php echo $form->error($model, 'partnumberid'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'partnumberid'); ?>
        <?php echo $form->textField($model, 'partnumberid', array('size' => 60, 'maxlength' => 255)); ?>
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
        <?php //echo $form->dropDownList($model, 'priority',array(0=>'низкий',1=>'обычный',2=>'высокий',3=>'горит!',10=>'критический')); ?>
        <?php echo $form->checkBox($model, 'priority'); ?>
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
    $(function(){
        $inp = $('#Extcomponents_partnumber');
        $inp.autocomplete({
            //appendTo: ui.$cell, //for grid in maximized state.
            source: '/component/ajaxList',
            selectItem: { on: true }, //custom option
            highlightText: { on: true }, //custom option
            minLength: 2,
            select: function (a,b,c) {
                // console.log(a,b,c);
                $.ajax({
                    url: '/component/ajaxComponent',
                    data: {partnumber: b.item.value},
                    success: function (res) {
                        if (typeof res === 'string') {
                            $('#Extcomponents_partnumberid').val(res).addClass('success');
                            $inp.parent().addClass('success');
                        }
                    }
                });
            }
        }).focus(function () {
            //open the autocomplete upon focus
            $(this).autocomplete("search", "");
        });
    });

</script>