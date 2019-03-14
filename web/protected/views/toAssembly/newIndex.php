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
//$min = '.min';
$min = '.dev';
$cs->registerScriptFile($baseUrl . '/js/jquery.js');
$cs->registerScriptFile($baseUrl . '/js/jquery-ui.min.js');
$cs->registerScriptFile($baseUrl . '/js/pq/pqgrid'.$min.'.js');
$cs->registerScriptFile($baseUrl . '/js/pq/pqselect.min.js');
$cs->registerScriptFile($baseUrl . '/js/common.js');
$cs->registerScriptFile($baseUrl . '/js/requestsTable.js',CClientScript::POS_END);
$cs->registerScriptFile($baseUrl . '/js/componentsTable.js',CClientScript::POS_END);
$cs->registerCssFile($baseUrl . '/js/pq/pqselect.bootstrap.min.css');
$cs->registerCssFile($baseUrl . '/js/pq/pqgrid'.$min.'.css');
$cs->registerCssFile($baseUrl . '/js/pq/pqselect'.$min.'.css');
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
        $("#popup-dialog-replace").dialog({
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
        $('.controlgroup').controlgroup();
        //$('.controlgroup button').button()/*.css('border-radius','4px 0px 0px 4px')*/;
        $('.ui-button').css('padding','3px').not('.ui-selectmenu-button').css({
            'padding-right':'5px'
        });
        $('.controlgroup select').selectmenu({
            classes: {
                "ui-selectmenu-button": "ui-button-icon-only splitbutton-select"
            },
            change: function(){
                $( ".output" ).append( "<li>" + this.value + "</li>" );
                $(this).parent().find('button').text($(this).find(':selected').text());
                //let action = $(this).val();
                //requestsAction(action);
            }
        })/*.css({
            'border-radius':'0px 4px 4px 0px',
            'margin-left':'-3px'
        })*/;
        $('.controlgroup button').click(function () {
            let action = $(this).parent().find('select').val();
            requestsAction(action);
        });
        $('.splitbutton-select').css('width','2em');
        function requestsAction(action) {
            alert(action);
            if (typeof controlData.selection !== 'undefined') {
                let datM = $("#grid_requests").pqGrid("option", "dataModel");
                let grid = $("#grid_requests").pqGrid();
                let data = {};
                data.ids = controlData.selection;
                if(action==='append'){
                    if(controlData.requestSelection.length==0){
                        alert('Выберие заявку для добавления');
                        return;
                    }
                    data.requestid = controlData.requestSelection;
                    let rowIndx = getRowIndx();
                    var row = $requestsGrid.pqGrid('getRowData', {rowIndx: rowIndx});
                    if(!confirm('Добавить компоненты к заявке '+row['requestid'].replace(/^0+/, '')+'?')){
                        return;
                    }
                }
                $.ajax({
                    url: '/toassembly/request',
                    data: data,
                    dataType: "json",
                    type: "POST",
                    async: true,
                    beforeSend: function (jqXHR, settings) {
                        $(".saving", grid).show();
                    },
                    success: function () {
                        //commit the changes.
                        $("#grid_requests").pqGrid('refreshDataAndView');
                        $("#grid_new_components").pqGrid('refreshDataAndView');
                    },
                    complete: function () {
                        $(".saving", grid).hide();
                    }
                });
            }
        }

        $(function(){
            $inp = $('#replace_component');
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
                                $('#newpartnumberid').val(res).addClass('success');
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
        $( document ).tooltip();

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
<div id="popup-dialog-replace" style="display:none;">
    <form id="replace-form">
        <table align="center">
            <tbody>
            <tr>
                <td>Заявка</td>
                <td><span id="replace_request"></span></td>
            </tr>
            <tr>
                <td>Компонент</td>
                <td><span id="old_component"></span></td>
            </tr>
            <tr>
                <td><label for="replace_component">Замена</label><span class="ui-icon ui-icon-help" title="Принцип
                работы: будет создан новый элемент в
                    данной заявке с количеством, равным непринятому остатку. У заменяемого компонента будет выставлен
                     статус «Отмена»"></span></td>
                <td><input type="text" name="replace_component" id="replace_component"/>
                    <input type="hidden" id="newpartnumberid"
                           name="newpartnumberid"
                    /></td>
            </tr>
            </tbody>
        </table>
    </form>
</div>
