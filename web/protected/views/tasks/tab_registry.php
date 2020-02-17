<?php
/**
 * @var $this TasksController
 * @var Tasks $tasks_model
 * @var Products $products_model
 */

$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile($baseUrl . '/js/jquery.js');
//$cs->registerScriptFile($baseUrl . '/js/jquery-ui.min.js');
//$cs->registerScriptFile($baseUrl . '/js/pq/pqgrid.min.js');
//$cs->registerScriptFile($baseUrl . '/js/common.js');
//$cs->registerCssFile($baseUrl . '/js/pq/pqgrid.min.css');
//$cs->registerCssFile($baseUrl . '/js/themes/office/pqgrid.css');
?>
<div id="grid_registry"></div>
<script type="application/javascript" src="../js/registryTable.js"
</script>

