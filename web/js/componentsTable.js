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
        editor: {
            type: 'textbox',
            init: dateEditor
        },
    },
    /*{
        title: "Сдано",
        dataIndx: 'delivered',
        dataType: "integer",
        align: "right",
    },*/
    {
        title: "Скомплектовать до",
        dataIndx: 'assembly_to',
        dataType: "date",
        editor: {
            type: 'textbox',
            init: dateEditor
        },
    },
    {
        title: "Монтаж до",
        dataIndx: 'install_to',
        dataType: "date",
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
        editor: {
            type: 'textbox',
            init: dateEditor
        },
    },
    {
        title: "Приоритет",
        dataIndx: 'priority',
        dataType: "integer",
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

let ComponentsTableDataModel = {
    recIndx: "id", //primary key
    location: "remote",
    sorting: "local",
    dataType: "JSON",
    method: "POST",
    sortIndx: "priority",
    sortDir: "up",
    url: "/toAssembly/componentslist",
    getData: function (response) {
        return {data: response.data};
    }
};
let ComponentsTable = {
    flexHeight: true,
    scrollModel: {autoFit: true, horizontal: false},
    pageModel: {type: 'local', curPage: 1},
    stringify: false, //for PHP
    dataModel: ComponentsTableDataModel,
    colModel: ComponentsTableColumnModel,
    selectionModel: {
        type: 'row',
        mode: 'range',
        fireSelectChange: true
    },
    filterModel: {on: true, mode: "OR", header: false, type: 'local'},
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
                type: 'button', attr: 'id="requestbutton"', icon: 'ui-icon-plus', label: 'Создать заявку',
                listener:
                    {
                        "click": function (evt, ui) {
                            if (typeof controlData.selection !== 'undefined') {
                                let datM = $("#grid_requests").pqGrid("option", "dataModel");
                                let grid = $("#grid_requests").pqGrid();

                                $.ajax({
                                    url: '/toassembly/request',
                                    data: {ids: controlData.selection, requestid: controlData.requestSelection},
                                    dataType: "json",
                                    type: "POST",
                                    async: true,
                                    beforeSend: function (jqXHR, settings) {
                                        $(".saving", grid).show();
                                    },
                                    success: function () {
                                        //commit the changes.
                                        location.reload();
                                    },
                                    complete: function () {
                                        $(".saving", grid).hide();
                                    }
                                });
                            }
                        }
                    }
            }
        ]
    },
    history: function (evt, ui) {
        let $grid = $(this);
        if (ui.canUndo != null) {
            $("button.changes", $grid).button("option", {disabled: !ui.canUndo});
        }
        if (ui.canRedo != null) {
            $("button:contains('Redo')", $grid).button("option", "disabled", !ui.canRedo);
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