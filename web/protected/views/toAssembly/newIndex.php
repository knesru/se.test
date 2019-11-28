<?php
/* @var $this ToAssemblyController */
/* @var $dataProviderRequests CActiveDataProvider */
/* @var $dataProviderAssemblies CActiveDataProvider */

$this->setPageTitle('Задания в производство');

$this->breadcrumbs = array(
    'Задания в производство',
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
    let $storeCorrectionGrid;
    let formHashes = {};
    isAdmin = <?php print ((Yii::app()->user->name=='admin')?'true':'false'); ?>;
    isGuest = <?php print ((Yii::app()->user->isGuest)?'true':'false'); ?>;
    function getStatusesArray() {
        return [<?php
                $out = array();
            foreach (Extcomponents::getStatuses() as $status=>$label) {
                $out[] = sprintf('{"value": %d, "text": "%s"}'."\n",$status, $label);
            }
            print implode(',',$out);
            ?>
        ];
    }
    function getStatusesMatrix() {
        return <?php print json_encode(Extcomponents::getStatusesMatrix());?>;
    }
    function canChangeStatus(to, from)
    {
        if((''+from)===(''+to)){
            return true;
        }

        to = ''+to;
        from = ''+from;

        let matrix = getStatusesMatrix();
        if(typeof matrix[from] === "undefined"){
            return false;
        }
        if(typeof matrix[from][to] === "undefined"){
            return false;
        }
        return matrix[from][to]==='allow';
    }
    function getUsersArray() {
        return <?php
            $criteria = new CDbCriteria();
            $criteria->with = array(
                'userinfo' => array('together' => true,),
            );
            $criteria->order = 'userinfo.fullname, t.username';
            $listUsers = array();
            $listUsersModels = User::model()->findAll($criteria);
            foreach ($listUsersModels as $userModel) {
                $username = $userModel->username;
                if(!empty($userModel->userinfo->fullname)){
                    $username = $userModel->userinfo->fullname;
                }
                $listUsers[] = array('value'=>$username,'text'=>$username);
            }
            print json_encode($listUsers);
            ?>;
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
$cs->registerScriptFile($baseUrl . '/js/storeCorrectionTable.js',CClientScript::POS_END);
$cs->registerScriptFile($baseUrl . '/js/pq/localize/pq-localize-ru.js',CClientScript::POS_END);
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
        $requestsGrid.find(".pq-pager").pqPager("option", $.paramquery.pqPager.regional['ru']);
        $componentsGrid = $("#grid_new_components").pqGrid(ComponentsTable);
        $componentsGrid.find(".pq-pager").pqPager("option", $.paramquery.pqPager.regional['ru']);
        $storeCorrectionGrid = $("#grid_store_correction").pqGrid(StoreCorrectionTable);
        $storeCorrectionGrid.find(".pq-pager").pqPager("option", $.paramquery.pqPager.regional['ru']);

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
            width: 800,
            modal: false,
            autoOpen: false
        });
        $('#open_settigs_menu').click(function(){
            $("#popup-dialog-settings").dialog('open');
        });
        $('#open_popup_login').click(function(e){
            $("#popup-dialog-login").dialog({
                title:'Вход в систему',
                buttons: {
                    'Войти': function () {
                        $.ajax({
                            url: '/site/login',
                            type: "POST",
                            dataType: "json",
                            data: {LoginForm:{username: $('[name="popop-login"]').val(), password:$('[name="popop-password"]').val()}},
                            success: function (res) {
                                if(generalAjaxAnswer(res,true)){
                                    window.location.reload();
                                }
                            }
                        });

                    }
                }}).dialog('open');
            e.stopImmediatePropagation();
            e.stopPropagation();
            return false;
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
            select: function(){
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
            }).keyup(function(){
            $(this).addClass('warning').attr('title','Произвольный компонент').tooltip();
            $('#fnc_partnumberid').val('');
            $.ajax({
                url: '/component/ajaxComponent',
                data: {partnumber: $(this).val()},
                success: function (res) {
                    if (typeof res === 'string' && res.match(/\d+/)) {
                        $('#newpartnumberid').val(res).addClass('success');
                        $('#replace_component').removeClass('warning').addClass('success').attr('title','Компонент ' +
                            'найден в STMS')
                            .tooltip();
                    }else{
                        userLog('component not');
                    }
                }
            });
        });
        });
        $( document ).tooltip({
            tooltipClass: "toolTipDetails"
        });
        $componentsGrid.one("pqgridload", function (evt, ui) {
            $('#ss_rollback').click();
            loadUserHistory();
            // $componentsGrid.pqGrid("option", $.paramquery.pqGrid.regional['ru']).pqGrid("refresh");
            // $requestsGrid.pqGrid("option", $.paramquery.pqGrid.regional['ru']).pqGrid("refresh");
            // $storeCorrectionGrid.pqGrid("option", $.paramquery.pqGrid.regional['ru']).pqGrid("refresh");
        });

        // $("#grid_requests")
        //     .on('click','.delete_component_btn',deleteRow)
        //     .on('click','.create_request_btn',createRow);
        $("#grid_requests")
            .on('click','.change-priority-down',changePriority)
            .on('click','.change-priority-up',changePriority);
        $("#grid_new_components")
            .on('click','.delete_component_btn',deleteRow)
            .on('click','.change-priority-down',changePriority)
            .on('click','.change-priority-up',changePriority)
            .on('click','.create_request_btn',createRow);
        setInterval(function(){
            function changeHeights(fh, nh, rh) {
                $('#footer').height(fh);
                $('#grid_new_components').pqGrid('option',{height:nh}).pqGrid( "refresh" );
                $('#grid_requests').pqGrid('option',{height:rh}).pqGrid( "refresh" );
            }
            let delta;
            let total = $('body').height()-80;
            let footer_height = $('#footer').height();
            let nc_height = $('#grid_new_components').height();
            let req_height = $('#grid_requests').height();
            if(typeof $('#footer').data('heights') !== "undefined"){
                let prev_h = $('#footer').data('heights');
                delta = total-(footer_height+nc_height+req_height);
                if(Math.abs(prev_h.fh-footer_height)>1){
                    //changed footer
                    nc_height+=Math.floor(delta/2);
                    req_height+=delta-Math.floor(delta/2);
                    changeHeights(footer_height,nc_height,req_height);
                }else
                if(Math.abs(prev_h.nh-nc_height)>1){
                    //changed new components
                    footer_height+=Math.floor(delta);
                    //req_height+=delta-Math.floor(delta/2);
                    changeHeights(footer_height,nc_height,req_height);
                }else
                if(Math.abs(prev_h.rh-req_height)>1 || Math.abs($('#footer').data('total')-total)>3){
                    //changed requests
                    nc_height+=Math.floor(delta);
                    //footer_height+=Math.floor(delta);
                    changeHeights(footer_height,nc_height,req_height);
                }
            }else if(Math.abs($('#footer').data('total')-total)>3){
                nc_height+=Math.floor(delta/2);
                footer_height+=delta-Math.floor(delta/2);
                changeHeights(footer_height,nc_height,req_height);
            }
            $('#footer').data('total',total);
            $('#footer').data('heights',{fh:footer_height,nh:nc_height,rh:req_height});
        },200);
        $('#popup-password, #popup-login').keypress(function (e) {
            var key = e.which;
            if(key == 13)  // the enter key code
            {
                $('#popup-dialog-login').parent().find('.ui-dialog-buttonset').find('button').click();
                return false;
            }
        });
        setInterval(function(){
            $.ajax({
                url: '/toAssembly/checkLogin',
                data: {},
                success: function (res) {
                    if(!res || !res.logged){
                        $('#open_popup_login a').click();
                    }
                }
            })
        },1000);
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
            title: tdt(params.title),
            buttons: params.buttons,
            modal: true
            // dialogClass: "ui-state-highlight",
            // classes: {
            //     "ui-dialog": "ui-state-highlight",
            //     "ui-dialog-title": "ui-state-highlight"
            // }
        }).dialog("open");
    }
    //translate dialog title
    function tdt(title) {
        let titles = {
            'error': 'ошибка',
            'info': 'инфо',
            'warning': 'предупреждение',
        };
        if(typeof titles[title] !== 'undefined'){
            return titles[title];
        }
        return title;
    }

    function requestsAction(action,force_id) {
        // userLog(force_id);
        if (typeof controlData.selection !== 'undefined') {
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
                // userLog('Добавляю '+components+' '+pns.join(',')+' к заявке '+row['requestid'].replace(/^0+/, ''));
                data.requestid = controlData.requestSelection;
                if(!confirm('Добавить компоненты к заявке '+row['requestid'].replace(/^0+/, '')+'?')){
                    // userLog('Отменил');
                    return;
                }
            }else{
                if(pns.length===1){
                    components = 'компонентом';
                }else{
                    components = 'компонентами';
                }
                // userLog('Создаю заявку с '+components+' '+pns.join(','));
            }
            $.ajax({
                url: '/toAssembly/request',
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
            <tr><td colspan="2" id="random_component_info"></td></tr>
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

<div title="История коррекции на складе" id="popup_grid_store_correction" style="overflow:hidden; display: none">
    <div id="grid_store_correction"></div>
</div>

<div id="popup-dialog-login" style="display: none;">
    <div class="form">
        <form id="login-form">
            <table align="center">
                <tbody>
                    <tr>
                        <td><label for="popup-login">Логин</label></td>
                        <td><input type="text" name="popop-login" id="popup-login"/></td>
                    </tr>
                    <tr>
                        <td><label for="popup-password">Пароль</label></td>
                        <td><input type="password" name="popop-password" id="popup-password"/></td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div><!-- form -->
</div>
