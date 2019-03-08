<?php
/* @var $this UserController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Users',
);

$this->menu=array(
	array('label'=>'Create User', 'url'=>array('create')),
	array('label'=>'Manage User', 'url'=>array('admin')),
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

<h1>Users</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
    'id'=>'xxx',
)); ?>
<div id="grid_table"></div>

<script type="application/javascript">
    $(function(){
        tableToArray("table.items","#grid_table",{
            colModel:[
                { title: "Id", width: 50, dataType: "integer" },
                { title: "Username", width: 109, dataType: "string" },
                // { title: "Algorithm", width: 90, dataType: "string" },
                // { title: "Salt", width: 372, dataType: "string" },
                // { title: "Password", width: 462, dataType: "string" },
                { title: "Active", width: 70, dataType: "bool", dataIndx: 5 },
                { title: "SuperAdmin", width: 116, dataType: "bool", dataIndx: 6 },
                { title: "Last login", width: 193, dataType: "datetime", dataIndx: 7 },
                { title: "Created At", width: 193, dataType: "datetime", dataIndx: 8 },
                { title: "Update date", width: 193, dataType: "datetime", dataIndx: 9 }
            ],
            width: 'auto',
            height: '400',
            title: "Grid From Table",
            showTitle: false,
            scrollModel: { autoFit: true },
            numberCell: { show: false }
        });
        /*var tbl = $();
        tbl.find('th').each(function(){
            $(this).replaceWith('<td>'+$(this).find('a').text()+'</td>');
        });
        // tbl.find('thead').replaceWith();
        tbl.find('tbody').prepend(tbl.find('thead').find('tr'));
        tbl.find('thead').remove();
        //tbl.find('tbody').replaceWith(tbl.find('tbody').find('tr'));
        tbl.find('tr').removeClass('odd').removeClass('even');
        var obj = $.paramquery.tableToArray(tbl);
        var newObj = { width: 'auto', height: '400', title: "Grid From Table", flexWidth: true };
        //return;
        newObj.dataModel = { data: obj.data };
        newObj.colModel = ;
        //newObj.pageModel = { rPP: 20, type: "local" };
        $("#grid_table").pqGrid(newObj);
        tbl.css("display", "none");*/
    });
</script>
