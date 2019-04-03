function filterHandlerRequests(evt, ui) {
    var $toolbar = $requestsGrid.find('.pq-toolbar-search'),
        $value = $toolbar.find(".filterValue"),
        value = $value.val(),
        condition = 'contain',
        dataIndx = 'partnumber',
        filterObject;

    if (dataIndx == "") {//search through all fields when no field selected.
        filterObject = [];
        let CM = $requestsGrid.pqGrid("getColModel");
        for (let i = 0, len = CM.length; i < len; i++) {
            dataIndx = CM[i].dataIndx;
            filterObject.push({ dataIndx: dataIndx, condition: condition, value: value });
        }
    }
    else {//search through selected field.
        filterObject = [{ dataIndx: dataIndx, condition: condition, value: value}];
    }
    $requestsGrid.pqGrid("filter", {
        oper: 'replace',
        data: filterObject
    });
    $requestsGrid.pqGrid('refreshDataAndView');
}

function saveChangesRequests() {
    let grid = $requestsGrid.pqGrid('getInstance').grid;

    userLog('Правка строки в заявках','log');
    //debugger;
    //attempt to save editing cell.
    if (grid.saveEditCell() === false) {
        return false;
    }

    let isDirty = grid.isDirty();
    if (isDirty) {
        //validate the new added rows.
        let addList = grid.getChanges().addList;
        //debugger;
        for (let i = 0; i < addList.length; i++) {
            let rowData = addList[i];
            let isValid = grid.isValid({"rowData": rowData}).valid;
            if (!isValid) {
                return;
            }
        }
        let changes = grid.getChanges({format: "byVal"});
        let oldData = grid.getChanges({format: "raw"}).updateList[0].oldRow;
        let newData = grid.getChanges({format: "raw"}).updateList[0]['rowData'];
        if(typeof oldData['partnumber']!=="undefined" && newData['partnumber']!==oldData['partnumber']){
            if(!isNaN(newData['partnumberid']) && newData['partnumberid']!==null) {
                showMessage('Нельзя переименовать компонент из STMS. Но можно назначить замену.', 'warning');
                userLog('Нельзя переименовать компонент из STMS. Отмена действия', 'info');
                grid.rollback();
                return null;
            }else{
                if(!confirm('Действительно переименовать компонент?')){
                    userLog('Отменил переименование', 'log');
                    grid.rollback();
                    return null;
                }
            }
        }
        for (let x in oldData){
            if(oldData.hasOwnProperty(x)){
                if(typeof newData[x] !== 'undefined'){
                    let identifier = '';
                    identifier = newData['id'];
                    if(typeof grid.getColumn({dataIndx: x}) !== 'undefined') {
                        let oldval = oldData[x];
                        let newval = newData[x];
                        if(x==='status'){
                            let statuses = getStatusesArray();
                            for(let i=0;i<statuses.length;i++){
                                if(statuses[i].value===oldval){
                                    oldval = statuses[i].text;
                                }
                            }
                            for(let i=0;i<statuses.length;i++){
                                if(statuses[i].value===newval){
                                    newval = statuses[i].text;
                                }
                            }
                        }
                        if (x === 'priority') {
                            oldval = oldval?'высокий':'низкий';
                            newval = newval?'высокий':'низкий';
                        }
                        userLog('Поменял в строке ' + identifier + ' поле "' + grid.getColumn({dataIndx: x}).title + '": ' + oldval + ' -> ' + newval, 'log');
                    }
                }
            }
        }

        //post changes to server
        $.ajax({
            dataType: "json",
            type: "POST",
            async: true,
            beforeSend: function (jqXHR, settings) {
                grid.showLoading();
            },
            url: "/toAssembly/update", //for ASP.NET, java
            data: {list: JSON.stringify(changes)},
            success: function (changes) {
                generalAjaxAnswer(changes,true);
                if(typeof changes.success!=="undefined"){
                    if(changes.success===false){
                        grid.rollback();
                    }
                }
                //debugger;
                grid.commit();

                grid.history({method: 'reset'});
                grid.refresh();
            },
            complete: function () {
                grid.hideLoading();
            },
            error: function(err){
                userLog('Сохранение заявок не прошло','error');
                userLog(err.responseText,'error');
                grid.history({method: 'reset'});
                grid.rollback();
                grid.refresh();
            }
        });
    }
}

let RequestsTableColumnModel = [
    getIdColumn(),
    {
        title: "Заявка",
        dataIndx: 'requestid',
        dataType: "string",
        editable: false,
        render: function (ui) {
            let rowData = ui.rowData,
                dataIndx = ui.dataIndx;
            return rowData[dataIndx].replace(/^0+/, '');
        },
        sortType: function (rowData1, rowData2, dataIndx) {
            let val1 = rowData1[dataIndx],
                val2 = rowData2[dataIndx],
                data1 = $.trim(val1).split('.'),
                data2 = $.trim(val2).split('.');

            let c1 = parseInt(data1[0]),
                c2 = parseInt(data2[0]),
                y1 = parseInt(data1[2]),
                y2 = parseInt(data2[2]);

            if (y1 > y2 || (y1 === y2 && c1 > c2)) {
                return -1;
            }
            else if (y1 < y2 || (y1 === y2 && c1 < c2)) {
                return 1;
            }
            return 0;
        },
        filter: {
            type: 'textbox',
            condition: 'contain',
            listeners: ['change']
        }
    },
    getPartnumberColumn(),
    getPartnumberIdColumn(),
    getAmountColumn(),
    getUserColumn(),
    getPurposeColumn(),
    getCreated_atColumn(),
    {
        title: "Сдано",
        dataIndx: 'delivered',
        dataType: "integer",
        align: "right",
        filter: {
            type: 'textbox',
            condition: 'between',
            listeners: ['change']
        }
    },
    getAssembly_toColumn(),
    getInstall_toColumn(),
    getDeficiteColumn(),
    getDescriptionColumn(),
    getInstall_fromColumn(),
    getPriorityColumn(),
    getStatusColumn()
];
let RequestsTableDataModel = {
    recIndx: "id", //primary key
    location: "remote",
    sorting: "remote",
    dataType: "JSON",
    method: "POST",
    sortIndx: "priority",
    sortDir: "down",
    url: "/toAssembly/requestslist",
    getData: function (response) {
        return {curPage: response.curPage, totalRecords: response.totalRecords,data: response.data};
    },
    beforeSend: function (jqXHR, settings) {
        // console.log(jqXHR);
        // console.log(settings);
        if (settings.data.length > 0) {
            settings.data += '&';
        }
        settings.data += 'showall=' + !!$('#showAll').is(":checked");
    }
};
let RequestsTable = {
    scrollModel: {autoFit: true, horizontal: false},
    pageModel: getPageModel(),
    collapsible: false,
    resizable: true,
    stringify: false, //for PHP
    dataModel: RequestsTableDataModel,
    colModel: RequestsTableColumnModel,
    selectionModel: {
        type: 'row',
        mode: 'single',
        fireSelectChange: true
    },
    filterModel: {on: true, mode: "AND", header: false},
    toolbar: {
        cls: "pq-toolbar-search",
        items: [
            getClearFilterButton(),
            getFilterWord(),
            {
                type: 'checkbox',
                listeners: [{
                    'change': function () {
                        $requestsGrid.pqGrid("option", "filterModel.header", !!$(this).is(":checked"));
                        $requestsGrid.pqGrid("refresh");
                    }
                }]
            },
            {type: 'separator'},
            {
                type: 'textbox',
                attr: 'placeholder="быстрый поиск"',
                cls: "filterValue",
                listeners: [{'keyup': filterHandlerRequests}]
            },
            {type: 'separator'},
            {
                type: 'button', icon: 'ui-icon-check', cls: 'receive', label: 'Принять', listener:
                    {
                        "click": function (evt, ui) {
                            let grid = $requestsGrid.pqGrid('getInstance').grid;
                            let rowIndx = getRequestsSelectedRowIndx();
                            var row = $requestsGrid.pqGrid('getRowData', {rowIndx: rowIndx});

                            let $frm = $("form#receive-form");
                            let delta = row['amount'] - row['delivered'];
                            if (delta < 0) {
                                delta = 0;
                            }
                            $frm.find("input[name='amount']").val(delta);
                            $frm.find("#received_request").text(row['requestid'].replace(/^0+/, ''));
                            $frm.find("input[name='partnumberid']").val(row['partnumberid']);
                            $frm.find("#received_component").text(row['partnumber']);
                            if(isNaN(row['partnumberid']) || row['partnumberid']===null){
                                $frm.find("#storeid").attr('disabled','disabled');
                                $frm.find("#place").attr('disabled','disabled');
                            }else{
                                $frm.find("#storeid").removeAttr('disabled');
                                $frm.find("#place").removeAttr('disabled');
                            }
                            userLog('Принимаю компонент '+row['partnumber']+', строка '+row['id']+', заявка '+row.requestid+' на склад');
                            if(row.status==0 || row.status==5 || row.status==4){
                                userLog('Нельзя принять из этого статуса. Отменено','info');
                                return;
                            }
                            $("#popup-dialog-receive").dialog({
                                title: row['requestid'].replace(/^0+/, '') + ': ' + row['partnumber'], buttons: {
                                    "Принять": function () {
                                        userLog('Отправка формы');
                                        if ($frm.find("input[name='amount']").val() == row['amount'] - row['delivered']) {
                                            if (!confirm("Заявка удовлетворена. Компонент будет закрыт.")) {
                                                userLog('Отказался закрывать заявку. Отмена принятия компонента.');
                                                return;
                                            }
                                        }else if($frm.find("input[name='amount']").val() > row['amount'] - row['delivered']){
                                            if (!confirm("Количество принятых больше, чем заказано. Компонент будет" +
                                                " закрыт. Все равно принять?")) {
                                                userLog('Отказался закрывать заявку с количеством большим, чем осталось по заказу('+$frm.find("input[name='amount']").val()+'>'+(row['amount'] - row['delivered'])+'). Отмена принятия компонента.');
                                                return;
                                            }
                                        }
                                        let sendData = {
                                            requestid: row['id'],
                                            amount: $frm.find("input[name='amount']").val(),
                                            storeid: $frm.find("#storeid").val(),
                                            place: $frm.find("#place").val(),
                                            installerid: $frm.find("#installerid").val(),
                                            installername: $frm.find("#installerid").find(':selected').text()
                                        };

                                        userLog('Заполнил форму.');
                                        userLog('Строка:'+sendData.requestid);
                                        userLog('Заявка:'+row['requestid'].replace(/^0+/, ''));
                                        userLog('Компонент:'+row['partnumber']);
                                        userLog('Склад:'+sendData.storeid);
                                        userLog('Место:'+sendData.place);
                                        let isNewInstaller = ((sendData.installerid>0)?'':' (новый сборщик)');
                                        userLog('Сборщик:'+sendData.installername+isNewInstaller);
                                        // console.log($frm.find("input[name='installer']").val());
                                        // console.log($frm.find("input[name='installer']").val());
                                        $.ajax({
                                            dataType: "json",
                                            type: "POST",
                                            async: true,
                                            beforeSend: function (jqXHR, settings) {
                                                grid.showLoading();
                                            },
                                            url: "/toAssembly/receive", //for ASP.NET, java
                                            data: sendData,
                                            success: function (changes) {
                                                //debugger;
                                                if(typeof changes.success !== 'undefined') {
                                                    if(changes.success==true) {
                                                        userLog('Успешно');
                                                    }else{
                                                        userLog(changes.error);
                                                    }
                                                }
                                                grid.history({method: 'reset'});
                                            },
                                            error:function(err){
                                                userLog(err.responseText,'error');
                                            },
                                            complete: function () {
                                                grid.hideLoading();
                                                grid.refreshDataAndView();
                                            }
                                        });

                                        $(this).dialog("close");
                                    },
                                    "Отмена": function () {
                                        userLog('Закрыл окно.');
                                        $(this).dialog("close");
                                    }
                                }
                            }).dialog("open");


                        }
                    },
                options: {disabled: true}
            },
            {type: 'separator'},
            {
                type: "<span style='margin:5px;' title='Отображать закрытые и отмененные компоненты'>Все заявки</span>"
            },
            {
                type: 'checkbox',
                listeners: [{
                    'change': function () {
                        $(this).attr('id', 'showAll');
                        $requestsGrid.pqGrid('refreshDataAndView');
                    }
                }]
            },
            {type: 'separator'},
            {
                type: 'button',
                label: "Экспорт в Excel",
                icon: 'ui-icon-document',
                listeners: [{
                    "click": function (evt) {
                        let date1 = new Date();
                        userLog('Получаю экспорт таблицы заявок');
                        $requestsGrid.pqGrid("showLoading");
                        let initial_amount_of_iframes = $("body").find('iframe').length;
                        let stopit = setInterval(function(){
                            let date2 = new Date();
                            let diff = date2 - date1;
                            if (initial_amount_of_iframes != $("body").find('iframe').length){
                                clearInterval(stopit);
                                $requestsGrid.pqGrid("hideLoading");
                                let seconds_passed = Math.round(diff/100)/10;
                                userLog('Похоже, экспорт заявок сформирован за '+seconds_passed+'с');
                            }
                            if(diff>180000){
                                clearInterval(stopit);
                                $requestsGrid.pqGrid("hideLoading");
                                let seconds_passed = Math.round(diff*100)/100;
                                userLog('Прошло уже '+seconds_passed+'с, а экспорта заявок нет. Что-то пошло не так...','error');
                            }
                        },300);
                        $requestsGrid.pqGrid("exportCsv", {url: "/toAssembly/export", sheetName: "Заявки"});
                    }
                }]
            },
            {type: 'separator'},
            {
                type: 'button',
                label: "Замена",
                icon: 'ui-icon-transferthick-e-w',
                listeners: [{
                    "click": function (evt) {
                        let grid = $requestsGrid.pqGrid('getInstance').grid;
                        let rowIndx = getRequestsSelectedRowIndx();
                        if(rowIndx === null){
                            return;
                        }
                        let row = $requestsGrid.pqGrid('getRowData', {rowIndx: rowIndx});
                        userLog('Назначаю замену для компонента '+row['partnumber']+' в заявке '+row['requestid']+', строка '+row['id']);

                        let $frm = $("form#replace-form");
                        $frm.find("#replace_request").text(row['requestid'].replace(/^0+/, ''));
                        $frm.find("#old_component").text(row['partnumber']);

                        $("#popup-dialog-replace").dialog({
                            title: row['requestid'].replace(/^0+/, '') + ': ' + row['partnumber'], buttons: {
                                "Заменить": function () {
                                    let oldpn,newpn,requestid;
                                    oldpn = row['partnumber'];
                                    requestid = row['requestid'];
                                    newpn = $frm.find("input#replace_component").val();

                                    $.ajax({
                                        dataType: "json",
                                        type: "POST",
                                        async: true,
                                        beforeSend: function (jqXHR, settings) {
                                            grid.showLoading();
                                        },
                                        url: "/toAssembly/replace", //for ASP.NET, java
                                        data: {
                                            requestid: row['id'],
                                            partnumber: $frm.find("input#replace_component").val(),
                                            partnumberid: $frm.find("input#newpartnumberid").val()
                                        },
                                        success: function (changes) {
                                            if(typeof changes!=='undefined'){
                                                if(typeof changes.success!=="undefined"){
                                                    if(changes.success){
                                                        userLog('Компонент в заявке '+requestid+' успешно заменен: '+oldpn+' -> '+newpn+' новая строка '+changes.id);
                                                    }else{
                                                        userLog(changes.error,'error');
                                                    }
                                                }
                                            }
                                            //debugger;
                                            grid.history({method: 'reset'});
                                        },
                                        error: function(err){
                                            userLog(err,'error');
                                        },
                                        complete: function () {
                                            grid.hideLoading();
                                            grid.refreshDataAndView();
                                        }
                                    });

                                    $(this).dialog("close");
                                },
                                "Отмена": function () {
                                    $(this).dialog("close");
                                }
                            }
                        }).dialog("open");
                    }
                }]
            },
            {type: 'separator'},
            {
                type: 'button',
                label: ' ',
                icon: 'ui-icon-clock',
                listeners: [
                    {
                        'click': function(){
                            //popup_grid_store_correction
                            let rowIndx = getRequestsSelectedRowIndx();
                            if(rowIndx==null){
                                return;
                            }
                            let row = $requestsGrid.pqGrid('getRowData', {rowIndx: rowIndx});
                            $("#grid_store_correction").data('selectedComp',row['id']);
                            $("#popup_grid_store_correction")
                                .dialog({
                                    height: 400,
                                    width: 1000,
                                    //width: 'auto',
                                    modal: true,
                                    open: function (evt, ui) {
                                        $("#grid_store_correction").pqGrid(StoreCorrectionTable).pqGrid('refreshDataAndView');
                                    },
                                    close: function () {
                                        //$("#grid_store_correction").pqGrid('destroy');
                                    }
                                });
                        },
                    }
                ]
            }
        ]
    },
    history: function (evt, ui) {
        let $grid = $(this);
        if (ui.canUndo != null) {
            $("button.changes", $grid).button("option", {disabled: !ui.canUndo || isGuest});
        }
        if (ui.canRedo != null) {
            $("button:contains('Вернуть')", $grid).button("option", "disabled", !ui.canRedo || isGuest);
        }
        $("button:contains('Отменить')", $grid).button("option", {label: 'Отменить (' + ui.num_undo + ')'});
        $("button:contains('Вернуть')", $grid).button("option", {label: 'Вернуть (' + ui.num_redo + ')'});
    },
    editable: true,
    editor: {
        select: true
    },
    change: function(event, ui){
        //debugger;
        if (ui.source == 'commit' || ui.source == 'rollback') {
            return;
        }
        saveChangesRequests();
    },
    rowSelect: function( event, ui ) {
        $('.shorttext').addClass('folded-text');
        ui.$tr.find('.shorttext').removeClass('folded-text');
    },
    trackModel: {on: true},
    showTitle: false,
    numberCell: {show: false},
    columnBorders: true
};

function getRequestsSelectedRowIndx() {
    var arr = $requestsGrid.pqGrid("selection", {type: 'row', method: 'getSelection'});
    if (arr && arr.length > 0) {
        return arr[0].rowIndx;
    }
    else {
        showMessage("Выберите заявку");
        return null;
    }
}

RequestsTable.selectChange = function (evt, ui) {
    let rows = ui.rows;
    if (typeof controlData.requestSelection[0] !== 'undefined') {
        controlData.prevSelection = controlData.requestSelection[0];
    }
    controlData.requestSelection = [];
    if (rows && rows.length) {
        for (let i = 0; i < rows.length; i++) {
            // console.log(rows[i].rowData);
            controlData.requestSelection.push(rows[i].rowData.id);
        }
    }
    if (controlData.prevSelection === controlData.requestSelection[0]) {
        $("#grid_requests").pqGrid("setSelection", null);
        controlData.prevSelection = null;
    }
    if (controlData.requestSelection.length > 0) {
        //$('#requestbutton').button('option','label','Добавить в заявку');
        $("button.receive", $requestsGrid).button("option", {disabled: isGuest});
    } else {
        //$('#requestbutton').button('option','label','Создать заявку');
        $("button.receive", $requestsGrid).button("option", {disabled: true});
    }
};