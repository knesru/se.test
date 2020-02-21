function filterHandlerRegistry(evt, ui) {
    let $toolbar = $registryGrid.find('.pq-toolbar-search'),
        $value = $toolbar.find(".filterValue"),
        value = $value.val(),
        condition = 'contain',
        dataIndx = 'partnumber',
        filterObject;

    if (dataIndx == "") {//search through all fields when no field selected.
        filterObject = [];
        let CM = $registryGrid.pqGrid("getColModel");
        for (let i = 0, len = CM.length; i < len; i++) {
            dataIndx = CM[i].dataIndx;
            filterObject.push({dataIndx: dataIndx, condition: condition, value: value});
        }
    } else {//search through selected field.
        filterObject = [{dataIndx: dataIndx, condition: condition, value: value}];
    }
    $registryGrid.pqGrid("filter", {
        oper: 'replace',
        data: filterObject
    });
    $requestsGrid.pqGrid('refreshDataAndView');
}


function saveChangesRegistry() {
    let grid = $registryGrid.pqGrid('getInstance').grid;
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
        if (grid.getChanges({format: "raw"}).updateList.length === 0) {
            userLog('Создал новый компонент', 'log');
            newData = grid.getChanges({format: "raw"}).addList;
        } else {
            newData = grid.getChanges({format: "raw"}).updateList[0]['rowData'];
            oldData = grid.getChanges({format: "raw"}).updateList[0].oldRow;
            if (typeof oldData['partnumber'] !== "undefined" && newData['partnumber'] !== oldData['partnumber']) {
                if (!isNaN(newData['partnumberid']) && newData['partnumberid'] !== null) {
                    showMessage('Нельзя переименовать компонент из STMS', 'warning');
                    userLog('Нельзя переименовать компонент из STMS. Отмена действия', 'info');
                    grid.rollback();
                    return null;
                } else {
                    if (!confirm('Действительно переименовать компонент?')) {
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
                        if (typeof oldval !== 'undefined') {
                            oldval = oldval ? 'высокий' : 'низкий';
                        }
                        newval = newval ? 'высокий' : 'низкий';
                    }
                    if (typeof oldval !== 'undefined') {
                        userLog('Поменял в строке ' + identifier + ' у компонента ' + newData['partnumber'] + ' поле «' + grid.getColumn({dataIndx: x}).title + '»: ' + oldval + ' -> ' + newval, 'log');
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
            url: "/tasks/update", //for ASP.NET, java
            data: {list: JSON.stringify(changes)},
            success: function (changes) {
                generalAjaxAnswer(changes, true);
                if (typeof changes.success !== "undefined") {
                    if (changes.success === false) {
                        grid.rollback();
                    }
                }
                if (typeof changes.data !== 'undefined') {
                    let rowIndx = $registryGrid.pqGrid('getRowIndx', {rowData: changes.data[0].id});
                    $registryGrid.pqGrid("goToPage", {rowIndx: rowIndx});
                    $registryGrid.pqGrid("setSelection", null);
                    $registryGrid.pqGrid("setSelection", {rowIndx: rowIndx, dataIndx: 'partnumber'});
                    $registryGrid.pqGrid("editFirstCellInRow", {rowIndx: rowIndx});
                    return;
                }
                //debugger;
                grid.commit({type: 'add', rows: changes.addList});
                grid.commit({type: 'update', rows: changes.updateList});
                grid.commit({type: 'delete', rows: changes.deleteList});

                grid.history({method: 'reset'});
                grid.refresh();
            },
            error: function (err) {
                userLog(err.responseText, 'error');
            },
            complete: function () {
                grid.hideLoading();
            }
        });
    }
}

let RegistryTableColumnModel = [
        getIdColumn(),
        {
            title: "Номер заказа",
            dataIndx: 'name',
            dataType: "string",
            editable: false,
            // hidden: hidden,
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: ['change']
            }
        },
        getUserColumn("Руководитель"),
        {
            title: "Ведущий",
            dataIndx: 'manager_name',
            dataType: "string",
            editable: false,
            // hidden: hidden,
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: ['change']
            }
        },
        {
            title: "Контракт, договор, счет",
            dataIndx: 'contract',
            dataType: "string",
            editable: false,
            // hidden: hidden,
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: ['change']
            }
        },
        {
            title: "Заказчик",
            dataIndx: 'customer',
            dataType: "string",
            editable: false,
            // hidden: hidden,
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: ['change']
            }
        },
        {
            title: "Наименование товара",
            dataIndx: 'packages',
            dataType: 'string',
            width: 300,
            sortable: false,
            editable: false,
            render: function (ui) {
                let rowData = ui.rowData,
                    dataIndx = ui.dataIndx;
                // if(rowData['status'] == 4 || rowData['status'] == 5){
                //     return '';
                // }
                let out = '<table>';
                for (let acceptor_name in rowData['packages']) {
                    if (rowData['packages'].hasOwnProperty(acceptor_name)) {
                        out += '<th colspan="3" class="acceptor-name-incell" ><div>' + acceptor_name + '</div>';
                        let acceptor = rowData['packages'][acceptor_name];
                        let odd = false;
                        for (let j = 0; j < acceptor.length; j++) {
                            let package = acceptor[j];
                            out += '<tr class="package-name-incell ' + (odd ? 'odd' : 'even') + '"><td>' + package['name'] + '</td><td style="width: 10%; text-align: right; ">' + package['amount'] + '</td><td style="width: 10%; text-align: center">' + package['units'] + '</td></tr>';
                            odd = !odd;
                        }
                        out += '';
                    }
                }
                out += '</table>';
                return out;
            }
        },
        generalDateColumn({title: "Дата внесения заказа", dataIndx: 'created_at',editable: false}),
        generalDateColumn({title: "Срок поставки", dataIndx: 'delivery_date',editable: false}),
        generalDateColumn({title: "Срок сдачи на склад", dataIndx: 'store_delivery_date',editable: false}),
        {
            title: "Тип приемки",
            dataIndx: 'inspection_type',
            dataType: "string",
            editable: false,
            // hidden: hidden,
            filter: {
                type: 'textbox',
                condition: 'equal',
                listeners: ['change']
            }
        },
        {
            title: "Гарантия",
            dataIndx: 'warranty',
            dataType: "integer",
            editable: false,
            // hidden: hidden,
            filter: {
                type: 'textbox',
                condition: 'equal',
                listeners: ['change']
            }
        },
        {
            title: "Примечания",
            dataIndx: 'notes',
            dataType: "string",
            editable: false,
            // hidden: hidden,
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: ['change']
            }
        },
        generalDateColumn({title: "Дата поступления на склад", dataIndx: 'store_acceptance_date',editable: false}),
        generalDateColumn({title: "Дата отгрузки по документам", dataIndx: 'official_delivery_date',editable: false}),
        {
            title: "Статус",
            dataIndx: 'statusid',
            dataType: "integer",
            cls: 'buttons-here',
            editor: {
                type: 'select',
                init: function (ui) {
                    ui.$cell.find("select").find('option').each(function () {
                        // if (!canChangeStatus($(this).val(), ui.rowData.status)) {
                        //     $(this).attr('disabled', 'disabled');
                        // }
                    });
                },
                valueIndx: "value",
                labelIndx: "text",
                mapIndices: {"text": "Статус", "value": "status"},
                options: getStatusesArray()
            },
            render: function (ui) {
                let rowData = ui.rowData,
                    dataIndx = ui.dataIndx;
                let options = ui.column.editor.options;
                return options[rowData[dataIndx]]['text'];
            },
            filter: {
                type: 'select',
                condition: 'equal',
                //init: multiSelect,
                prepend: {'': 'Любой'},
                listeners: ['change'],
                valueIndx: "value",
                labelIndx: "text",
                mapIndices: {"text": "Статус", "value": "status"},
                options: getStatusesArray()
            }
        },
        generalDateColumn({title: "Обновлено", dataIndx: 'updated_at',editable: false}),
    ]
;

let RegistryTableDataModel = {
    recIndx: "id", //primary key
    location: "remote",
    sorting: "remote",
    dataType: "JSON",
    method: "POST",
    sortIndx: getCookie('sortRegistry', 'sortIndx'),
    sortDir: getCookie('sortRegistry', 'sortDir'),
    url: "/tasks/registrylist",
    getData: function (response) {
        return {curPage: response.curPage, totalRecords: response.totalRecords, data: response.data};
    }
};
let RegistryTable = {
    scrollModel: {autoFit: true, horizontal: false},
    pageModel: getPageModel(),
    collapsible: true,
    resizable: true,
    stringify: false, //for PHP
    dataModel: RegistryTableDataModel,
    colModel: RegistryTableColumnModel,
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
                        $registryGrid.pqGrid("option", "filterModel.header", !!$(this).is(":checked"));
                        $registryGrid.pqGrid("refresh");
                    }
                }]
            },
            {type: 'separator'},
            {
                type: 'textbox',
                attr: 'placeholder="быстрый поиск"',
                cls: "filterValue",
                listeners: [{'keyup': filterHandlerRegistry}]
            },
            {type: 'separator'},
            {
                type: 'button',
                label: 'Добавить заказ',
                listeners: [
                    {
                        'click': function (evt, ui) {
                            //append empty row at the end.
                            $('#new-comp-form-hash').val(Math.random());
                            openTaskForm('Добавить заказ');
                        },
                    }
                ]
            },
            {
                type: 'button',
                label: 'Изменить заказ',
                listeners: [
                    {
                        'click': function (evt, ui) {
                            //append empty row at the end.
                            $('#new-comp-form-hash').val(Math.random());
                            let grid = $registryGrid.pqGrid('getInstance').grid;
                            let rowIndx = getSelectedRegistryRowsIndx();
                            console.log(rowIndx);
                            if (rowIndx.length === 0) {
                                return;
                            }
                            let row = $registryGrid.pqGrid('getRowData', {rowIndx: rowIndx});
                            console.log(row);
                            $.ajax({
                                'url': '/tasks/getTask',
                                'method': 'POST',
                                'dataType': 'json',
                                'data': {taskid: row['id']},
                                success: function (response) {
                                    if (typeof response !== 'undefined') {
                                        if (typeof response.data !== "undefined") {
                                            for (let field in response.data) {
                                                if (response.data.hasOwnProperty(field)) {
                                                    let $field = $('#' + response.data.model + '_' + field);
                                                    if ($field.prop("tagName") == 'SELECT' || $field.prop("tagName") == 'INPUT') {
                                                        $field.val(response.data[field]);
                                                    } else {
                                                        $field.text(response.data[field]);
                                                    }

                                                }
                                            }
                                            let acceptors_amount = 0;
                                            for (let acceptor in response.data.packages){
                                                if (response.data.packages.hasOwnProperty(acceptor)) {
                                                    acceptors_amount++;
                                                    if(acceptors_amount>1){
                                                        $('.addAcceptor').click();
                                                    }
                                                    let $acceptor = response.data.packages[acceptor];
                                                    let $acceptor_block = $('.acceptor-block').last().prev();
                                                    $acceptor_block.find('.product-acceptor').val(acceptor);
                                                    let packages = 0;
                                                    for(let i=0;i<$acceptor.length;i++){
                                                        packages++;
                                                        if((acceptors_amount===1 && packages>1) || (acceptors_amount>1 && packages>0)){
                                                            $acceptor_block.find('.addPackage').click();
                                                        }
                                                        let $package_block = $acceptor_block.find(".packages-list li").last();
                                                        $package_block.find('.product-package').val($acceptor[i].name);
                                                        $package_block.find('.product-package-amount').val($acceptor[i].amount);
                                                        $package_block.find('.product-package-units').val($acceptor[i].units);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                },
                                error: function (a, b, c) {
                                    console.error(a, b, c);
                                }
                            });
                            openTaskForm('Изменить заказ ' + row['name']);
                        },
                    }
                ]
            },
            {
                type: 'button',
                label: 'Удалить заказ',
                listeners: [
                    {
                        'click': function (evt, ui) {
                            //append empty row at the end.
                            $('#new-comp-form-hash').val(Math.random());
                            $('#popup_dialog_new_task').dialog(
                                {
                                    title: 'Добавить заказ',
                                    width: 700,
                                    height: 700,
                                    buttons: {
                                        "Сохранить": function () {
                                            let packages_tree = {};
                                            $('.acceptor-block').each(function () {
                                                if ($(this).find('.product-acceptor').length > 0) {
                                                    let acceptor = $(this).find('.product-acceptor').val();
                                                    packages_tree[acceptor] = [];
                                                    $(this).find('.packages-list').find('li').each(function () {
                                                        let package_data = {
                                                            'name': $(this).find('.product-package').val(),
                                                            'amount': $(this).find('.product-package-amount').val(),
                                                            'units': $(this).find('.product-package-units').val()
                                                        };
                                                        packages_tree[acceptor].push(package_data);
                                                    });
                                                }
                                            });
                                            let packages_data = $('<input>').attr({
                                                'name': 'products_tree',
                                                'type': 'hidden'
                                            });
                                            packages_data.val(JSON.stringify(packages_tree));
                                            $('#new-task-form').append(packages_data);
                                            let form_data = $('#new-task-form').serialize();
                                            $.ajax({
                                                'url': '/tasks/update',
                                                'method': 'POST',
                                                'dataType': 'json',
                                                'data': form_data
                                            });
                                            packages_data.remove();
                                            packages_tree = {};
                                            $(this).dialog("close");

                                        }/*,
                                            "Отмена": function() {
                                                $( this ).dialog( "close" );
                                            }*/
                                    }
                                }
                            ).dialog('open');
                        },
                    }
                ]
            },
            {type: 'separator'},
            {
                type: 'button',
                label: "Экспорт в Excel",
                icon: 'ui-icon-document',
                listeners: [{
                    "click": function (evt) {
                        let date1 = new Date();
                        userLog('Получаю экспорт таблицы компонентов');
                        $registryGrid.pqGrid("showLoading");
                        let initial_amount_of_iframes = $("body").find('iframe').length;
                        let stopit = setInterval(function () {
                            let date2 = new Date();
                            let diff = date2 - date1;
                            if (initial_amount_of_iframes != $("body").find('iframe').length) {
                                clearInterval(stopit);
                                $registryGrid.pqGrid("hideLoading");
                                let seconds_passed = Math.round(diff / 100) / 10;
                                userLog('Похоже, экспорт компонентов сформирован за ' + seconds_passed + 'с');
                            }
                            if (diff > 180000) {
                                clearInterval(stopit);
                                $registryGrid.pqGrid("hideLoading");
                                let seconds_passed = Math.round(diff / 100) / 10;
                                userLog('Прошло уже ' + seconds_passed + 'с, а экспорта компонентов еще нет. Возможно, что-то пошло не так...', 'error');
                            }
                        }, 300);
                        $registryGrid.pqGrid("exportCsv", {url: "/tasks/export", sheetName: "Компоненты"});
                    }
                }]
            }
        ]
    },
    history: function (evt, ui) {
        // let $grid = $(this);
        // if (ui.canUndo != null) {
        //     $("button.changes", $grid).button("option", {disabled: !ui.canUndo || isGuest});
        // }
        // if (ui.canRedo != null) {
        //     $("button:contains('Redo')", $grid).button("option", "disabled", !ui.canRedo || isGuest);
        // }
        // $("button:contains('Undo')", $grid).button("option", {label: 'Undo (' + ui.num_undo + ')'});
        // $("button:contains('Redo')", $grid).button("option", {label: 'Redo (' + ui.num_redo + ')'});
    },
    editable: true,
    editor: {
        select: true
    },
    change: function (event, ui) {
        //debugger
        if (ui.source == 'commit' || ui.source == 'rollback' || ui.source == 'delete') {
            return;
        }
        saveChangesRegistry();
    },
    sort: function (event, ui) {
        setCookie('sortRegistry', JSON.stringify({
            'sortDir': ui.dataModel.sortDir,
            'sortIndx': ui.dataModel.sortIndx
        }), 100);
    },
    trackModel: {on: true},
    showTitle: false,
    numberCell: {show: false},
    columnBorders: true,
    rowSelect: function (event, ui) {
        $('.shorttext').not('.folded-text').addClass('folded-text', 150);
        if (typeof ui.$tr !== 'undefined') {
            ui.$tr.find('.shorttext').removeClass('folded-text');
        }
    }
};
RegistryTable.selectChange = function (evt, ui) {
    let rows = ui.rows;
    controlData.selection = [];
    if (rows && rows.length) {
        for (let i = 0; i < rows.length; i++) {
            // console.log(rows[i].rowData);
            controlData.selection.push(rows[i].rowData.id);
        }
    }
};

function getSelectedRegistryRowsIndx(silent) {
    if (typeof silent === 'undefined') {
        silent = false;
    }
    let arr = $registryGrid.pqGrid("selection", {type: 'row', method: 'getSelection'});
    // console.log(arr);
    let rowIndexes = [];
    if (arr && arr.length > 0) {
        for (let i = 0; i < arr.length; i++) {
            rowIndexes.push(arr[i].rowIndx);
        }
    } else {
        if (!silent) {
            showMessage("Выберите заказ");
        }
    }
    return rowIndexes;
}

function openTaskForm(title){
    //init form
    $('#new-task-form input').val('');
    $('.acceptor-block').not(':first').not(':last').remove();
    $('.product-acceptor').val('');
    $('.packages-list li').not(':first').remove();
    $('.product-package').val('');
    $('.product-package-amount').val('');
    $('.product-package-units').val('');
    $('#Tasks_updated_at').text('');
    //open dialog
    $('#popup_dialog_new_task').dialog(
        {
            title: title,
            width: 700,
            height: 700,
            buttons: {
                "Сохранить": function () {
                    let packages_tree = {};
                    $('.acceptor-block').each(function () {
                        if ($(this).find('.product-acceptor').length > 0) {
                            let acceptor = $(this).find('.product-acceptor').val();
                            packages_tree[acceptor] = [];
                            $(this).find('.packages-list').find('li').each(function () {
                                let package_data = {
                                    'name': $(this).find('.product-package').val(),
                                    'amount': $(this).find('.product-package-amount').val(),
                                    'units': $(this).find('.product-package-units').val()
                                };
                                packages_tree[acceptor].push(package_data);
                            });
                        }
                    });
                    let packages_data = $('<input>').attr({
                        'name': 'products_tree',
                        'type': 'hidden'
                    });
                    packages_data.val(JSON.stringify(packages_tree));
                    $('#new-task-form').append(packages_data);
                    let form_data = $('#new-task-form').serialize();
                    $.ajax({
                        'url': '/tasks/update',
                        'method': 'POST',
                        'dataType': 'json',
                        'data': form_data
                    });
                    packages_data.remove();
                    packages_tree = {};
                    $(this).dialog("close");
                    let grid = $registryGrid.pqGrid('getInstance').grid;
                    grid.refreshDataAndView();
                }/*,
                                            "Отмена": function() {
                                                $( this ).dialog( "close" );
                                            }*/
            }
        }
    ).dialog('open');
}

$registryGrid = $("#grid_registry").pqGrid(RegistryTable);