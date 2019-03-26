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
    function getStatusesArray() {
        return [
            {"value": 0, "text": 'Не активен'},
            {"value": 1, "text": 'Комплектация'},
            {"value": 2, "text": 'Скомпонован'},
            {"value": 3, "text": 'На монтаже'},
            {"value": 4, "text": 'Закрыт'},
            {"value": 5, "text": 'Отмена'}
        ];
    }
</script>
<?php
//$min = '.min';
$min = '.dev';
$cs->registerScriptFile($baseUrl . '/js/jquery.js');
$cs->registerScriptFile($baseUrl . '/js/jquery-ui.min.js');
$cs->registerScriptFile($baseUrl . '/js/pq/pqgrid'.$min.'.js');
$cs->registerScriptFile($baseUrl . '/js/pq/pqselect.min.js');
$cs->registerScriptFile($baseUrl . '/js/common.js');
$cs->registerScriptFile($baseUrl . '/js/commonColumns.js',CClientScript::POS_END);
$cs->registerScriptFile($baseUrl . '/js/requestsTable.js',CClientScript::POS_END);
$cs->registerScriptFile($baseUrl . '/js/componentsTable.js',CClientScript::POS_END);
$cs->registerCssFile($baseUrl . '/js/pq/pqselect.bootstrap.min.css');
$cs->registerCssFile($baseUrl . '/js/pq/pqgrid'.$min.'.css');
$cs->registerCssFile($baseUrl . '/js/pq/pqselect.min.css');
$cs->registerCssFile($baseUrl . '/js/pq/themes/office/pqgrid.css');
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
        $("#popup-dialog-form-new-component").dialog({
            width: 400, modal: false,
            open: function () {
                //$(".ui-dialog").position({of: "#grid_requests"});
            },
            autoOpen: false
        });
        $("#popup-dialog-settings").dialog({
            width: 400, modal: false,
            autoOpen: false
        });
        $('#open_settigs_menu').click(function(){
            $("#popup-dialog-settings").dialog('open');
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
                // $(this).parent().find('button').text($(this).find(':selected').text());
                let action = $(this).val();
                requestsAction(action);
            }
        })/*.css({
            'border-radius':'0px 4px 4px 0px',
            'margin-left':'-3px'
        })*/;
        $('.controlgroup button').click(function () {
            let action = 'create';
            requestsAction(action);
        });
        $('.splitbutton-select').css('width','2em');


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
    function showMessage(message, type) {
        if(typeof type === 'undefined'){
            type = 'info';
        }
        showDialogMessage({title: type, message: message});
    }
    function showWarning(message) {
        showMessage(message,'warning');
    }
    function showError(message) {
        showMessage(message,'error');
    }
    function showDialogMessage(params) {
        defaultParams = {
            header: 'info',
            type: 'info',
            message: 'info',
            buttons: {
                ok: function () {
                    $(this).dialog("close");
                }
            }
        };
        params = $.extend(defaultParams,params);
        $("#popup-dialog-message").html(params.message).removeClass('ui-state-error').removeClass('ui-state-highlight');
        if(params.type==='warning'){
            $("#popup-dialog-message").addClass('ui-state-highlight');
        }
        if(params.type==='error'){
            $("#popup-dialog-message").addClass('ui-state-error');
        }
        $("#popup-dialog-message").dialog({
            title: params.title,
            buttons: params.buttons,
            modal: true
            // dialogClass: "ui-state-highlight",
            // classes: {
            //     "ui-dialog": "ui-state-highlight",
            //     "ui-dialog-title": "ui-state-highlight"
            // }
        }).dialog("open");
    }

    function requestsAction(action,force_id) {
        userLog(force_id);
        if (typeof controlData.selection !== 'undefined') {
            // console.log(controlData.selection);
            let grid = $("#grid_requests").pqGrid();
            let data = {};
            data.ids = controlData.selection;
            let components = '';
            let pns = [];
            let selectedCompRowsIndexes = getSelectedCompsRowsIndx(true);
            if(selectedCompRowsIndexes.length===0 && typeof force_id!=='undefined'){
                selectedCompRowsIndexes = [force_id];
            }
            if(selectedCompRowsIndexes.length===0){
                showWarning('Не выбран компонент для создания заявки.');
                return;
            }
            let compsRow = null;

            for (let i=0; i<selectedCompRowsIndexes.length; i++){
                compsRow = $componentsGrid.pqGrid('getRowData', {rowIndx: selectedCompRowsIndexes[i]});
                pns.push(compsRow.partnumber);
            }


            if(action==='append'){
                if(controlData.requestSelection.length==0){
                    showWarning('Выберие заявку для добавления');
                    return;
                }
                let rowIndx = getRequestsSelectedRowIndx();
                let row = $requestsGrid.pqGrid('getRowData', {rowIndx: rowIndx});
                if(pns.length===1){
                    components = 'компонент';
                }else{
                    components = 'компоненты';
                }
                userLog('Добавляю '+components+' '+pns.join(',')+' к заявке '+row['requestid'].replace(/^0+/, ''));
                data.requestid = controlData.requestSelection;
                if(!confirm('Добавить компоненты к заявке '+row['requestid'].replace(/^0+/, '')+'?')){
                    userLog('Отменил');
                    return;
                }
            }else{
                if(pns.length===1){
                    components = 'компонентом';
                }else{
                    components = 'компонентами';
                }
                userLog('Создаю заявку с '+components+' '+pns.join(','));
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
                success: function (result) {
                    if(result && typeof result.success !== 'undefined'){
                        if(action==='append'){
                            userLog('Успешно добавлены '+components+' '+pns.join(',')+' к заявке '+result.requestid.replace(/^0+/, ''));
                        }else{
                            userLog('Успешно создана новая заявка '+result.requestid.replace(/^0+/, '')+' с '+components+' '+pns.join(','));
                        }
                    }else if(typeof result.error !== 'undefined'){
                        userLog('Произошла ошибка '+result.error);
                    }
                    //commit the changes.
                    $("#grid_requests").pqGrid('refreshDataAndView');
                    $("#grid_new_components").pqGrid('refreshDataAndView');

                },
                complete: function () {
                    $(".saving", grid).hide();
                },
                error(err){
                    userLog(err.responseText,'error');
                }
            });
        }
    }
</script>
<?php

?>
<div id="grid_requests"></div>
<div id="grid_new_components" style="height: 300px"></div>
<div id="grid_history"></div>
<div id="popup-dialog-receive" style="display:none;">
    <form id="receive-form">
        <div class="form">
        <table align="center">
            <tbody>
            <tr>
                <td><label for="received_request">Заявка</label></td>
                <td><span id="received_request"></span></td>
            </tr>
            <tr>
                <td><label for="received_component">Компонент</label></td>
                <td><span id="received_component"></span><input type="hidden" name="partnumberid" /></td>
            </tr>
            <tr>
                <td><label for="received_amount">Количество</label></td>
                <td><input type="number" name="amount" id="received_amount"/></td>
            </tr>
            <tr>
                <td><label for="storeid">Склад<span
                                class="required">*</span><span class="ui-icon ui-icon-help" title="Для произвольного компонента склад будет проигнорирован"></span></label></td>
                <td><?php print CHtml::dropDownList('storeid', '', CHtml::listData(Storelist::model()->findAll(),'storeid','name')); ?></td>
            </tr>
            <tr>
                <td><label for="place">Место<span class="ui-icon ui-icon-help" title="Для произвольного компонента место будет проигнорировано"></span></label></td>
                <td><input type="text" id="place"/></td>
            </tr>
            <tr>
                <td><label for="installer">Сборщик<span
                                class="required">*</span></label></td>
                <td><?php print CHtml::dropDownList('installerid', '', CHtml::listData(Installer::model()->findAll(),'id','name')); ?></td>
            </tr>
            </tbody>
        </table>
        </div>
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

<div id="popup-dialog-message"></div>

<div id="popup-dialog-form-new-component" style="display: none;">
    <?php
        $this->renderPartial('_form_new_comp');
    ?>
</div>
<div id="popup-dialog-settings" style="display: none;">
    <?php
        $this->renderPartial('_form_settings');
    ?>
</div>
