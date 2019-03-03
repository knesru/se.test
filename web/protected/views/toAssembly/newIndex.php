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
    isAdmin = <?php print ((Yii::app()->user->name=='admin')?'true':'false'); ?>;
    isGuest = <?php print ((Yii::app()->user->isGuest)?'true':'false'); ?>;
</script>
<?php
$cs->registerScriptFile($baseUrl . '/js/jquery-1.8.3.js');
$cs->registerScriptFile($baseUrl . '/js/jquery-ui-1.9.2.custom.min.js');
$cs->registerScriptFile($baseUrl . '/js/pq/pqgrid.min.js');
$cs->registerScriptFile($baseUrl . '/js/pq/pqselect.min.js');
$cs->registerScriptFile($baseUrl . '/js/common.js');
$cs->registerScriptFile($baseUrl . '/js/requestsTable.js',CClientScript::POS_END);
$cs->registerScriptFile($baseUrl . '/js/componentsTable.js',CClientScript::POS_END);
$cs->registerCssFile($baseUrl . '/js/pq/pqselect.bootstrap.min.css');
$cs->registerCssFile($baseUrl . '/js/pq/pqgrid.min.css');
$cs->registerCssFile($baseUrl . '/js/pq/pqselect.min.css');
$cs->registerCssFile($baseUrl . '/js/themes/office/pqgrid.css');
?>
<script type="application/javascript">

    $(function () {
        controlData.selection = [];
        controlData.prevSelection = null;
        controlData.requestSelection = [];
        $requestsGrid = $("#grid_requests").pqGrid(RequestsTable);

        $componentsGrid = $("#grid_new_components").pqGrid(ComponentsTable);
        $("#grid_requests").on("pqgridcollapse pqgridexpand", function (event, ui) {
            $("#grid_new_components").pqGrid('refreshDataAndView');
        });
        $("#popup-dialog-receive").dialog({
            width: 400, modal: true,
            open: function () {
                $(".ui-dialog").position({of: "#grid_requests"});
            },
            autoOpen: false
        });
        $('#storeid').autocomplete({
            //appendTo: ui.$cell, //for grid in maximized state.
            source: '/component/storesList',
            selectItem: {on: true}, //custom option
            highlightText: {on: true}, //custom option
            minLength: 0,
            focus: function (event, ui) {
                $("#storeid").val(ui.item.label);
                return false;
            },
            select: function (event, ui) {
                // console.log(a,b,c);
                $('#storeid').val(ui.item.label);
                $('input[name="storeid"]').val(ui.item.value);
                return false;
                // $.ajax({
                //     url: '/component/ajaxComponent',
                //     data: {partnumber: b.item.value},
                //     success: function (res) {
                //         if (typeof res === 'string') {
                //             $('#Extcomponents_partnumberid').val(res).addClass('success');
                //             $inp.parent().addClass('success');
                //         }
                //     }
                // });
            }

        }).focus(function () {
            $(this).autocomplete("search", "");
        }).click(function () {
            $(this).autocomplete("search", "");
        });
        function extractLast( term ) {
            return term.split( /,\s*/ ).pop();
        }
        $('#place').autocomplete({
            //appendTo: ui.$cell, //for grid in maximized state.
            source: function( request, response ) {
                $.getJSON( '/component/getPlace', {
                    term: extractLast( request.term ),
                    storeid: $('input[name="storeid"]').val(),
                    partnumberid: $('input[name="partnumberid"]').val()
                }, response );
            },
            selectItem: {on: true}, //custom option
            highlightText: {on: true}, //custom option
            minLength: 0,
            focus: function (event, ui) {
                $("#place").val(ui.item.label);
                return false;
            },
            select: function (event, ui) {
                // console.log(a,b,c);
                $('#place').val(ui.item.label);
                // $('input[name="storeid"]').val(ui.item.value);
                return false;
                // $.ajax({
                //     url: '/component/ajaxComponent',
                //     data: {partnumber: b.item.value},
                //     success: function (res) {
                //         if (typeof res === 'string') {
                //             $('#Extcomponents_partnumberid').val(res).addClass('success');
                //             $inp.parent().addClass('success');
                //         }
                //     }
                // });
            }
        }).focus(function () {
            $(this).autocomplete("search", "");
        }).click(function () {
            $(this).autocomplete("search", "");
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
                <td><span id="received_component"></span><input type="hidden" name="partnumberid" /></td>
            </tr>
            <tr>
                <td>Количество</td>
                <td><input type="number" name="amount" id="received_amount"/></td>
            </tr>
            <tr>
                <td>Склад</td>
                <td><input type="text" id="storeid"/><input type="hidden" name="storeid"></td>
            </tr>
            <tr>
                <td>Место</td>
                <td><input type="text" id="place"/></td>
            </tr>
            </tbody>
        </table>
    </form>
</div>
