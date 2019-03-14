function filterhandler(evt, ui) {
    var $toolbar = $componentsGrid.find('.pq-toolbar-search'),
        $value = $toolbar.find(".filterValue"),
        value = $value.val(),
        condition = 'contain',
        dataIndx = 'partnumber',
        filterObject;

    if (dataIndx == "") {//search through all fields when no field selected.
        filterObject = [];
        var CM = $componentsGrid.pqGrid("getColModel");
        for (var i = 0, len = CM.length; i < len; i++) {
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
    $requestsGrid.refresh();
}



function saveChangesComponents() {
    let grid = $componentsGrid.pqGrid('getInstance').grid;

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

let ComponentsTableColumnModel = [
    {
        title: "Заявка",
        dataIndx: 'requestid',
        dataType: "string",
        editable: false
    },
    {
        title: "ID",
        dataIndx: 'id',
        dataType: "integer",
        editable: false,
        filter: {
            type: 'textbox',
            condition: 'contain',
            listeners: ['change']
        }
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
        },
        filter: {
            type: 'textbox',
            condition: 'contain',
            listeners: ['change']
        }
    },
    {
        title: "ID компонента",
        dataIndx: 'partnumberid',
        dataType: "string",
        align: "right",
        filter: {
            type: 'textbox',
            condition: 'begin',
            listeners: ['change']
        }
    },
    {
        title: "Кол-во",
        dataIndx: 'amount',
        dataType: "integer",
        align: "right",
        filter: {
            type: 'textbox',
            condition: 'between',
            listeners: ['change']
        }
    },
    {
        title: "Пользователь",
        dataIndx: 'user',
        dataType: "string",
        filter: {
            type: 'textbox',
            condition: 'contain',
            listeners: ['change']
        }
    },
    {
        title: "Назначение",
        dataIndx: 'purpose',
        dataType: "string",
        filter: {
            type: 'textbox',
            condition: 'contain',
            listeners: ['change']
        }
    },
    {
        title: "Добавлено",
        dataIndx: 'created_at',
        dataType: "date",
        editor: {
            type: 'textbox',
            init: pqDatePicker
        },
        render: function (ui) {
            return renderDateOnly(ui);
        },
        filter: {
            type: 'textbox',
            condition: 'between',
            init: pqDatePicker,
            listeners: ['change']
        }
    },
    /*{
        title: "Сдано",
        dataIndx: 'delivered',
        dataType: "integer",
        align: "right",
    },*/
    {
        title: "Скомпл. до",
        dataIndx: 'assembly_to',
        dataType: "date",
        editor: {
            type: 'textbox',
            init: dateEditor
        },
        render: function (ui) {
            return renderDateOnly(ui);
        },
        filter: {
            type: 'textbox',
            condition: 'between',
            init: pqDatePicker,
            listeners: ['change']
        }
    },
    {
        title: "Монтаж до",
        dataIndx: 'install_to',
        dataType: "date",
        editor: {
            type: 'textbox',
            init: dateEditor
        },
        render: function (ui) {
            return renderDateOnly(ui);
        },
        filter: {
            type: 'textbox',
            condition: 'between',
            init: pqDatePicker,
            listeners: ['change']
        }
    },
    {
        title: "Дефицит",
        dataIndx: 'deficite',
        dataType: "string",
        filter: {
            type: 'textbox',
            condition: 'contain',
            listeners: ['change']
        }
    },
    {
        title: "Примечание",
        dataIndx: 'description',
        dataType: "string",
        filter: {
            type: 'textbox',
            condition: 'contain',
            listeners: ['change']
        }
    },
    {
        title: "Монтаж с",
        dataIndx: 'install_from',
        dataType: "date",
        editor: {
            type: 'textbox',
            init: dateEditor
        },
        render: function (ui) {
            return renderDateOnly(ui);
        },
        filter: {
            type: 'textbox',
            condition: 'between',
            init: pqDatePicker,
            listeners: ['change']
        }
    },
    {
        title: "Приоритет",
        dataIndx: 'priority',
        dataType: "integer",
        filter: {
            type: 'checkbox',
            condition: 'contain',
            listeners: ['change']
        },
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
        filter: { type: 'select',
            condition: 'equal',
            //init: multiSelect,
            valueIndx: "status",
            labelIndx: "status",
            prepend: { '': '--Select--' },
            listeners: ['change']
        }
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
    // flexHeight: false,
    scrollModel: {autoFit: true, horizontal: false},
    pageModel: {
        curPage: 1,
        type: "remote",
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
    dataModel: ComponentsTableDataModel,
    colModel: ComponentsTableColumnModel,
    selectionModel: {
        type: 'row',
        mode: 'range',
        fireSelectChange: true
    },
    filterModel: {on: true, mode: "OR", header: false},
    toolbar: {
        cls: "pq-toolbar-search",
        items: [
            {
                type: "<span style='margin:5px;'>Фильтр</span>"
            },
            /*{
                type: 'checkbox',
                listeners: [{
                    'change': function () {
                        $componentsGrid.pqGrid("option", "filterModel.header", !!$(this).is(":checked"));
                        $componentsGrid.pqGrid("refresh");
                    }
                }]
            },
            {type: 'separator'},*/
            {
                type: 'textbox',
                attr: 'placeholder="быстрый поиск"',
                cls: "filterValue",
                listeners: [{'keyup': filterhandler}]
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
                type: 'button', icon: 'ui-icon-disk', label: 'Сохранить', cls: 'changes', listener:
                    {
                        "click": function (evt, ui) {
                            saveChangesComponents();
                        }
                    },
                options: {disabled: true}
            },
            {
                type: 'button', icon: 'ui-icon-cancel', label: 'Сбросить', cls: 'changes', listener:
                    {
                        "click": function (evt, ui) {
                            $componentsGrid.pqGrid("rollback");
                            $componentsGrid.pqGrid("history", {method: 'resetUndo'});
                        }
                    },
                options: {disabled: true}
            },
            {type: 'separator'},
            {
                type: 'button', icon: 'ui-icon-arrowreturn-1-s', label: 'Отменить', cls: 'changes', listener:
                    {
                        "click": function (evt, ui) {
                            $componentsGrid.pqGrid("history", {method: 'undo'});
                        }
                    },
                options: {disabled: true}
            },
            {
                type: 'button', icon: 'ui-icon-arrowrefresh-1-s', label: 'Вернуть', listener:
                    {
                        "click": function (evt, ui) {
                            $componentsGrid.pqGrid("history", {method: 'redo'});
                        }
                    },
                options: {disabled: true}
            },
            {type: 'separator'},
            {
                type: 'button',
                label: "Экспорт в Excel",
                icon: 'ui-icon-document',
                listeners: [{
                    "click": function (evt) {
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
    trackModel: {on: true},
    showTitle: false,
    numberCell: {show: false},
    columnBorders: true
};
ComponentsTable.selectChange = function (evt, ui) {
    let rows = ui.rows;
    controlData.selection = [];
    if (rows && rows.length) {
        for (let i = 0; i < rows.length; i++) {
            console.log(rows[i].rowData);
            controlData.selection.push(rows[i].rowData.id);
        }
    }
};