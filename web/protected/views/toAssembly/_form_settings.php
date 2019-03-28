<form id="ss_form">
    <div class="form">
        <table>
            <tr id="ss_reqs_block">
                <th style="text-align: center">Заявки</th><th style="text-align: center">Компоненты</th>
            </tr>
            <tr id="ss_buttons_block">
                <td colspan="2"  style="text-align: center"><button type="button" id="ss_rollback">Загрузить</button><button type="button" id="ss_submit">Сохранить</button></td>
            </tr>
        </table>
    </div>
</form><!-- form -->
<script type="application/javascript">
    $(function(){
        let $form = $('#ss_form');
        let rcm = $requestsGrid.pqGrid( "option", "colModel" );
        let ccm = $componentsGrid.pqGrid( "option", "colModel" );

        for(let i=0; i<Math.max(rcm.length,ccm.length); i++){
            let rcm_column = '';
            let ccm_column = '';
            if(typeof rcm[i] !== 'undefined' && rcm[i].title!=='') {
                rcm_column = '<label><input class="ss_column_switch requests_switch" '+(rcm[i].hidden?'':'checked="checked"')+' name="r_'+rcm[i].dataIndx+'" type="checkbox"/> ' + rcm[i].title + '</label>';
            }
            if(typeof ccm[i] !== 'undefined' && ccm[i].title!=='') {
                ccm_column = '<label><input class="ss_column_switch components_switch" '+(ccm[i].hidden?'':'checked="checked"')+' name="c_'+ccm[i].dataIndx+'" type="checkbox"/> ' + ccm[i].title + '</label>';
            }
            $('#ss_buttons_block').before('<tr><td>'+rcm_column+'</td><td>'+ccm_column+'</td></tr>');
        }
        $('.ss_column_switch').change(function(){
            let rcm = $requestsGrid.pqGrid( "getColModel" );
            let ccm = $componentsGrid.pqGrid( "getColModel" );

            if($(this).hasClass('requests_switch')){
                for (let i = 0; i<rcm.length; i++){
                    if('r_'+rcm[i].dataIndx===$(this).attr('name')){
                        rcm[i].hidden = !$(this).is(":checked");
                        $requestsGrid.pqGrid('refresh');
                    }
                }
            }else if($(this).hasClass('components_switch')){
                for (let i = 0; i<ccm.length; i++){
                    if('c_'+ccm[i].dataIndx===$(this).attr('name')){
                        ccm[i].hidden = !$(this).is(":checked");
                        $componentsGrid.pqGrid('refresh');
                    }
                }
            }
        });
        $('#ss_submit').click(function(){
            let data = {'rcm':[],'ccm':[]};
            let rcm = $requestsGrid.pqGrid( "option", "colModel" );
            let ccm = $componentsGrid.pqGrid( "option", "colModel" );

            for (let i = 0; i < rcm.length; i++) {
                let hidden = false;
                if(typeof rcm[i].hidden!=="undefined"){
                    hidden = rcm[i].hidden;
                }
                data.rcm[i] = {
                    dataIndx: rcm[i].dataIndx,
                    hidden: hidden,
                    width: rcm[i].width
                }
            }
            for (let i = 0; i < ccm.length; i++) {
                let hidden = false;
                if(typeof ccm[i].hidden!=="undefined"){
                    hidden = ccm[i].hidden;
                }
                data.ccm[i] = {
                    dataIndx: ccm[i].dataIndx,
                    hidden: hidden,
                    width: ccm[i].width
                }
            }

            data.rpm = $requestsGrid.pqGrid( "option" ,'pageModel');
            data.cpm = $componentsGrid.pqGrid( "option" ,'pageModel');
            $.ajax({
                dataType: "json",
                type: "POST",
                async: true,
                url: "/settings/save", //for ASP.NET, java
                data: {name: 'to_assembly', data: data},
                success: function (result) {
                    generalAjaxAnswer(result);
                },
                error: function(err){
                    userLog(err.responseText,'error');
                }
            });
        });
        $('#ss_rollback').click(function(){
            $.ajax({
                dataType: "json",
                type: "POST",
                async: true,
                beforeSend: function (jqXHR, settings) {
                    //grid.showLoading();
                },
                url: "/settings/load", //for ASP.NET, java
                data: {name: 'to_assembly'},
                success: function (result) {
                    if (generalAjaxAnswer(result)) {
                        if (typeof result.data !== "undefined" && result.data != null) {
                            let data = result.data;
                            let rcm = $requestsGrid.pqGrid("option", "colModel");
                            let rcm_new = [];
                            let ccm = $componentsGrid.pqGrid("option", "colModel");
                            let ccm_new = [];

                            function findInCm(cm, dataIndx) {
                                for (let i = 0; i < cm.length; i++) {
                                    if (cm[i].dataIndx === dataIndx) {
                                        return cm[i];
                                    }
                                }
                                userLog('Не распознан индекс колонки ' + dataIndx, 'error');
                                return false;
                            }

                            for (let i = 0; i < data.rcm.length; i++) {
                                let cm = {};
                                data.rcm[i].hidden = data.rcm[i].hidden === 'true' || data.rcm[i].hidden === true;
                                if (cm = findInCm(rcm, data.rcm[i].dataIndx)) {
                                    cm.hidden = data.rcm[i].hidden === 'true';
                                    cm.width = data.rcm[i].width;
                                    rcm_new.push(cm);
                                    $("input[name='r_" + data.rcm[i].dataIndx + "']").prop('checked', !cm.hidden);
                                }

                            }
                            for (let i = 0; i < data.ccm.length; i++) {
                                let cm = {};
                                data.ccm[i].hidden = data.ccm[i].hidden === 'true' || data.ccm[i].hidden === true;
                                if (cm = findInCm(ccm, data.ccm[i].dataIndx)) {
                                    cm.hidden = data.ccm[i].hidden;
                                    cm.width = data.ccm[i].width;
                                    ccm_new.push(cm);
                                    $("input[name='c_" + data.ccm[i].dataIndx + "']").prop('checked', !cm.hidden);
                                }

                            }
                            $requestsGrid
                                .pqGrid("option", 'colModel', rcm_new)
                                .pqGrid("option", 'pageModel', data.rpm).pqGrid("refresh");
                            $componentsGrid
                                .pqGrid("option", 'colModel', ccm_new)
                                .pqGrid("option", 'pageModel', data.cpm).pqGrid("refresh");
                        }

                    }
                },
                error: function(err){
                    userLog(err.responseText,'error');
                },
                complete: function () {
                    //grid.hideLoading();
                }
            });
        });
    });

</script>