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
            {title: "ID", dataType: "integer"},
            {title: "Партномер", dataType: "string"},
            {title: "ID компонента", dataType: "string", align: "right"},
            {title: "Кол-во", dataType: "integer", align: "right"},
            {title: "Пользователь", dataType: "string"},
            {title: "Назначение", dataType: "string"},
            {title: "Добавлено", dataType: "date"},
            {title: "Сдано", dataType: "integer", align: "right"},
            {title: "Скомплектовать до", dataType: "date"},
            {title: "Монтаж до", dataType: "date"},
            {title: "Дефицит", dataType: "string"},
            {title: "Примечание", dataType: "string"},
            {title: "Монтаж с", dataType: "date"},
            {title: "Приоритет", dataType: "integer"}
        ];

        let arbitraryComponentsTable = {
            width: "100%",
            showTitle: false,
            toolbar: {
                items: [
                    { type: 'button', icon: 'ui-icon-plus', label: 'New Product', listener:
                            { "click": function (evt, ui) {
                                    //append empty row at the end.
                                    var $grid = $(this).closest('.pq-grid');
                                    var rowData = [0,0,0,0,0,0,0,0,0,0]; //empty row
                                    var rowIndx = $grid.pqGrid("addRow", { rowData: rowData });
                                    $grid.pqGrid("goToPage", { rowIndx: rowIndx });
                                    $grid.pqGrid("setSelection", null);
                                    $grid.pqGrid("setSelection", { rowIndx: rowIndx, dataIndx: 1 });
                                    $grid.pqGrid("editFirstCellInRow", { rowIndx: rowIndx });
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
                        type: "<span class='saving'>Saving...</span>"
                    }
                ]
            },
            resizable: false,
            draggable: false,
            scrollModel: { autoFit: true },
            numberCell: { show: false }
        };
        arbitraryComponentsTable.colModel = [
            {title: "Заявка", dataType:"string", dataIndx:14},
            {title: "ID", dataType: "integer"},
            {title: "Партномер", dataType: "string"},
            {title: "ID компонента", dataType: "string", align: "right"},
            {title: "Кол-во", dataType: "integer", align: "right"},
            {title: "Пользователь", dataType: "string"},
            {title: "Назначение", dataType: "string"},
            {title: "Добавлено", dataType: "date",
                editor: {
                    type: 'textbox',
                    init: dateEditor
                },
            },
            {title: "Сдано", dataType: "integer", align: "right"},
            {title: "Скомплектовать до", dataType: "date",
                editor: {
                    type: 'textbox',
                    init: dateEditor
                },
            },
            {title: "Монтаж до", dataType: "date",
                editor: {
                    type: 'textbox',
                    init: dateEditor
                },
            },
            {title: "Дефицит", dataType: "string"},
            {title: "Примечание", dataType: "string"},
            {title: "Монтаж с", dataType: "date",
                editor: {
                    type: 'textbox',
                    init: dateEditor
                },
            },
            {title: "Приоритет", dataType: "integer"}
        ];
        // arbitraryComponentsTable.dataModel = {data: data};

        tableToArray("table.items","#grid_array",arbitraryComponentsTable);
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
<?php $this->widget('zii.widgets.grid.CGridView', array(
    'dataProvider'=>$dataProvider
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
<button type="button" id="select-stms-component" class="ui-button ui-widget ui-corner-all">Add</button>
</div>