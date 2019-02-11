<?php
/* @var $this ToAssemblyController */
/* @var $dataProviderRequests CActiveDataProvider */
/* @var $dataProviderAssemblies CActiveDataProvider */

$this->breadcrumbs = array(
    'Extcomponents',
);

$this->menu = array(
    array('label' => 'Create Extcomponents', 'url' => array('create')),
    array('label' => 'Manage Extcomponents', 'url' => array('admin')),
);
$baseUrl = Yii::app()->baseUrl;
/** @var CClientScript $cs */
$cs = Yii::app()->getClientScript();
?>
<script type="application/javascript">
    let controlData = {};
    let $requestsGrid;
    let $componentsGrid;
</script>
<?php
$cs->registerScriptFile($baseUrl . '/js/jquery-1.8.3.js');
$cs->registerScriptFile($baseUrl . '/js/jquery-ui-1.9.2.custom.min.js');
$cs->registerScriptFile($baseUrl . '/js/pq/pqgrid.min.js');
$cs->registerScriptFile($baseUrl . '/js/pq/pqselect.min.js');
$cs->registerScriptFile($baseUrl . '/js/common.js');
$cs->registerScriptFile($baseUrl . '/js/requestsTable.js');
$cs->registerScriptFile($baseUrl . '/js/componentsTable.js');
$cs->registerCssFile($baseUrl . '/js/pq/pqselect.bootstrap.min.css');
$cs->registerCssFile($baseUrl . '/js/pq/pqgrid.min.css');
$cs->registerCssFile($baseUrl . '/js/pq/pqselect.min.css');
$cs->registerCssFile($baseUrl . '/js/themes/office/pqgrid.css');
?>
<script type="application/javascript">

    $(function () {
        controlData.selection = [];
        controlData.prevSelection=null;
        controlData.requestSelection = [];
        $requestsGrid = $("#grid_requests").pqGrid(RequestsTable);
        $componentsGrid = $("#grid_new_components").pqGrid(ComponentsTable);
        $("#popup-dialog-receive").dialog({ width: 400, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#grid_requests" }); },
            autoOpen: false
        });
    });
</script>
<?php

?>
<div id="grid_requests"></div>
<div id="grid_new_components" style="height: 300px"></div>
<div id="popup-dialog-receive" style="display:none;">
    <form id="receive-form">
        <table align="center">
            <tbody>
            <tr>
                <td>Заявка</td>
                <td><span id="received_request"></span></td>
            </tr>
            <tr>
                <td>Компонент</td>
                <td><span id="received_component"></span></td>
            </tr>
            <tr>
                <td>Количество</td>
                <td><input type="number" name="amount" id="received_amount" /></td>
            </tr>
            <tr>
                <td>Склад</td>
                <td><input type="number" name="storeid" /></td>
            </tr>
            </tbody></table>
    </form>
</div>
