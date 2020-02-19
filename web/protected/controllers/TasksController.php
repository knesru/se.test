<?php

class TasksController extends Controller
{


    public function actionIndex()
    {
        $tasks_model = Tasks::model();
        $products_model = Products::model();
        $this->render('index', compact('tasks_model', 'products_model'));
    }

    public function actionRegistryList()
    {
        $_GET['pq_curpage'] = Yii::app()->request->getParam('pq_curpage');
        /** @var Tasks $model */
        $model = Tasks::model();
        $result = array('data' => array());
        $showall = Yii::app()->request->getPost('showall', true);
        $showall = ($showall == 'true' ? true : false);
        $dp = $model->getTasks(
            array(
                'page_size' => Yii::app()->request->getParam('pq_rpp', 10),
                'filter' => Yii::app()->request->getParam('pq_filter', array()),
                'show_closed' => $showall
            )
        );
        $sort_attrs = array();
        foreach (Yii::app()->request->getParam('pq_sort') as $sort_item) {
            $sort_attrs[] = $sort_item['dataIndx'] . ($sort_item['dir'] == 'down' ? '.desc' : '');
        }
        $_GET['sort'] = implode('-', $sort_attrs);
        if ($sort_item['dataIndx'] !== 'id') {
            $_GET['sort'] .= '-' . 'id';
        }
        $dp->setSort(array(
            'class' => 'CSort',
            'multiSort' => true,
            'sortVar' => 'sort',
            'attributes' => array('*',
                'user' => array(
                    'asc' => 'userinfo.fullname, "user".username',
                    'desc' => 'userinfo.fullname desc, "user".username desc'
                ),
//                'partnumber' => array(
//                    'asc' => 't.partnumber',
//                    'desc' => 't.partnumber desc',
//                ),
//                'partnumberid' => array(
//                    'asc' => 't.partnumberid',
//                    'desc' => 't.partnumberid desc',
//                ),
            ),
            'defaultOrder' => 'name asc'
        ));
        /** @var Tasks $request */
        $colors = Tasks::getStatusesColors();

        /** @var Tasks $task */
        foreach ($dp->getData() as $task) {
            $row = $task->attributes;
            if (isset($task->user) && isset($task->user->userinfo) && isset($task->user->userinfo->fullname)) {
                $row['user'] = $task->user->userinfo->fullname;
            }
            $row['pq_rowcls'] = $colors[$row['status']];
            if (empty($row['user'])) {
                $row['user'] = $task->user->username;
            }
            $row['packages'] = $task->packages;
            $result['data'][] = $row;
        }
        $result['totalRecords'] = $dp->totalItemCount;
        $result['curPage'] = Yii::app()->request->getParam('pq_curpage');
        if ($result['curPage'] <= 1) {
            $result['curPage'] = 1;
        }
        print json_encode($result);
    }

    public function actionGetTasksTree()
    {
        $tasks = Tasks::model()->findAll();
        if (is_null($tasks) || count($tasks) == 0) {
            $this->j(array());
        }
        $result = array(
            array(
                'key' => '1',
                'title' => '001.СР.19',
                'folder' => true,
                'lazy' => true,
                /*'children'=>array(
                    array(
                        'key'=>'2',
                        'title'=>'Получатель 1',
                        'folder'=>true,
                        'lazy'=>true,
                        'children'=>array(
                            array(
                                'key'=>'3',
                                'title'=>'Изделие 1',
                            ),
                        )
                    ),
                )*/
            ),
        );
        print json_encode($result);
    }

    public function actionGetTask()
    {
        $taksid = Yii::app()->request->getPost('taskid');
        /** @var Tasks $taskModel */
        $taskModel = Tasks::model()->findByPk($taksid);
        $properties = array();
        if(!is_null($taskModel)){
            $properties = $taskModel->attributes;
            $properties['packages'] = $taskModel->packages;
            $properties['model'] = get_class($taskModel);
            $this->j(array('data'=>$properties));
        }
        $this->j($properties,false);
    }

    public function actionUpdate()
    {
        $task_data = Yii::app()->request->getPost('Tasks');
        $task = null;

        $t = Tasks::model()->find();
        if (is_null($t)) {
            $new_id = 1;

        }


        foreach ($task_data as $key => $val) {
            if (empty($task_data[$key])) {
                unset($task_data[$key]);
            }
        }
        if (isset($task_data['id'])) {
            $task = Tasks::model()->findByPk($task_data['id']);
        }
        if (is_null($task)) {
            $task = new Tasks();
        }
        $task->attributes = $task_data;
        if (empty($task->name)) {
            $task->name = Tasks::getNextPrefix();
        }
        if (!$task->save()) {
            $this->j(array('message' => 'Не удалось сохранить задачу', 'error' => $task->errors), false);
        }
        $products_tree = json_decode(Yii::app()->request->getPost('products_tree'));
        Products::model()->deleteAllByAttributes(array('taskid'=>$task->id));
        foreach ($products_tree as $acceptor => $items) {
            if (!empty($acceptor) && $acceptor != '_empty_') {
                $acceptorModel = Acceptors::model()->findByAttributes(array('name' => $acceptor));
                if (empty($acceptorModel)) {
                    $acceptorModel = new Acceptors();
                    $acceptorModel->name = $acceptor;
                    $acceptorModel->created_at = date('Y.m.d H:i:s');
                    $acceptorModel->userid = Yii::app()->user->id;
                    if (!$acceptorModel->save()) {
                        $this->j(array('message' => 'Не удалось сохранить получателя', 'error' => $acceptorModel->errors), false);
                    }
                }
                foreach ($items as $item) {
//                    $productsModel = Products::model()->findByAttributes(array('name'=>$item->name,'taskid'=>$task->id,'acceptorid'=>$acceptorModel->id));
//                    if(empty($productsModel)) {
                        $productsModel = new Products();
//                    }
                    $productsModel->name = $item->name;
                    $productsModel->amount = $item->amount;
                    $productsModel->units = $item->units;
                    $productsModel->acceptorid = $acceptorModel->id;
                    $productsModel->created_at = date('Y.m.d H:i:s');
                    $productsModel->taskid = $task->id;
                    if (!$productsModel->save()) {
                        $this->j(array('message' => 'Не удалось сохранить изделие', 'error' => $productsModel->errors, 'item' => $item), false);
                    }
                }
            }
        }
        print json_encode(array('success' => true, 'task' => $task, 'pt' => $products_tree));
        Yii::app()->end();
    }
    // Uncomment the following methods and override them if needed
    /*
    public function filters()
    {
        // return the filter configuration for this controller, e.g.:
        return array(
            'inlineFilterName',
            array(
                'class'=>'path.to.FilterClass',
                'propertyName'=>'propertyValue',
            ),
        );
    }

    public function actions()
    {
        // return external action classes, e.g.:
        return array(
            'action1'=>'path.to.ActionClass',
            'action2'=>array(
                'class'=>'path.to.AnotherActionClass',
                'propertyName'=>'propertyValue',
            ),
        );
    }
    */
}