<?php
/**
 * @var Tasks $tasks_model
 * @var Products $products_model
 * @var UActiveForm $form
 */
?>

    <p class="note">Поля, отмеченные <span class="required">*</span>, обязательны для заполнения.</p>

<?php echo $form->errorSummary($tasks_model); ?>

<?php

$row_params = array('class'=>'wide-field', 'maxlength' => 255);

$form->rowHiddenField($tasks_model, 'id', array(), 'wide-label');
$form->rowHiddenField($tasks_model, 'managerid', array(), 'wide-label');
$form->rowHiddenField($tasks_model, 'userid', array(), 'wide-label');

$form->rowTextField($tasks_model, 'name',                   $row_params, 'wide-label');
$form->rowTextField($tasks_model, 'customer',               $row_params, 'wide-label');
$form->rowTextField($tasks_model, 'user_name',              $row_params, 'wide-label');
$form->rowTextField($tasks_model, 'manager_name',           $row_params, 'wide-label');
$form->rowTextField($tasks_model, 'contract',               $row_params, 'wide-label');
$form->rowDateField($tasks_model, 'created_at',             $row_params, 'wide-label');
$form->rowDateField($tasks_model, 'delivery_date',          $row_params, 'wide-label');
$form->rowDateField($tasks_model, 'store_delivery_date',    $row_params, 'wide-label');
$form->rowTextField($tasks_model, 'inspection_type',        $row_params, 'wide-label');
$form->rowTextField($tasks_model, 'warranty',               $row_params, 'wide-label');
$form->rowTextArea ($tasks_model, 'notes',                  array('class'=>'wide-field'), 'wide-label');
$form->rowDateField($tasks_model, 'store_acceptance_date',  $row_params, 'wide-label');
$form->rowDateField($tasks_model, 'official_delivery_date', $row_params, 'wide-label');
$form->rowTextField($tasks_model, 'statusid',               $row_params, 'wide-label');
$form->rowDateField($tasks_model, 'updated_at',             $row_params, 'wide-label');





