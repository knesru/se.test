function getIdColumn(hidden) {
    if(typeof hidden === "undefined"){
        hidden = false;
    }
    return {
        title: "ID",
        dataIndx: 'id',
        dataType: "integer",
        editable: false,
        hidden: hidden,
        filter: {
            type: 'textbox',
            condition: 'equal',
            listeners: ['change']
        }
    };
}

function getPartnumberColumn() {
    return {
        title: "Партномер",
        dataIndx: 'partnumber',
        dataType: "string",
        render: function(ui){
            return renderShortText(ui);
        },
        editor: {
            type: 'textbox',
            init: function (ui) {
                //console.log('inline');
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
    };
}

function getPartnumberIdColumn() {
    return {
        title: "ID компонента",
        dataIndx: 'partnumberid',
        dataType: "string",
        align: "right",
        hidden: true
    };
}

function getAmountColumn() {
    return {
        title: "Кол-во",
        dataIndx: 'amount',
        dataType: "integer",
        align: "right",
        filter: {
            type: 'textbox',
            condition: 'between',
            listeners: ['change']
        }
    };
}

function getUserColumn() {
    return {
        title: "Пользователь",
        dataIndx: 'user',
        dataType: "string",
        editable: false,
        filter: {
            type: 'select',
            condition: 'equal',
            //init: multiSelect,
            prepend: { '': 'Любой' },
            listeners: ['change'],
            valueIndx: "value",
            labelIndx: "text",
            mapIndices: {"text": "Пользователь", "value": "user"},
            options: getUsersArray()
        }
    };
}

function getPurposeColumn() {
    return {
        title: "Назначение",
        dataIndx: 'purpose',
        dataType: "string",
        render: function(ui){
            return renderShortText(ui);
        },
        filter: {
            type: 'textbox',
            condition: 'contain',
            listeners: ['change']
        }
    };
}

function getCreated_atColumn() {
    return {
        title: "Добавлено",
        dataIndx: 'created_at',
        dataType: "date",
        render: function (ui) {
            return renderDateOnly(ui);
        },
        editable: false,
        filter: {
            type: 'textbox',
            condition: 'between',
            init: pqDatePicker,
            listeners: ['change']
        }
    };
}

function getAssembly_toColumn() {
    return {
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
    };
}

function getInstall_toColumn() {
    return {
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
    };
}

function getDeficiteColumn() {
    return {
        title: "Дефицит",
        dataIndx: 'deficite',
        dataType: "string",
        render: function(ui){
            return renderShortText(ui);
        },
        filter: {
            type: 'textbox',
            condition: 'contain',
            listeners: ['change']
        }
    };
}

function getDescriptionColumn() {
    return {
        title: "Примечание",
        dataIndx: 'description',
        dataType: "string",
        render: function(ui){
            return renderShortText(ui);
        },
        filter: {
            type: 'textbox',
            condition: 'contain',
            listeners: ['change']
        }
    };
}

function getInstall_fromColumn() {
    return {
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
    };
}

function getPriorityColumn() {
    return {
        title: "Приоритет",
        dataIndx: 'priority',
        dataType: "bool",
        minWidth: 90,
        maxWidth: 90,
        cls:'buttons-here',
        align: 'right',
        editable: false,
        filter: { type: 'select',
            condition: 'equal',
            listeners: ['change'],
            prepend: { '': 'Любой' },
            valueIndx: "value",
            labelIndx: "text",
            mapIndices: {"text": "Приоритет", "value": "status"},
            options: [
                {"value": false, "text": 'Низкий'},
                {"value": true, "text": 'Высокий'},
            ]
        },
        render: function (ui) {
            let rowData = ui.rowData,
                dataIndx = ui.dataIndx;

            if(rowData[dataIndx]===true){
                rowData[dataIndx] = 1;
            }
            if(rowData[dataIndx]!==1){
                rowData[dataIndx] = 0;
            }

            let buttonUp = '<button class="change-priority-up  ui-button ui-corner-all ui-widget" title="Повысить приоритет"><span class="ui-button-icon ui-icon ui-icon-arrowthick-1-n"></span><span class="ui-button-icon-space"> </span></button>';
            let buttonDown = '<button class="change-priority-down  ui-button ui-corner-all ui-widget" title="Снизить приоритет"><span class="ui-button-icon ui-icon ui-icon-arrowthick-1-s"></span><span class="ui-button-icon-space"> </span></button>';

            if (rowData[dataIndx] > 0){
                buttonUp = '';
            }else {
                buttonDown = '';
            }

            let buttons = "<span class=\"change-priority\">"+buttonUp+buttonDown+"</span>";

            if(rowData['status'] == 4 || rowData['status'] == 5){
                buttons = '';
            }
            rowData.pq_cellcls = rowData.pq_cellcls || {};

            if (rowData[dataIndx] > 0){
                rowData.pq_cellcls[dataIndx] = 'high-priority';
                return "<span class='ui-icon ui-icon-alert'> </span>" + buttons;
            }else {
                return buttons;
            }
        }
    };
}

function getStatusColumn() {
    return {
        title: "Статус",
        dataIndx: 'status',
        dataType: "integer",
        cls:'buttons-here',
        editor: {
            type: 'select',
            init: function (ui) {
                ui.$cell.find("select").find('option').each(function(){
                    if( $(this).val()==4){
                        $(this).attr('disabled','disabled');
                    }
                    if($(this).val()==5 && ui.rowData.status!==6){
                        $(this).attr('disabled','disabled');
                    }
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
        filter: { type: 'select',
            condition: 'equal',
            //init: multiSelect,
            prepend: { '': 'Любой' },
            listeners: ['change'],
            valueIndx: "value",
            labelIndx: "text",
            mapIndices: {"text": "Статус", "value": "status"},
            options: getStatusesArray()
        }
    };
}


function getActionsColumn(type){
    return {
        title: "",
        dataIndx: 'actionsColumn',
        editable: false,
        sortable: false,
        cls:'buttons-here',
        maxWidth: 60,
        minWidth: 60,
        render: function (ui) {
            let rowData = ui.rowData,
                dataIndx = ui.dataIndx;
            if(rowData['status'] == 4 || rowData['status'] == 5){
                return '';
            }
            return "<button type='button' class='delete_"+type+"_btn ui-button'>Удалить</button>";
        },
    };
}


//=============================Common Table Settings
function getClearFilterButton() {
    return {
        type: 'button',
        label: ' ',
        icon: 'ui-icon-trash',
        listeners: [
            {
                'click': clearFilter,
            }
        ]
    };
}

function getFilterWord() {
    return {
        type: "<span style='margin:5px;'>Фильтр</span>"
    };
}

function getPageModel() {
    return {
        curPage: 1,
        type: "remote",
        rPP: 10,
        strRpp: "{0}",
        strDisplay: "с {0} до {1} из {2}",
        rPPOptions: function () {
            let rpp = [5, 10, 20];
            for (let i=0; i<8; i++){
                rpp.push(rpp[i]*10);
            }
            return rpp;
        }()
    };
}