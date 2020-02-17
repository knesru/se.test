<?php
/**
 * @var $this TasksController
 * @var Tasks $tasks_model
 * @var Products $products_model
 */

$this->breadcrumbs=array(
	'Tasks',
);
$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->getClientScript();
$min = '.dev';
$cs->registerCssFile($baseUrl . '/js/pq/pqselect.bootstrap.min.css');
$cs->registerCssFile($baseUrl . '/js/pq/pqgrid'.$min.'.css');
$cs->registerCssFile($baseUrl . '/js/pq/pqselect.min.css');
$cs->registerCssFile($baseUrl . '/js/pq/themes/office/pqgrid.css');
?>

<link href="../js/fancytree/src/skin-win8/ui.fancytree.css" rel="stylesheet" type="text/css">
<script src="../js/jquery.js" type="text/javascript"></script>
<script src="../js/jquery-ui.min.js" type="text/javascript"></script>
<script src="../js/pq/pqgrid.dev.js" type="text/javascript"></script>
<script src="../js/pq/pqselect.min.js" type="text/javascript"></script>
<script src="../js/fancytree/src/jquery.fancytree.js" type="text/javascript"></script>
<script src="../js/commonColumns.js" type="text/javascript"></script>
<script src="../js/common.js" type="text/javascript"></script>




<script type="text/javascript">
    let controlData = {};
    let $registryGrid;
    let $tasksTree;
    // let $storeCorrectionGrid;
    // let formHashes = {};
    $(function(){
        $('.ui-button').css('padding','3px').not('.ui-selectmenu-button').css({
            'padding-right':'5px'
        });
        $( "#tasks_tabs" ).tabs();
    });
    function getUsersArray() {
        return <?php
            $criteria = new CDbCriteria();
            $criteria->with = array(
                'userinfo' => array('together' => true,),
            );
            $criteria->order = 'userinfo.fullname, t.username';
            $listUsers = array();
            $listUsersModels = User::model()->findAll($criteria);
            foreach ($listUsersModels as $userModel) {
                $username = $userModel->username;
                if(!empty($userModel->userinfo->fullname)){
                    $username = $userModel->userinfo->fullname;
                }
                $listUsers[] = array('value'=>$username,'text'=>$username);
            }
            print json_encode($listUsers);
            ?>;
    }
    function getStatusesArray() {
        return [<?php
            $out = array();
            foreach (Tasks::getStatuses() as $status=>$label) {
                $out[] = sprintf('{"value": %d, "text": "%s"}'."\n",$status, $label);
            }
            print implode(',',$out);
            ?>
        ];
    }
    function getStatusesMatrix() {
        return <?php print json_encode(Tasks::getStatusesMatrix());?>;
    }
    function canChangeStatus(to, from)
    {
        if((''+from)===(''+to)){
            return true;
        }

        to = ''+to;
        from = ''+from;

        let matrix = getStatusesMatrix();
        if(typeof matrix[from] === "undefined"){
            return false;
        }
        if(typeof matrix[from][to] === "undefined"){
            return false;
        }
        return matrix[from][to]==='allow';
    }
</script>

<div id="tasks_tabs">
    <ul>
        <li><a href="#tabs-1">Реестр заказов</a></li>
        <li><a href="#tabs-2">Задачи</a></li>
    </ul>
    <div id="tabs-1">
        <?php $this->renderPartial('tab_registry'); ?>
    </div>
    <div id="tabs-2">
        <?php $this->renderPartial('tab_tasks'); ?>
    </div>
</div>
<?php
$this->renderPartial('mw_new_task/mw_new_task', compact('tasks_model','products_model'));

