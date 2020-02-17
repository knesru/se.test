<?php
/**
 * @var $this TasksController
 * @var Tasks $tasks_model
 * @var Products $products_model
 * @var UActiveForm $form
 */

?>

<script type="application/javascript">
    $(function () {
        $("#new_tasks_tabs").tabs();
    });

</script>


<div id="popup_dialog_new_task" style="display: none; padding: 0">
    <?php
    $form = $this->beginWidget('UActiveForm', array(
        'id' => 'new-task-form',
        // Please note: When you enable ajax validation, make sure the corresponding
        // controller action is handling ajax validation correctly.
        // There is a call to performAjaxValidation() commented in generated controller code.
        // See class documentation of CActiveForm for details on this.
        'enableAjaxValidation' => false,
    )); ?>
    <div id="new_tasks_tabs">
        <ul>
            <li><a href="#new_tasks_tabs_params">Параметры</a></li>
            <li><a href="#new_tasks_tabs_packages">Товары и получатели</a></li>
        </ul>
        <div id="new_tasks_tabs_params">
            <?php $this->renderPartial('mw_new_task/tab_main_form', compact('tasks_model', 'products_model', 'form')); ?>
        </div>
        <div id="new_tasks_tabs_packages">
            <?php $this->renderPartial('mw_new_task/tab_packages', compact('tasks_model', 'products_model', 'form')); ?>
        </div>
    </div>
    <?php
    $this->endWidget();
    ?>
</div>
