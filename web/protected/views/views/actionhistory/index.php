fkldsafkdsl;fksdl;<?php
/* @var $this ActionhistoryController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Actionhistories',
);

$this->menu=array(
	array('label'=>'Create Actionhistory', 'url'=>array('create')),
	array('label'=>'Manage Actionhistory', 'url'=>array('admin')),
);
?>

<h1>Actionhistories</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>

<script type="application/javascript">
alert('sss');
  $(function(){
  $('body').css({'overflow':'scroll'});
});
</script>
