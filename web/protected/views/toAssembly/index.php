<?php
/* @var $this ToAssemblyController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs = array(
    'Extcomponents',
);

$this->menu = array(
    array('label' => 'Create Extcomponents', 'url' => array('create')),
    array('label' => 'Manage Extcomponents', 'url' => array('admin')),
);
$baseUrl = Yii::app()->baseUrl;
/** @var CClientScript $cs */
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile($baseUrl . '/js/jquery-1.8.3.js');
$cs->registerScriptFile($baseUrl . '/js/jquery-ui-1.9.2.custom.min.js');
$cs->registerScriptFile($baseUrl . '/js/pq/pqgrid.min.js');
$cs->registerScriptFile($baseUrl . '/js/common.js');
$cs->registerCssFile($baseUrl . '/js/pq/pqgrid.min.css');
$cs->registerCssFile($baseUrl . '/js/themes/office/pqgrid.css');
?>
<script>
    $(function () {
        let selection = null;
        var stmsComponentsTable = {
            width: "100%",
            title: {show: false},
            resizable: false,
            draggable: false,
            scrollModel: { autoFit: true },
            numberCell: { show: true }
        };
        stmsComponentsTable.colModel = [
            {title: "Заявка", dataType:"string", dataIndx:14},
            {title: "ID", dataType: "integer", dataIndx:0},
            {title: "Партномер", dataType: "string", dataIndx:1},
            {title: "ID компонента", dataType: "string", align: "right", dataIndx:2},
            {title: "Кол-во", dataType: "integer", align: "right", dataIndx:3},
            {title: "Пользователь", dataType: "string", dataIndx:4},
            {title: "Назначение", dataType: "string", dataIndx:5},
            {title: "Добавлено", dataType: "date", dataIndx:6},
            {title: "Сдано", dataType: "integer", align: "right", dataIndx:7},
            {title: "Скомплектовать до", dataType: "date", dataIndx:8},
            {title: "Монтаж до", dataType: "date", dataIndx:9},
            {title: "Дефицит", dataType: "string", dataIndx:10},
            {title: "Примечание", dataType: "string", dataIndx:11},
            {title: "Монтаж с", dataType: "date", dataIndx:12},
            {title: "Приоритет", dataType: "integer", dataIndx:13}
        ];

        let arbitraryComponentsTable = {
            width: "100%",
            showTitle: false,
            toolbar: {
                items: [
                    { type: 'button', icon: 'ui-icon-plus', label: 'Добавить', listener:
                            { "click": function (evt, ui) {
                                    window.open('./toAssembly/create');
                                    return;
                                    //append empty row at the end.
                                    var $grid = $(this).closest('.pq-grid');
                                    var rowData = [null]; //empty row
                                    var rowIndx = $grid.pqGrid("addRow", { rowData: rowData });
                                    $grid.pqGrid("goToPage", { rowIndx: rowIndx });
                                    $grid.pqGrid("setSelection", null);
                                    $grid.pqGrid("setSelection", { rowIndx: rowIndx, dataIndx: 14 });
                                    //$grid.pqGrid("editFirstCellInRow", { rowIndx: rowIndx, dataIndx: 6 });
                                }
                            }
                    },
                    { type: 'button', icon: 'ui-icon-plus', label: 'Создать заявку', listener:
                            { "click": function (evt, ui) {
                                    if(typeof selection !== 'undefined'){
                                        console.log(selection);
                                        let datM = $("#grid_array").pqGrid("option", "dataModel");
                                        let grid = $("#grid_array").pqGrid();
                                        console.log('datM');
                                        console.log(datM.data[selection]['ID']);
                                        $.ajax({
                                            url: '<?php print Yii::app()->controller->createUrl('toassembly/request') ?>',
                                            data: {id:datM.data[selection]['ID']},
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
                    { type: 'separator' },
                    { type: 'button', icon: 'ui-icon-arrowreturn-1-s', label: 'Undo', cls: 'changes', listener:
                            { "click": function (evt, ui) {
                                    $grid.pqGrid("history", { method: 'undo' });
                                }
                            },
                        options: { disabled: true }
                    },
                    { type: 'button', icon: 'ui-icon-arrowrefresh-1-s', label: 'Redo', listener:
                            { "click": function (evt, ui) {
                                    $grid.pqGrid("history", { method: 'redo' });
                                }
                            },
                        options: { disabled: true }
                    },
                    {
                        type: "<span class='saving ui-state-highlight ui-corner-all'>Сохранение...</span>"
                    },
                    {
                        type: "<div class='summary'></div>",
                    }
                ]
            },
            resizable: false,
            draggable: false,
            scrollModel: { autoFit: true },
            numberCell: { show: false },
            change: function (evt, ui) {
                //debugger;
                if (ui.source == 'commit' || ui.source == 'rollback') {
                    return;
                }
                console.log(ui);
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
                        type = 'add',//obj.type,
                        rowData = obj.rowData;
                    if (type == 'add') {
                        var valid = grid.isValid({ rowData: newRow, allowInvalid: true }).valid;
                        if (valid) {
                            addList.push(newRow);
                        }
                    }
                    else if (type == 'update') {
                        var valid = grid.isValid({ rowData: rowData, allowInvalid: true }).valid;
                        if (valid) {
                            if (rowData[recIndx] == null) {
                                addList.push(rowData);
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
                            grid.commit({ type: 'add', rows: changes.addList });
                            grid.commit({ type: 'update', rows: changes.updateList });
                            grid.commit({ type: 'delete', rows: changes.deleteList });
                        },
                        complete: function () {
                            $(".saving", $grid).hide();
                        }
                    });
                }
            }
        };
        arbitraryComponentsTable.colModel = [
            {title: "Заявка", dataType:"string",
                render: function (ui) {
                    var rowData = ui.rowData,
                        dataIndx = ui.dataIndx;
                        return rowData[dataIndx].replace(/^0+/,'');
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

                    if (y1 > y2 || (y1===y2 && c1 > c2)) {
                        return -1;
                    }
                    else if (y1 < y2 || (y1===y2 && c1 < c2)) {
                        return 1;
                    }
                    return 0;
                },
                dataIndx:14, editable: false},
            {title: "ID", dataType: "integer", dataIndx:0, editable: false},
            {title: "Партномер", dataType: "string", dataIndx:1,
                editor: {
                    type: 'textbox',
                    init: function(ui){
                        console.log('inline');
                        var $inp = ui.$cell.find("input");
                        var url = ui.column.editor.url;

                        //initialize the editor
                        $inp.autocomplete({
                            appendTo: ui.$cell, //for grid in maximized state.
                            source: url,
                            selectItem: { on: true }, //custom option
                            highlightText: { on: true }, //custom option
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
            {title: "ID компонента", dataType: "string", align: "right", dataIndx:2},
            {title: "Кол-во", dataType: "integer", align: "right", dataIndx:3},
            {title: "Пользователь", dataType: "string", dataIndx:4},
            {title: "Назначение", dataType: "string", dataIndx:5},
            {title: "Добавлено", dataType: "date",
                editor: {
                    type: 'textbox',
                    init: dateEditor
                }, dataIndx:6
            },
            {title: "Сдано", dataType: "integer", align: "right", dataIndx:7},
            {title: "Скомплектовать до", dataType: "date",
                editor: {
                    type: 'textbox',
                    init: dateEditor
                }, dataIndx:8
            },
            {title: "Монтаж до", dataType: "date",
                editor: {
                    type: 'textbox',
                    init: dateEditor
                }, dataIndx:9
            },
            {title: "Дефицит", dataType: "string", dataIndx:10},
            {title: "Примечание", dataType: "string", dataIndx:11},
            {title: "Монтаж с", dataType: "date",
                editor: {
                    type: 'textbox',
                    init: dateEditor
                }, dataIndx:12
            },
            {title: "Приоритет", dataType: "integer", dataIndx:13}
        ];
        // arbitraryComponentsTable.dataModel = {data: data};
        // arbitraryComponentsTable.selectChange =

        arbitraryComponentsTable.cellSelect = function (evt, ui) {
            if (ui.rowData) {
                var rowIndx = ui.rowIndx,
                    colIndx = ui.colIndx,
                    dataIndx = ui.dataIndx,
                    cellData = ui.rowData[dataIndx];
                selection = rowIndx;
            }
        };

        arbitraryComponentsTable = tableToArray("table.items","#grid_array",arbitraryComponentsTable);

        $('.summary').hide();
        $('.grid-view').hide();
        // $("#grid_array").pqGrid(arbitraryComponentsTable);
        var element = document.querySelector('#grid_array'),
            list_of_events = $._data(element, "events");
        $("#grid_array").on("pqgridrefresh", function (event, ui) {
            var colM = $("#grid_array").pqGrid("option", "colModel");
            var datM = $("#grid_array").pqGrid("option", "dataModel");
            console.log(colM);
            console.log(datM);
        });
        $("button#select-stms-component").button().click(function (evt) {
            $("#popup-stms-component")
                .dialog({
                    height: 400,
                    width: 700,
                    //width: 'auto',
                    modal: true,
                    open: function (evt, ui) {
                        $("#grid-stms-component").pqGrid(stmsComponentsTable);

                    },
                    close: function () {
                        $("#grid-stms-component").pqGrid('destroy');
                    },
                    show: {
                        effect: "blind",
                        duration: 500
                    }
                });
        });
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
            if($data->component->partnumber){
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
    'priority'
);

$this->widget('zii.widgets.grid.CGridView', array(
    'dataProvider'=>$dataProvider,
    'columns'=>$columns
)); ?>
<div id="grid_array"></div>
<div title="Добавить компонент из stms" id="popup-stms-component" style="overflow:hidden;">
    <div id="grid-stms-component"></div>
</div>
<script type="application/javascript">
    $( function() {
        // $( ".widget input[type=submit], .widget a, .widget button" ).button();
        // $( "button, input, a" ).click( function( event ) {
        //     event.preventDefault();
        // } );
    } );
</script>
<div class="widget">
<!--<button type="button" id="select-stms-component" class="ui-button ui-widget ui-corner-all">Add</button>-->
</div>