function filterhandler(evt, ui) {
    $requestsGrid.pqGrid("option", "filterModel.type", 'local');
    var $toolbar = $requestsGrid.find('.pq-toolbar-search'),
        $value = $toolbar.find(".filterValue"),
        value = $value.val(),
        condition = 'contain',
        dataIndx = 'partnumber',
        filterObject;

    if (dataIndx == "") {//search through all fields when no field selected.
        filterObject = [];
        var CM = $requestsGrid.pqGrid("getColModel");
        for (var i = 0, len = CM.length; i < len; i++) {
            dataIndx = CM[i].dataIndx;
            filterObject.push({dataIndx: dataIndx, condition: condition, value: value});
        }
    }
    else {//search through selected field.
        filterObject = [{dataIndx: dataIndx, condition: condition, value: value}];
    }
    $requestsGrid.pqGrid("filter", {
        oper: 'replace',
        data: filterObject
    });
    $requestsGrid.refresh();
    $requestsGrid.pqGrid("option", "filterModel.type", 'remote');
}

function saveChangesRequests() {
    let grid = $requestsGrid.pqGrid('getInstance').grid;

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
            },
            complete: function () {
                grid.hideLoading();
            }
        });
    }
}

let RequestsTableColumnModel = [
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
        }
    },
    {
        title: "ID",
        dataIndx: 'id',
        dataType: "integer",
        editable: false
    },
    {
        title: "Партномер",
        dataIndx: 'partnumber',
        dataType: "string",
        editor: {
            type: 'textbox',
            init: function (ui) {
                console.log('inline');
                let $inp = ui.$cell.find("input");
                let url = ui.column.editor.url;

                //initialize the editor
                $inp.autocomplete({
                    appendTo: ui.$cell, //for grid in maximized state.
                    source: url,
                    selectItem: {on: true}, //custom option
                    highlightText: {on: true}, //custom option
                    minLength: 2
                }).focus(function () {
                    //open the autocomplete upon focus
                    $(this).autocomplete("search", "");
                }).select(function () {

                });
            },
            url: 'component/ajaxList'
        }
    },
    {
        title: "ID компонента",
        dataIndx: 'partnumberid',
        dataType: "string",
        align: "right"
    },
    {
        title: "Кол-во",
        dataIndx: 'amount',
        dataType: "integer",
        align: "right",
    },
    {
        title: "Пользователь",
        dataIndx: 'user',
        dataType: "string",
    },
    {
        title: "Назначение",
        dataIndx: 'purpose',
        dataType: "string",
    },
    {
        title: "Добавлено",
        dataIndx: 'created_at',
        dataType: "date",
        render: function (ui) {
            return renderDateOnly(ui);
        },
        editor: {
            type: 'textbox',
            init: dateEditor
        },
    },
    {
        title: "Сдано",
        dataIndx: 'delivered',
        dataType: "integer",
        align: "right",
    },
    {
        title: "Скомпл. до",
        dataIndx: 'assembly_to',
        dataType: "date",
        render: function (ui) {
            return renderDateOnly(ui);
        },
        editor: {
            type: 'textbox',
            init: dateEditor
        },
    },
    {
        title: "Монтаж до",
        dataIndx: 'install_to',
        dataType: "date",
        render: function (ui) {
            return renderDateOnly(ui);
        },
        editor: {
            type: 'textbox',
            init: dateEditor
        },
    },
    {
        title: "Дефицит",
        dataIndx: 'deficite',
        dataType: "string",
    },
    {
        title: "Примечание",
        dataIndx: 'description',
        dataType: "string",
    },
    {
        title: "Монтаж с",
        dataIndx: 'install_from',
        dataType: "date",
        render: function (ui) {
            return renderDateOnly(ui);
        },
        editor: {
            type: 'textbox',
            init: dateEditor
        },
    },
    {
        title: "Приоритет",
        dataIndx: 'priority',
        dataType: "integer",
        render: function (ui) {
            let rowData = ui.rowData,
                dataIndx = ui.dataIndx;

            rowData.pq_cellcls = rowData.pq_cellcls || {};
            if (rowData[dataIndx] > 0){
                rowData.pq_cellcls[dataIndx] = 'high-priority';
                return "<span class='ui-icon ui-icon-alert'> </span>&nbsp;высокий";
            }else {
                return '';
            }
        }
    },
    {
        title: "Статус",
        dataIndx: 'status',
        dataType: "integer",
        editor: {
            type: 'select',
            init: function (ui) {
                ui.$cell.find("select").pqSelect();
            },
            valueIndx: "value",
            labelIndx: "text",
            mapIndices: {"text": "Статус", "value": "status"},
            options: [
                {"value": 0, "text": 'Не активен'},
                {"value": 1, "text": 'Комплектация'},
                {"value": 2, "text": 'Скомпонован'},
                {"value": 3, "text": 'На монтаже'},
                {"value": 4, "text": 'Закрыт'},
                {"value": 5, "text": 'Отмена'}
            ]
        },
        render: function (ui) {
            let rowData = ui.rowData,
                dataIndx = ui.dataIndx;
            let options = ui.column.editor.options;
            return options[rowData[dataIndx]]['text'];
        },
    },
];
let RequestsTableDataModel = {
    recIndx: "id", //primary key
    location: "remote",
    sorting: "remote",
    dataType: "JSON",
    method: "POST",
    sortIndx: ["priority","requestid"],
    sortDir: "down",
    url: "/toAssembly/requestslist",
    getData: function (response) {
        return {data: response.data};
    },
    beforeSend: function (jqXHR, settings) {
        console.log(jqXHR);
        console.log(settings);
        if (settings.data.length > 0) {
            settings.data += '&';
        }
        settings.data += 'showall=' + !!$('#showAll').is(":checked");
    }
};
let RequestsTable = {
    // flexHeight: true,
    //height: '50%',
    scrollModel: {autoFit: true, horizontal: false},
    pageModel: {
        curPage: 1,
        type: "local",
        rPP: 10,
        strRpp: "{0}",
        strDisplay: "с {0} до {1} из {2}",
        rPPOptions: function () {
            let rpp = [5, 10, 20];
            for (let i=0; i<11; i++){
                rpp.push(rpp[i]*10);
            }
            return rpp;
        }()
    },
    stringify: false, //for PHP
    dataModel: RequestsTableDataModel,
    colModel: RequestsTableColumnModel,
    selectionModel: {
        type: 'row',
        mode: 'single',
        fireSelectChange: true
    },
    filterModel: {on: true, mode: "OR", header: false},
    toolbar: {
        cls: "pq-toolbar-search",
        items: [
            {
                type: "<span style='margin:5px;'>Фильтр</span>"
            },
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
                listeners: [{'keyup': filterhandler}]
            },
            {type: 'separator'},
            {
                type: 'button', icon: 'ui-icon-disk', label: 'Сохранить', cls: 'changes', listener:
                    {
                        "click": function (evt, ui) {
                            saveChangesRequests();
                        }
                    },
                options: {disabled: true, hidden: true}
            },
            {
                type: 'button', icon: 'ui-icon-cancel', label: 'Сбросить', cls: 'changes', listener:
                    {
                        "click": function (evt, ui) {
                            $requestsGrid.pqGrid("rollback");
                            $requestsGrid.pqGrid("history", {method: 'resetUndo'});
                        }
                    },
                options: {disabled: true}
            },
            {type: 'separator'},
            {
                type: 'button', icon: 'ui-icon-arrowreturn-1-s', label: 'Отменить', cls: 'changes', listener:
                    {
                        "click": function (evt, ui) {
                            $requestsGrid.pqGrid("history", {method: 'undo'});
                        }
                    },
                options: {disabled: true}
            },
            {
                type: 'button', icon: 'ui-icon-arrowrefresh-1-s', label: 'Вернуть', listener:
                    {
                        "click": function (evt, ui) {
                            $requestsGrid.pqGrid("history", {method: 'redo'});
                        }
                    },
                options: {disabled: true}
            },
            {type: 'separator'},
            {
                type: 'button', icon: 'ui-icon-check', cls: 'receive', label: 'Принять', listener:
                    {
                        "click": function (evt, ui) {
                            let grid = $requestsGrid.pqGrid('getInstance').grid;
                            let rowIndx = getRowIndx();
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

                            $("#popup-dialog-receive").dialog({
                                title: row['requestid'].replace(/^0+/, '') + ': ' + row['partnumber'], buttons: {
                                    "Принять": function () {


                                        if ($frm.find("input[name='amount']").val() == row['amount'] - row['delivered']) {
                                            if (!confirm("Заявка удовлетворена. Компонент будет закрыт.")) {
                                                return;
                                            }
                                        }else if($frm.find("input[name='amount']").val() > row['amount'] - row['delivered']){
                                            if (!confirm("Количество принятых больше, чем заказано. Компонент будет" +
                                                " закрыт. Все равно принять?")) {
                                                return;
                                            }
                                        }

                                        $.ajax({
                                            dataType: "json",
                                            type: "POST",
                                            async: true,
                                            beforeSend: function (jqXHR, settings) {
                                                grid.showLoading();
                                            },
                                            url: "/toAssembly/receive", //for ASP.NET, java
                                            data: {
                                                requestid: row['id'],
                                                amount: $frm.find("input[name='amount']").val(),
                                                storeid: $frm.find("input[name='storeid']").val(),
                                                place: $frm.find("#place").val()
                                            },
                                            success: function (changes) {
                                                //debugger;
                                                grid.history({method: 'reset'});
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
                        let rowIndx = getRowIndx();
                        if(rowIndx === null){
                            return;
                        }
                        var row = $requestsGrid.pqGrid('getRowData', {rowIndx: rowIndx});

                        let $frm = $("form#replace-form");
                        $frm.find("#replace_request").text(row['requestid'].replace(/^0+/, ''));
                        $frm.find("#old_component").text(row['partnumber']);

                        $("#popup-dialog-replace").dialog({
                            title: row['requestid'].replace(/^0+/, '') + ': ' + row['partnumber'], buttons: {
                                "Заменить": function () {
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
                                            //debugger;
                                            grid.history({method: 'reset'});
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
    trackModel: {on: true},
    showTitle: false,
    numberCell: {show: false},
    columnBorders: true
};

function getRowIndx() {
    var arr = $requestsGrid.pqGrid("selection", {type: 'row', method: 'getSelection'});
    if (arr && arr.length > 0) {
        return arr[0].rowIndx;
    }
    else {
        alert("Select a row.");
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
            console.log(rows[i].rowData);
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