function filterHandlerComponents(evt, ui) {
    var $toolbar = $componentsGrid.find('.pq-toolbar-search'),
        $value = $toolbar.find(".filterValue"),
        value = $value.val(),
        condition = 'contain',
        dataIndx = 'partnumber',
        filterObject;

    if (dataIndx == "") {//search through all fields when no field selected.
        filterObject = [];
        let CM = $componentsGrid.pqGrid("getColModel");
        for (let i = 0, len = CM.length; i < len; i++) {
            dataIndx = CM[i].dataIndx;
            filterObject.push({ dataIndx: dataIndx, condition: condition, value: value });
        }
    }
    else {//search through selected field.
        filterObject = [{ dataIndx: dataIndx, condition: condition, value: value}];
    }
    $componentsGrid.pqGrid("filter", {
        oper: 'replace',
        data: filterObject
    });
    $requestsGrid.pqGrid('refreshDataAndView');
}



function saveChangesComponents() {
    let grid = $componentsGrid.pqGrid('getInstance').grid;
    //userLog('Правка строки в компонентах');
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
        let newData;
        let oldData = [];
        if(grid.getChanges({format: "raw"}).updateList.length===0){
            userLog('Создал новый компонент','log');
            newData = grid.getChanges({format: "raw"}).addList;
        }else {
            newData = grid.getChanges({format: "raw"}).updateList[0]['rowData'];
            oldData = grid.getChanges({format: "raw"}).updateList[0].oldRow;
            if(typeof oldData['partnumber']!=="undefined" && newData['partnumber']!==oldData['partnumber']){
                if(!isNaN(newData['partnumberid']) && newData['partnumberid']!==null) {
                    showMessage('Нельзя переименовать компонент из STMS', 'warning');
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
        }



        for (let x in newData) {
            if (newData.hasOwnProperty(x)) {
                let identifier = '';
                identifier = newData['id'];
                if (typeof grid.getColumn({dataIndx: x}) !== 'undefined') {
                    let oldval = oldData[x];
                    let newval = newData[x];
                    if (x === 'status') {
                        let statuses = getStatusesArray();
                        for (let i = 0; i < statuses.length; i++) {
                            if (statuses[i].value === oldval) {
                                oldval = statuses[i].text;
                            }
                        }
                        for (let i = 0; i < statuses.length; i++) {
                            if (statuses[i].value === newval) {
                                newval = statuses[i].text;
                            }
                        }
                    }
                    if (x === 'priority') {
                        if(typeof oldval !== 'undefined') {
                            oldval = oldval ? 'высокий' : 'низкий';
                        }
                        newval = newval?'высокий':'низкий';
                    }
                     if(typeof oldval !== 'undefined') {
                        userLog('Поменял в строке ' + identifier + ' у компонента '+newData['partnumber']+' поле «' + grid.getColumn({dataIndx: x}).title + '»: ' + oldval + ' -> ' + newval, 'log');
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
                if(typeof changes.data !== 'undefined'){
                    let rowIndx = $componentsGrid.pqGrid('getRowIndx',{rowData:changes.data[0].id});
                    $componentsGrid.pqGrid("goToPage", { rowIndx: rowIndx });
                    $componentsGrid.pqGrid("setSelection", null);
                    $componentsGrid.pqGrid("setSelection", { rowIndx: rowIndx, dataIndx: 'partnumber' });
                    $componentsGrid.pqGrid("editFirstCellInRow", { rowIndx: rowIndx });
                    return;
                }
                //debugger;
                grid.commit({type: 'add', rows: changes.addList});
                grid.commit({type: 'update', rows: changes.updateList});
                grid.commit({type: 'delete', rows: changes.deleteList});

                grid.history({method: 'reset'});
                grid.refresh();
            },
            error: function(err){
                userLog(err.responseText,'error');
            },
            complete: function () {
                grid.hideLoading();
            }
        });
    }
}

let ComponentsTableColumnModel = [
    getIdColumn(),
    {
        title: "Действия",
        dataIndx: 'requestid',
        dataType: "string",
        cls:'buttons-here',
        minWidth: 80,
        maxWidth: 80,
        editable: false,
        sortable: false,
        render: function (ui) {
            let rowData = ui.rowData,
                dataIndx = ui.dataIndx;
            if(rowData['status'] == 4 || rowData['status'] == 5){
                return '';
            }
            return "<span style='white-space: nowrap'><button type='button' class='create_request_btn ui-button' title='Создать заявку'>Создать</button> <button type='button' class='delete_component_btn ui-button' title='удалить'><span style='color: #8a1f11; font-weight: bold;'>&nbsp;&times;&nbsp;</span></button></span>";
        }

    },
    getPartnumberColumn(),
    getPartnumberIdColumn(),
    getAmountColumn(),
    getUserColumn(),
    getPurposeColumn(),
    getCreated_atColumn(),
    getAssembly_toColumn(),
    getInstall_toColumn(),
    getDeficiteColumn(),
    getDescriptionColumn(),
    getInstall_fromColumn(),
    getPriorityColumn()//,
    //getActionsColumn('component')
];

let ComponentsTableDataModel = {
    recIndx: "id", //primary key
    location: "remote",
    sorting: "remote",
    dataType: "JSON",
    method: "POST",
    sortIndx: "priority",
    sortDir: "down",
    url: "/toAssembly/componentslist",
    getData: function (response) {
        return {curPage: response.curPage, totalRecords: response.totalRecords,data: response.data};
    }
};
let ComponentsTable = {
    scrollModel: {autoFit: true, horizontal: false},
    pageModel: getPageModel(),
    collapsible: false,
    resizable: true,
    stringify: false, //for PHP
    dataModel: ComponentsTableDataModel,
    colModel: ComponentsTableColumnModel,
    selectionModel: {
        type: 'row',
        mode: 'range',
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
                        $componentsGrid.pqGrid("option", "filterModel.header", !!$(this).is(":checked"));
                        $componentsGrid.pqGrid("refresh");
                    }
                }]
            },
            {type: 'separator'},
            {
                type: 'textbox',
                attr: 'placeholder="быстрый поиск"',
                cls: "filterValue",
                listeners: [{'keyup': filterHandlerComponents}]
            },
            {type: 'separator'},
            {
                type: 'button',
                label: 'Добавить компонент',
                listeners: [
                    {
                        'click': function (evt, ui) {
                            //append empty row at the end.
                            $('#popup-dialog-form-new-component').dialog('open');
                        },
                    }
                ]
            },
            {type: 'separator'},
            {
                type:
                    '<div class="controlgroup">\n' +
                    '    <button id="requestbutton" class="ui-corner-left">Создать заявку</button>\n' +
                    '    <select>\n' +
                    '      <option value="create">Создать заявку</option>\n' +
                    '      <option value="append">Добавить&nbsp;в&nbsp;заявку</option>\n' +
                    '    </select>\n' +
                    '  </div>',
            },
            {type: 'separator'},
            /*{ type: 'button', icon: 'ui-icon-plus', label: 'New Product', listener:
                    { "click": function (evt, ui) {
                            //append empty row at the end.
                            var rowData = {status:0}; //empty row
                            var rowIndx = $componentsGrid.pqGrid("addRow", { rowData: rowData, checkEditable: true });
                            $componentsGrid.pqGrid("goToPage", { rowIndx: rowIndx });
                            $componentsGrid.pqGrid("editFirstCellInRow", { rowIndx: rowIndx });
                        }
                    }
            },
            { type: 'separator' },*/
            {
                type: 'button',
                label: "Экспорт в Excel",
                icon: 'ui-icon-document',
                listeners: [{
                    "click": function (evt) {
                        let date1 = new Date();
                        userLog('Получаю экспорт таблицы компонентов');
                        $componentsGrid.pqGrid("showLoading");
                        let initial_amount_of_iframes = $("body").find('iframe').length;
                        let stopit = setInterval(function(){
                            let date2 = new Date();
                            let diff = date2 - date1;
                            if (initial_amount_of_iframes != $("body").find('iframe').length){
                                clearInterval(stopit);
                                $componentsGrid.pqGrid("hideLoading");
                                let seconds_passed = Math.round(diff/100)/10;
                                userLog('Похоже, экспорт компонентов сформирован за '+seconds_passed+'с');
                            }
                            if(diff>180000){
                                clearInterval(stopit);
                                $componentsGrid.pqGrid("hideLoading");
                                let seconds_passed = Math.round(diff/100)/10;
                                userLog('Прошло уже '+seconds_passed+'с, а экспорта компонентов еще нет. Возможно, что-то пошло не так...','error');
                            }
                        },300);
                        $componentsGrid.pqGrid("exportCsv", {url: "/toAssembly/export", sheetName: "Компоненты"});
                    }
                }]
            },
            {type: 'separator'},
            {
                type: 'button',
                label: 'История коррекций',
                icon: 'ui-icon-clock',
                title: 'История коррекций',
                listeners: [
                    {
                        'click': function(){
                            //popup_grid_store_correction
                            let rowIndx = getSelectedCompsRowsIndx();
                            if(rowIndx==null){
                                return;
                            }else if(typeof rowIndx.length === "undefined" || rowIndx.length===0){
                                return;
                            }
                            let row = $componentsGrid.pqGrid('getRowData', {rowIndx: rowIndx[0]});
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
                                       // $("#grid_store_correction").pqGrid('destroy');
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
            $("button:contains('Redo')", $grid).button("option", "disabled", !ui.canRedo || isGuest);
        }
        $("button:contains('Undo')", $grid).button("option", {label: 'Undo (' + ui.num_undo + ')'});
        $("button:contains('Redo')", $grid).button("option", {label: 'Redo (' + ui.num_redo + ')'});
    },
    editable: true,
    editor: {
        select: true
    },
    change: function(event, ui){
        //debugger
        if (ui.source == 'commit' || ui.source == 'rollback' || ui.source=='delete') {
            return;
        }
        saveChangesComponents();
    },
    trackModel: {on: true},
    showTitle: false,
    numberCell: {show: false},
    columnBorders: true,
    rowSelect: function( event, ui ) {
        $('.shorttext').not('.folded-text').addClass('folded-text',150);
        if(typeof ui.$tr !== 'undefined'){
            ui.$tr.find('.shorttext').removeClass('folded-text');
        }
    }
};
ComponentsTable.selectChange = function (evt, ui) {
    let rows = ui.rows;
    controlData.selection = [];
    if (rows && rows.length) {
        for (let i = 0; i < rows.length; i++) {
            // console.log(rows[i].rowData);
            controlData.selection.push(rows[i].rowData.id);
        }
    }
};

function getSelectedCompsRowsIndx(silent) {
    if(typeof silent === 'undefined'){
        silent = false;
    }
    let arr = $componentsGrid.pqGrid("selection", {type: 'row', method: 'getSelection'});
    // console.log(arr);
    let rowIndexes = [];
    if (arr && arr.length > 0) {
        for(let i=0;i<arr.length;i++){
            rowIndexes.push(arr[i].rowIndx);
        }
    }
    else {
        if(!silent) {
            showMessage("Выберите компонент");
        }
    }
    return rowIndexes;
}