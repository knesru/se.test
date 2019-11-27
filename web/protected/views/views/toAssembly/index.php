<?php
/* @var $this ToAssemblyController */
/* @var $dataProviderRequests CActiveDataProvider */
/* @var $dataProviderAssemblies CActiveDataProvider */

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
$cs->registerScriptFile($baseUrl . '/js/jquery.js');
$cs->registerScriptFile($baseUrl . '/js/jquery-ui.min.js');
$cs->registerScriptFile($baseUrl . '/js/pq/pqgrid.min.js');
$cs->registerScriptFile($baseUrl . '/js/common.js');
$cs->registerCssFile($baseUrl . '/js/pq/pqgrid.min.css');
$cs->registerCssFile($baseUrl . '/js/themes/office/pqgrid.css');
?>
<script>
    $(function () {
        let controlData = {};
        controlData.selection = [];
        controlData.prevSelection=null;
        controlData.requestSelection = [];
        /*var stmsComponentsTable = {
            width: "100%",
            title: {show: false},
            resizable: false,
            draggable: false,
            scrollModel: {autoFit: true},
            numberCell: {show: true}
        };*/
        let groupModel = {
            dataIndx: ['Заявка'],
            collapsed: [true],
            title: [
                function (groupdata) {
                    return "<b style='font-weight:bold;'>"+groupdata.groupTitle.replace(/^0+/,'')+"</b> ("+groupdata.items+" "+pf(groupdata.items,"сборка","сборки","сборок")+")"
                }
            ],
            dir: ["up"],
            titleCls: ['#dddddd'],
            summaryCls: ['ShipCountry']
            //,icon: ["circle-plus", "circle-triangle", "triangle"]
        };
        /*stmsComponentsTable.colModel = [
            {title: "Заявка", dataType: "string", dataIndx: 14},
            {title: "ID", dataType: "integer", dataIndx: 0},
            {title: "Наименование", dataType: "string", dataIndx: 1},
            {title: "ID компонента", dataType: "string", align: "right", dataIndx: 2},
            {title: "Кол-во", dataType: "integer", align: "right", dataIndx: 3},
            {title: "Пользователь", dataType: "string", dataIndx: 4},
            {title: "Назначение", dataType: "string", dataIndx: 5},
            {title: "Добавлено", dataType: "date", dataIndx: 6},
            {title: "Сдано", dataType: "integer", align: "right", dataIndx: 7},
            {title: "Скомплектовать до", dataType: "date", dataIndx: 8},
            {title: "Монтаж до", dataType: "date", dataIndx: 9},
            {title: "Дефицит", dataType: "string", dataIndx: 10},
            {title: "Примечание", dataType: "string", dataIndx: 11},
            {title: "Монтаж с", dataType: "date", dataIndx: 12},
            {title: "Приоритет", dataType: "integer", dataIndx: 13}
        ];*/

        let arbitraryComponentsTable = {
            width: "100%",
            showTitle: false,
            height: '300',
            pageModel: {
                type: "local",
                rPP: 10,
                strRpp: "{0}",
                strDisplay: "с {0} до {1} из {2}",
                rPPOptions: [1,2,5,10, 20, 50, 100, 500, 1000, 2000, 5000, 10000]
            },
            scrollModel: {
                autoFit: true,
                //flexContent: true,
                horizontal: false
            },
            selectionModel: {
                type: 'row',
                mode: 'range',
                fireSelectChange: true
            },
            toolbar: {
                items: []
            },
            resizable: false,
            draggable: false,
            numberCell: {show: false},
            change: function (evt, ui) {
                //debugger;
                if (ui.source == 'commit' || ui.source == 'rollback') {
                    return;
                }
                //console.log(ui);
                var $grid = $(this),
                    grid = $grid.pqGrid('getInstance').grid;
                var rowList = ui.rowList,
                    addList = [],
                    recIndx = grid.option('dataModel').recIndx,
                    deleteList = [],
                    updateList = [];

                for (var i = 0; i < rowList.length; i++) {
                    var obj = rowList[i],
                        rowIndx = obj.rowIndx,
                        newRow = obj.newRow,
                        type = 'update',//obj.type,
                        rowData = obj.rowData;
                    if (type == 'add') {
                        var valid = grid.isValid({rowData: newRow, allowInvalid: true}).valid;
                        if (valid) {
                            addList.push(newRow);
                        }
                    }
                    else if (type == 'update') {
                        var valid = grid.isValid({rowData: rowData, allowInvalid: true}).valid;
                        if (valid) {
                            if (rowData[recIndx] == null) {
                                updateList.push(rowData);
                            }
                            //else if (grid.isDirty({rowData: rowData})) {
                            else {
                                updateList.push(rowData);
                            }
                        }
                    }
                    else if (type == 'delete') {
                        if (rowData[recIndx] != null) {
                            deleteList.push(rowData);
                        }
                    }
                }
                if (addList.length || updateList.length || deleteList.length) {
                    $.ajax({
                        url: '<?php print Yii::app()->controller->createUrl('toassembly/update') ?>',
                        data: {
                            list: JSON.stringify({
                                updateList: updateList,
                                addList: addList,
                                deleteList: deleteList
                            })
                        },
                        dataType: "json",
                        type: "POST",
                        async: true,
                        beforeSend: function (jqXHR, settings) {
                            $(".saving", $grid).show();
                        },
                        success: function (changes) {
                            //commit the changes.
                            grid.commit({type: 'add', rows: changes.addList});
                            grid.commit({type: 'update', rows: changes.updateList});
                            grid.commit({type: 'delete', rows: changes.deleteList});
                        },
                        complete: function () {
                            $(".saving", $grid).hide();
                        }
                    });
                }
            }
        };
        let arbitraryComponentsTableToolbar = [
            {
                type: 'button', icon: 'ui-icon-plus', label: 'Добавить', listener:
                    {
                        "click": function (evt, ui) {
                            window.open('./toAssembly/create');
                            return;
                            //append empty row at the end.
                            var $grid = $(this).closest('.pq-grid');
                            var rowData = [null]; //empty row
                            var rowIndx = $grid.pqGrid("addRow", {rowData: rowData});
                            $grid.pqGrid("goToPage", {rowIndx: rowIndx});
                            $grid.pqGrid("setSelection", null);
                            $grid.pqGrid("setSelection", {rowIndx: rowIndx, dataIndx: 14});
                            //$grid.pqGrid("editFirstCellInRow", { rowIndx: rowIndx, dataIndx: 6 });
                        }
                    }
            },
            {
                type: 'button', attr: 'id="requestbutton"', icon: 'ui-icon-plus', label: 'Создать заявку',
                listener:
                    {
                        "click": function (evt, ui) {
                            if (typeof controlData.selection !== 'undefined') {
                                let datM = $("#grid_array_assemblies").pqGrid("option", "dataModel");
                                let grid = $("#grid_array_assemblies").pqGrid();
                                //console.log('datM');

                                $.ajax({
                                    url: '<?php print Yii::app()->controller->createUrl('toassembly/request') ?>',
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
            },
            {type: 'separator'},
            {
                type: 'button', icon: 'ui-icon-arrowreturn-1-s', label: 'Undo', cls: 'changes', listener:
                    {
                        "click": function (evt, ui) {
                            $grid.pqGrid("history", {method: 'undo'});
                        }
                    },
                options: {disabled: true}
            },
            {
                type: 'button', icon: 'ui-icon-arrowrefresh-1-s', label: 'Redo', listener:
                    {
                        "click": function (evt, ui) {
                            $grid.pqGrid("history", {method: 'redo'});
                        }
                    },
                options: {disabled: true}
            },
            {
                type: "<span class='saving ui-state-highlight ui-corner-all'>Сохранение...</span>"
            },
            {
                type: "<div class='summary'></div>",
            }
        ];
        let requestsTableToolbar = [
            {
                type: 'button', icon: 'ui-icon-minus', label: 'Убрать', listener:
                    {
                        "click": function (evt, ui) {
                            if (typeof controlData.requestSelection !== 'undefined') {
                                let grid = $("#grid_array_assemblies").pqGrid();
                                $.ajax({
                                    url: '<?php print Yii::app()->controller->createUrl('toassembly/removeComponent') ?>',
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
            },
            {
                type: 'button', icon: 'ui-icon-folder-open', label: 'Группировка', listener:
                    {
                        "click": function (evt, ui) {
                            var $grid = $("#grid_array"),
                                gm = $grid.pqGrid('option', 'groupModel');
                            if (gm) {
                                var data = $grid.pqGrid('option', "dataModel").data;
                                for (var i = 0; i < data.length; i++) {
                                    //show all collapsed rows
                                    data[i].pq_hidden = false;
                                }
                                $grid.pqGrid('option', "groupModel", null).pqGrid('refreshView');
                            }
                            else {
                                $grid.pqGrid('option', "groupModel", groupModel).pqGrid('refreshView');
                            }
                        }
                    }
            },
            {
                type: 'button',
                label: "Экспорт в Excel",
                icon: 'ui-icon-document',
                listeners: [{
                    "click": function (evt) {
                        $("#grid_array").pqGrid("exportExcel", {url: "/toAssembly/export", sheetName: "Заявки"});
                    }
                }]
            },
            {type: 'separator'},
            {
                type: 'button', icon: 'ui-icon-arrowreturn-1-s', label: 'Undo', cls: 'changes', listener:
                    {
                        "click": function (evt, ui) {
                            $grid.pqGrid("history", {method: 'undo'});
                        }
                    },
                options: {disabled: true}
            },
            {
                type: 'button', icon: 'ui-icon-arrowrefresh-1-s', label: 'Redo', listener:
                    {
                        "click": function (evt, ui) {
                            $grid.pqGrid("history", {method: 'redo'});
                        }
                    },
                options: {disabled: true}
            },
            {
                type: "<span class='saving ui-state-highlight ui-corner-all'>Сохранение...</span>"
            },
            {
                type: "<div class='summary'></div>",
            }
        ];
        let requestsTable = $.extend({}, arbitraryComponentsTable);
        requestsTable.toolbar = {};
        arbitraryComponentsTable.toolbar = {};
        requestsTable.toolbar.items = requestsTableToolbar;
        requestsTable.selectionModel.mode = 'single';
        arbitraryComponentsTable.toolbar.items = arbitraryComponentsTableToolbar;
        //requestsTable.showBottom = false;
        arbitraryComponentsTable.roundCorners = false;
        requestsTable.colModel = arbitraryComponentsTable.colModel = [
            {
                title: "Заявка", dataType: "string",
                render: function (ui) {
                    var rowData = ui.rowData,
                        dataIndx = ui.dataIndx;
                    return rowData[dataIndx].replace(/^0+/, '');
                },
                sortType: function (rowData1, rowData2, dataIndx) {
                    var val1 = rowData1[dataIndx],
                        val2 = rowData2[dataIndx],
                        data1 = $.trim(val1).split('.'),
                        data2 = $.trim(val2).split('.');

                    var c1 = parseInt(data1[0]),
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
                dataIndx: 14, editable: false
            },
            {title: "ID", dataType: "integer", dataIndx: 0, editable: false},
            {
                title: "Наименование", dataType: "string", dataIndx: 1,
                editor: {
                    type: 'textbox',
                    init: function (ui) {
                        //console.log('inline');
                        var $inp = ui.$cell.find("input");
                        var url = ui.column.editor.url;

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
            {title: "ID компонента", dataType: "string", align: "right", dataIndx: 2},
            {title: "Кол-во", dataType: "integer", align: "right", dataIndx: 3},
            {title: "Пользователь", dataType: "string", dataIndx: 4},
            {title: "Назначение", dataType: "string", dataIndx: 5},
            {
                title: "Добавлено", dataType: "date",
                editor: {
                    type: 'textbox',
                    init: dateEditor
                }, dataIndx: 6
            },
            {title: "Сдано", dataType: "integer", align: "right", dataIndx: 7},
            {
                title: "Скомплектовать до", dataType: "date",
                editor: {
                    type: 'textbox',
                    init: dateEditor
                }, dataIndx: 8
            },
            {
                title: "Монтаж до", dataType: "date",
                editor: {
                    type: 'textbox',
                    init: dateEditor
                }, dataIndx: 9
            },
            {title: "Дефицит", dataType: "string", dataIndx: 10},
            {title: "Примечание", dataType: "string", dataIndx: 11},
            {
                title: "Монтаж с", dataType: "date",
                editor: {
                    type: 'textbox',
                    init: dateEditor
                }, dataIndx: 12
            },
            {title: "Приоритет", dataType: "integer", dataIndx: 13},
            // { title: "Статус", dataType: "integer", dataIndx: 14,
            //     //custom editor.
            //     editor: {
            //         options: [
            //             'Неактивен',
            //             'Комплектация',
            //             'Скомпонован',
            //             'На монтаже',
            //             'Закрыт',
            //             'Отмена'
            //         ],
            //         type: function (ui) {
            //             //debugger;
            //             var str = "",
            //                 options = ui.column.editor.options;
            //             $(options).each(function (i, option) {
            //                 var checked = '';
            //                 if (option == ui.cellData) {
            //                     checked = "selected = 'selected'";
            //                 }
            //                 str += "<option id='status_changer' " + checked + " ' style='margin-left:5px;' value='" + i + "'>  " + option+"</option>";
            //             });
            //             ui.$cell.append("<div class='pq-editor-focus' style='background: white' tabindex='0' style='padding:5px;'><select name='" + ui.dataIndx + "'>" + str + "</select></div>");
            //             $('#status_changer').click(function(e){
            //                 e.stopPropagation();
            //                 e.stopImmediatePropagation();
            //             });
            //         },
            //         getData: function (ui) {
            //             console.log($("select[name='" + ui.dataIndx + "']").val());
            //             return $("select[name='" + ui.dataIndx + "']").val();
            //         }
            //     },
            //     render: function (ui) {
            //         var rowData = ui.rowData,
            //             dataIndx = ui.dataIndx;
            //         let options = ui.column.editor.options;
            //         return options[rowData[dataIndx]];
            //     },
            // },
            { title: "Статус", dataType: "integer", dataIndx: 14,
                //custom editor.
                editor: {
                    type: 'select',
                    init: function (ui) {
                        ui.$cell.find("select").pqSelect();
                    },
                    valueIndx: "value",
                    labelIndx: "text",
                    mapIndices: {"text": "Статус", "value": "status"},
                    options: [
                        {"value":0,"text":'Неактивен'},
                        {"value":1,"text":'Комплектация'},
                        {"value":2,"text":'Скомпонован'},
                        {"value":3,"text":'На монтаже'},
                        {"value":4,"text":'Закрыт'},
                        {"value":5,"text":'Отмена'}
                            ]
                },
                render: function (ui) {
                    var rowData = ui.rowData,
                        dataIndx = ui.dataIndx;
                    let options = ui.column.editor.options;
                    return options[rowData[dataIndx]];
                },
            },
        ];


        arbitraryComponentsTable.selectChange = function (evt, ui) {
            var rows = ui.rows;
            controlData.selection = [];
            if (rows && rows.length) {
                for (var i = 0; i < rows.length; i++) {
                    //console.log(rows[i].rowData);
                    controlData.selection.push(rows[i].rowData.ID);
                }
            }
        };
        requestsTable.selectChange = function (evt, ui) {
            var rows = ui.rows;
            if(typeof controlData.requestSelection[0]!=='undefined') {
                controlData.prevSelection = controlData.requestSelection[0];
            }
            controlData.requestSelection = [];
            if (rows && rows.length) {
                for (var i = 0; i < rows.length; i++) {
                    //console.log(rows[i].rowData);
                    controlData.requestSelection.push(rows[i].rowData.ID);
                }
            }
            if(controlData.prevSelection===controlData.requestSelection[0]){
                $( "#grid_array" ).pqGrid( "setSelection", null );
                controlData.prevSelection = null;
            }
            if(controlData.requestSelection.length>0){
                $('#requestbutton').find('span.ui-button-text').text('Добавить в заявку');
            }else {
                $('#requestbutton').find('span.ui-button-text').text('Создать заявку');
            }
        };

        //requestsTable.groupModel = groupModel;

        tableToArray("#requestsTable table.items", "#grid_array", requestsTable);
        tableToArray("#assembliesTable table.items", "#grid_array_assemblies", arbitraryComponentsTable);

        $('.summary').hide();
        $('.grid-view').hide();
        // $("#grid_array").pqGrid(arbitraryComponentsTable);
        var element = document.querySelector('#grid_array'),
            list_of_events = $._data(element, "events");
        var antirecursion = false;
        $("#grid_array").on("pqgridrefresh", function (event, ui) {
            if (!antirecursion) {
                antirecursion = true;
                syncColM("#grid_array", "#grid_array_assemblies");
            }
            antirecursion = false;
        });
        $("#grid_array_assemblies").on("pqgridrefresh", function (event, ui) {
            if (!antirecursion) {
                antirecursion = true;
                syncColM("#grid_array_assemblies", "#grid_array");
            }
            antirecursion = false;
        });

        function syncColM(grid1, grid2) {
            var colM = $(grid1).pqGrid("option", "colModel");
            $(grid2).pqGrid("option", "colModel", colM).pqGrid("refresh");
        }

    });

</script>
<?php
$columns = array(
    'requestid',
    'id',
    array(
        'name'=>'partnumber',
        'value'=>function($data,$row){
            /** @var Extcomponents $data */
            if ($data->component->partnumber) {
                return $data->component->partnumber;
            }
            return $data->partnumber;
        }
    ),
    'partnumberid',
    'amount',
    array(
        'name'=>'user',
        'value'=>function($data,$row){
            /** @var Extcomponents $data */
            return $data->user->userinfo->fullname;
        }
    ),
    'purpose',
    'created_at',
    'delivered',
    'assembly_to',
    'install_to',
    'deficite',
    'description',
    'install_from',
    'priority',
    'status'
);

$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'requestsTable',
    'dataProvider' => $dataProviderRequests,
    'columns' => $columns
));
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'assembliesTable',
    'dataProvider' => $dataProviderAssemblies,
    'columns' => $columns
));
?>
<!--<div  style="height: 300px; overflow-y: scroll">-->
    <div id="grid_array"></div>
<!--</div>-->
<!--<div  style="height: 300px; overflow-y: scroll">-->
<div id="grid_array_assemblies" style="height: 300px"></div>
<!--</div>-->
<div title="Добавить компонент из stms" id="popup-stms-component" style="overflow:hidden;">
    <div id="grid-stms-component"></div>
</div>
<script type="application/javascript">
    $(function () {
        // $( ".widget input[type=submit], .widget a, .widget button" ).button();
        // $( "button, input, a" ).click( function( event ) {
        //     event.preventDefault();
        // } );
    });
</script>
<div class="widget">
    <!--<button type="button" id="select-stms-component" class="ui-button ui-widget ui-corner-all">Add</button>-->
</div>