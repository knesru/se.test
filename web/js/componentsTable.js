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
    $requestsGrid.pqGrid('refresh');
}



function saveChangesComponents() {
    let grid = $componentsGrid.pqGrid('getInstance').grid;
    userLog('Правка строки в компонентах','log');
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
        }
        for (let x in newData) {
            if (newData.hasOwnProperty(x)) {
                let identifier = '';
                identifier = newData['id'];
                if (typeof grid.getColumn({dataIndx: x}) !== 'undefined') {
                    if(typeof oldData[x] !== 'undefined') {
                        let oldval = oldData[x];
                    }
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
                    if(typeof oldval !== 'undefined') {
                        userLog('Поменял в строке ' + identifier + ' поле "' + grid.getColumn({dataIndx: x}).title + '": ' + oldval + ' -> ' + newval, 'log');
                    }else{
                        userLog('Добавил в строку ' + identifier + ' поле "' + grid.getColumn({dataIndx: x}).title + '": ' + newval, 'log');
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
                //debugger;
                grid.commit({type: 'add', rows: changes.addList});
                grid.commit({type: 'update', rows: changes.updateList});
                grid.commit({type: 'delete', rows: changes.deleteList});

                grid.history({method: 'reset'});
                grid.refresh();
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
        title: "Заявка",
        dataIndx: 'requestid',
        dataType: "string",
        editable: false,
        sortable: false,
        render: function (ui) {
            return "<button type='button' class='create_request_btn ui-button'>Создать</button>";
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
    getPriorityColumn(),
    {
        title: "",
        editable: false,
        sortable: false,
        render: function (ui) {
            return "<button type='button' class='delete_component_btn ui-button'>Удалить</button>";
        },
    },
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
            {
                type: 'button',
                label: 'Добавть компонент',
                listeners: [
                    {
                        'click': function (evt, ui) {
                            //append empty row at the end.
                            var rowData = { partnumber: 'новый компонент', purpose: 'назначение', amonunt: 1 }; //empty row
                            var rowIndx = $componentsGrid.pqGrid("addRow", { rowData: rowData });
                            $componentsGrid.pqGrid("goToPage", { rowIndx: rowIndx });
                            $componentsGrid.pqGrid("setSelection", null);
                            $componentsGrid.pqGrid("setSelection", { rowIndx: rowIndx, dataIndx: 'partnumber' });
                            $componentsGrid.pqGrid("editFirstCellInRow", { rowIndx: rowIndx });
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
        if (ui.source == 'commit' || ui.source == 'rollback') {
            return;
        }
        saveChangesComponents();
    },
    trackModel: {on: true},
    showTitle: false,
    numberCell: {show: false},
    columnBorders: true,
    refresh: function () {
        let $grid = $("#grid_new_components");
        $grid.find("button.create_request_btn").button()
            .unbind("click")
            .bind("click", function (evt) {
                let $tr = $(this).closest('tr');
                let rowIndx = $componentsGrid.pqGrid('getRowIndx',{$tr: $tr}).rowIndx;
                let row = $componentsGrid.pqGrid('getRowData', {rowIndx: rowIndx});
                requestsAction('create',row['id']);
            });

        $grid.find("button.delete_component_btn").button()
            .unbind("click")
            .bind("click", function (evt) {
                let $tr = $(this).parents('tr');
                let grid = $componentsGrid.pqGrid('getInstance').grid;
                let rowIndx = grid.getRowIndx({$tr: $tr}).rowIndx;
                let row = $componentsGrid.pqGrid('getRowData', {rowIndx: rowIndx});
                userLog('Удаляю '+(row['priority']?'приоритетный ':'')+'компонент '+row['partnumber']+', строка '+row['id']+'...');
                if(row['priority']) {
                    if (!confirm('Внимание, удаляется компонент с высоким приоритетом. Продолжить?')) {
                        userLog('Испугался и все отменил для компонента ' + row['partnumber'] + ', строки ' + row['id']);
                        return;
                    }
                    userLog('Подтвердил удаление приоритетного компонента ' + row['partnumber'] + ', строки ' + row['id']);
                }
                $.ajax({
                    dataType: "json",
                    type: "POST",
                    async: true,
                    beforeSend: function (jqXHR, settings) {
                        grid.showLoading();
                    },
                    url: "/toAssembly/removecomponent", //for ASP.NET, java
                    data: {id: [row['id']]},
                    success: function (result) {
                        if(result.success){
                            userLog('Успешно удален компонент '+result.pn);
                            grid.refreshDataAndView();
                            grid.history({method: 'reset'});
                        }else{
                            userLog(result.error,'error');
                        }
                    },
                    error: function(err){
                        userLog(err.responseText,'error');
                    },
                    complete: function () {
                        grid.hideLoading();
                    }
                });

            });
        $grid.find('.change-priority').each(function(){
            let $tr = $(this).parents('tr');
            let rowIndx = $componentsGrid.pqGrid('getRowIndx',{$tr: $tr}).rowIndx;
            let row = $componentsGrid.pqGrid('getRowData', {rowIndx: rowIndx});
            let disableUp = false;
            let disableDown = false;
            if(row['priority']){
                disableUp = true;
            }else{
                disableDown = true;
            }
            $(this).parent().css('text-align','right');
            $(this)
                .find('.change-priority-up')
                .button({
                    icon:  'ui-icon-arrowthick-1-n',
                    "disabled":disableUp
                })
                .unbind('click')
                .click(function () {
                    $componentsGrid
                        .pqGrid( "updateRow", {
                            rowIndx: rowIndx,
                            checkEditable: false,
                            row: {'priority':1}
                        });
                })
                .css('border-radius','4px 0px 0px 4px')
                .attr('title','Повысить приоритет')
                .tooltip();
            $(this)
                .find('.change-priority-down')
                .button({
                    icon:  'ui-icon-arrowthick-1-s',
                    "disabled":disableDown,
                })
                .unbind('click')
                .click(function () {
                    userLog('Понизил приоритет');
                    $componentsGrid
                        .pqGrid( "updateRow", {
                            rowIndx: rowIndx,
                            checkEditable: false,
                            row: { 'priority': 0 }
                        });
                })
                .css({'border-radius':'0px 4px 4px 0px','margin-left':-1})
                .attr('title','Понизить приоритет')
                .tooltip();

        });
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