<?php

/**
 * Class ToAssemblyController
 */
class ToAssemblyController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/main';

    private $lettersArray;

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow',  // allow all users to perform 'index' and 'view' actions
                'actions' => array('index', 'newIndex', 'view', 'export', 'requestslist', 'componentslist', 'storecorrectionlist','statuseslist'),
                'users' => array('*'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('create', 'update', 'receive', 'request', 'replace', 'removecomponent', 'storecorrectionlist'),
                'users' => array('@'),
            ),
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('admin', 'delete','generatesome'),
                'users' => array('admin'),
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model = new Extcomponents;
        $model->created_at = date('Y-m-d H:i:s');
        $model->userid = Yii::app()->user->id;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Extcomponents'])) {
            $data = array($_POST['Extcomponents']);
        } elseif (
            isset($_POST['list']) &&
            ($data = json_decode($_POST['list'], true)) &&
            isset($data['addList']) &&
            count($data['addList']) > 0) {
            $data = $data['addList'];
        }
        if (isset($data) && count($data)>0) {
            $result = array('success'=>true, 'data'=>array());

            foreach ($data as $datum) {
                $model->attributes = $datum;
                if (empty($model->created_at)) {
                    $model->created_at = null;
                }
                if (empty($model->assembly_to)) {
                    $model->assembly_to = null;
                }
                if (empty($model->install_to)) {
                    $model->install_to = null;
                }
                if (empty($model->install_from)) {
                    $model->install_from = null;
                }
                if (empty($model->partnumberid)) {
                    $model->partnumberid = null;
                }
                if (empty($model->priority)) {
                    $model->priority = 0;
                }elseif($model->priority=='on'){
                    $model->priority = 1;
                }
                if ($model->save()) {
                    if (Yii::app()->request->isAjaxRequest) {
                        $result['data'][] = array(
                          'id'=>$model->id,
                          'partnumber'=>$model->partnumber,
                          'success'=>true
                        );
                    }
                } else {
                    if (Yii::app()->request->isAjaxRequest) {
                        $result['data'][] = array(
                          'id'=>$model->id,
                          'partnumber'=>$model->partnumber,
                          'success'=>false,
                          'errors'=>$model->errors
                        );
                        $result['success']=false;
                    }
                }
            }
            $this->j($result['data'],$result['success']);
        }
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id = null)
    {
        if (isset($_POST['Extcomponents'])) {
            $data = $_POST['Extcomponents'];
        } elseif (
            isset($_POST['list']) &&
            ($data = json_decode($_POST['list'], true)) &&
            isset($data['updateList']) &&
            count($data['updateList']) > 0) {
            $data = $data['updateList'][0];
        }elseif (
            isset($_POST['list']) &&
            ($data = json_decode($_POST['list'], true)) &&
            isset($data['addList']) &&
            count($data['addList']) > 0) {
            $data = $data['addList'][0];
        }

        if (isset($_POST['list'])) {
            if ($data = json_decode($_POST['list'], true)) {
                if (isset($data['updateList']) && count($data['updateList']) > 0) {
                    if (isset($data['updateList'][0]['id'])) {
                        $id = $data['updateList'][0]['id'];
                        $new_status = $data['updateList'][0]['status'];
                    }
                }elseif (isset($data['addList']) && count($data['addList']) > 0) {
                    if (isset($data['addList'][0]['id'])) {
                        $id = $data['addList'][0]['id'];
                        $new_status = $data['addList'][0]['status'];
                    }
                }
            }
        }
        if (empty($id)) {
            $this->actionCreate();
            Yii::app()->end();
        }
        $model = $this->loadModel($id);
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        if (isset($data)) {
            $data['updateList'][0]['priority'] = ($data['updateList'][0]['priority']=='true'?1:0);
            $model->attributes = $data['updateList'][0];

            if (isset($new_status)) {
                $old_status = $model->status;
                $options = Extcomponents::getStatuses();

                $can_change = $model->canChangeStatus($new_status);

                if($can_change==Extcomponents::C_INVALID){
                    $this->j("Неизвестный номер статуса ".$new_status, false);
                }
                if($can_change==Extcomponents::C_DENY){
                    $this->j("Нельзя менять статус " . $options[$old_status], false);
                }
                if($can_change==Extcomponents::C_NO){
                    $this->j("Неопределено поведение после смены статуса на " . $options[$new_status], false);
                }

                $model->status = $new_status;
            }
            if ($model->save()) {
                print $_POST['list'];
                Yii::app()->end();
            } else {
                $this->j(array('message'=>'Ошибка сохранения','errors'=>$model->errors),false);
            }
        }
        $this->j('Нет данных',false);
    }

    public function actionRequest()
    {
        $ids = $_POST['ids'];
        //Это просто ID строки. Requestid он только чтобы отличать от id компонента
        $requestids = $_POST['requestid'];
        $model = new Extcomponents();
        $criteria = new CDbCriteria;
        //select requestid from extcomponents where requestid is not null order by substr(requestid,10) desc, requestid desc limit 1
        $criteria->select = 'requestid';
        $criteria->condition = 'requestid is not null';
        $criteria->order = 'substr(requestid,10) desc, requestid desc';
        $criteria->limit = 1;
        $row = $model->model()->find($criteria);
        if (count($requestids) == 0) {
            $maxRequestId = $row['requestid'];
            $new_id = 1;
            if (!empty($maxRequestId)) {
                $id_parts = explode('.', $maxRequestId);
                $year = intval($id_parts[2]);
                if ($year == intval(date('y'))) {
                    $new_id = intval($id_parts[0]) + 1;
                }
            }
        } elseif (count($requestids) == 1) {
            //Это просто ID строки. Requestid он только чтобы отличать от id компонента
            $requestModel = Extcomponents::model()->findByPk($requestids[0]);
            //А вот тут он автомагически превращается в правильный....
            $requestId = $requestModel->requestid;
            $id_parts = explode('.', $requestId);
            $new_id = $id_parts[0];
        } else {
            print json_encode(array('success'=>false,'error'=>'Не поддерживается несколько заяввок. Как вам это 
            удалось?!'));;
            Yii::app()->end();
        }
        foreach ($ids as $id) {
            $extcomponent = Extcomponents::model()->findByPk($id);
            $extcomponent->requestid = str_pad($new_id, 6, 0, STR_PAD_LEFT) . '.СБ.' . date('y');
            $extcomponent->save(false);
        }
        print json_encode(array('success'=>true,'requestid'=>$extcomponent->requestid));
        Yii::app()->end();
    }

	public function actionReplace()
	{
		/** @var Extcomponents $model */
		$model = Extcomponents::model()->findByPk(Yii::app()->request->getPost('requestid', -1));
		if (is_null($model)) {
			print json_encode(array('success' => false, 'error' => 'Не найден заменяемый компонент.'));
			Yii::app()->end();
		}
		$newModel = new Extcomponents();
		$newModel->attributes = $model->attributes;
		$newModel->id = null;
		$newModel->partnumber = Yii::app()->request->getPost('partnumber');
		$newModel->partnumberid = Yii::app()->request->getPost('partnumberid');
		$newModel->status = 0;
		$newModel->amount = max(1,$model->amount - $model->delivered);
		$newModel->delivered = 0;
		$model->amount = $model->delivered;
		$model->status = 5;
		if ($newModel->save()) {
		    if(!empty($model->description)){
                $model->description.=";\n";
            }
		    $model->description.='Заменен на компонент '.($newModel->partnumber).'; Новая строка '.($newModel->id);
		    $model->save();
			print json_encode(array('success' => true, 'requestid' => $newModel->requestid, 'id'=>$newModel->id));
		} else {
			print json_encode(array('success' => false, 'error' => 'Не получилось сохранить. ' .
				print_r($newModel->errors, 1).'Attrs:'.print_r($model->attributes,1)));
			Yii::app()->end();
		}
		Yii::app()->end();
	}

    public function actionRemoveComponent()
    {
        $ids = $_POST['id'];
        foreach ($ids as $id) {
            $model = Extcomponents::model()->findByPk($id);
            $pn = $model->partnumber;
            $pnid = $model->partnumberid;
            if(!empty($model->requestid)) {

                $requestid = $model->requestid;
                $model->requestid = null;
                if($model->save()){
                    print json_encode(array('success' => true, 'requestid' => $requestid, 'pn'=>$pn, 'pnid'=>$pnid));
                }else{
                    print json_encode(array('success' => false, 'error' => 'Не получилось сохранить. ' .print_r($model->errors, 1).'Attrs:'.print_r($model->attributes,1)));
                }
            }else{
                try {
                    if($model->delete()){
                        print json_encode(array('success' => true, 'pn'=>$pn, 'pnid'=>$pnid));
                    }else{
                        print json_encode(array('success' => false, 'error' => 'Не получилось удалить. '.__METHOD__.' '. __LINE__));
                    };
                }catch (CDbException $e){
                    print json_encode(array('success' => false, 'error' => 'Не получилось удалить. ' .$e->getMessage().'; '.$e->errorInfo));
                }
            }
        }

        Yii::app()->end();
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id)
    {
        $this->loadModel($id)->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    /**
     * Lists all models.
     */
    public function actionOldIndex()
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('requestid is not null');
        $criteria->with = array(
            'user.userinfo' => array('together' => true,),
            'component' => array('together' => true,),
        );
        $model = Extcomponents::model();
        $model->scenario = 'search';
        $model->attributes = Yii::app()->request->getPost(get_class($model));

        $criteria2 = new CDbCriteria();
        $criteria2->addCondition('requestid is null');
        $criteria2->with = array(
            'user.userinfo' => array('together' => true,),
            'component' => array('together' => true,),
        );
        $criteria2->order = 'priority desc, t.requestid desc';

        $this->render('index', array(
            'dataProviderRequests' => $model->findbyCriteria($criteria),
            'dataProviderAssemblies' => $model->findbyCriteria($criteria2),
        ));
    }

    public function actionIndex()
    {
        $this->render('newIndex');
    }

    public function actionRequestslist()
    {
        $_GET['pq_curpage']=Yii::app()->request->getParam('pq_curpage');
        $model = Extcomponents::model();
        $result = array('data' => array());
        $showall = Yii::app()->request->getPost('showall', false);
        $showall = ($showall == 'true' ? true : false);
        $dp = $model->getRequests(
            array(
                'page_size'=>Yii::app()->request->getParam('pq_rpp',10),
                'filter'=>Yii::app()->request->getParam('pq_filter',array()),
                'show_closed'=>$showall
            )
        );
        $sort_attrs = array();
        foreach (Yii::app()->request->getParam('pq_sort') as $sort_item){
            $sort_attrs[] = $sort_item['dataIndx'] . ($sort_item['dir'] == 'down' ? '.desc' : '');
        }
        $_GET['sort'] = implode('-',$sort_attrs);

        $dp->setSort(array(
          'class' => 'CSort',
          'multiSort' => true,
          'sortVar' => 'sort',
          'attributes' => array('*',
            'user' => array(
              'asc' => 'userinfo.fullname, "user".username',
              'desc' => 'userinfo.fullname desc, "user".username desc'
            ),
            'partnumber' => array(
              'asc' => 't.partnumber',
              'desc' => 't.partnumber desc',
            ),
            'partnumberid' => array(
              'asc' => 't.partnumberid',
              'desc' => 't.partnumberid desc',
            )
          ),
          'defaultOrder' => 'priority DESC, requestid asc'
        ));
        /** @var Extcomponents $request */
        foreach ($dp->getData() as $request) {
            $row = $request->attributes;
            $row['user'] = $request->user->userinfo->fullname;
            if(empty($row['user'])){
                $row['user'] = $request->user->username;
            }
            $result['data'][] = $row;
        }
        $result['totalRecords'] = $dp->totalItemCount;
        $result['curPage']=Yii::app()->request->getParam('pq_curpage');
        if($result['curPage']<=1){
            $result['curPage'] = 1;
        }
        print json_encode($result);
    }

    public function actionComponentslist()
    {
        $_GET['pq_curpage']=Yii::app()->request->getParam('pq_curpage');
        $model = Extcomponents::model();
        $result = array('data' => array());
        /** @var CActiveDataProvider $dp */
        $dp = $model->getNewComponents(
            array(
                'page_size'=>Yii::app()->request->getParam('pq_rpp',10),
                'filter'=>Yii::app()->request->getParam('pq_filter',array())
                )
        );
        $sort_attrs = array();
        foreach (Yii::app()->request->getParam('pq_sort') as $sort_item){
            $sort_attrs[] = $sort_item['dataIndx'] . ($sort_item['dir'] == 'down' ? '.desc' : '');
        }
        $_GET['sort'] = implode('-',$sort_attrs);
        $dp->setSort(array(
          'class' => 'CSort',
          'multiSort' => true,
          'sortVar' => 'sort',
          'attributes' => array(
            'user' => array(
              'asc' => 'userinfo.fullname, "user".username',
              'desc' => 'userinfo.fullname desc, "user".username desc'
            ),
            'priority'=>array(
              'asc'=>'t.priority, t.id',
              'desc'=>'t.priority desc, t.id',
            ),
            'partnumber' => array(
              'asc' => 't.partnumber',
              'desc' => 't.partnumber desc',
            ),
            'partnumberid' => array(
              'asc' => 't.partnumberid',
              'desc' => 't.partnumberid desc',
            ),
            '*',
          ),
          'defaultOrder' => 't.partnumber'
        ));

//        print(json_encode($dp->getSort()));

        /** @var Extcomponents $requestedComponent */
        foreach ($dp->getData() as $requestedComponent) {
            $row = $requestedComponent->attributes;
            $row['user'] = $requestedComponent->user->userinfo->fullname;
            if(empty($row['user'])){
                $row['user'] = $requestedComponent->user->username;
            }
            $result['data'][] = $row;
        }
        $result['totalRecords'] = $dp->totalItemCount;
        $result['curPage']=Yii::app()->request->getParam('pq_curpage');
        if($result['curPage']<=1){
            $result['curPage'] = 1;
        }
        print json_encode($result);
    }

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        $model = new Extcomponents('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Extcomponents'])) {
            $model->attributes = $_GET['Extcomponents'];
        }

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    public function actionGetFile($fileId)
    {

    }

    public function actionExport()
    {
        if (isset($_POST["excel"]) && isset($_POST["extension"])) {
            $url = Yii::app()->basePath . '/extensions/Excel/PHPExcel.php';
            require_once $url;
            $this->lettersArrayPrepare();
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()->setCreator("STMS-components-extension")
                ->setLastModifiedBy("STMS-components-extension")
                ->setTitle("Components Requests Export")
                ->setSubject("Components Requests Export")
                ->setDescription("Components Requests Export")
                ->setKeywords("components requests stms")
                ->setCategory("Export info");
            $activeSheet = $objPHPExcel->setActiveSheetIndex(0);

            $rows = explode("\n", $_POST['excel']);
            $cell = 'A1';
            foreach ($rows as $row) {
                $row_data = str_getcsv($row);
                $i = 0;
                foreach ($row_data as $cell_value) {
                    $cell_value = str_replace('null','',$cell_value);
                    $cell_value = str_replace('undefined','',$cell_value);
                    $activeSheet->setCellValue($cell, $cell_value);
                    $cell = $this->dc($cell, array(1, 0));
                    $i++;
                }
                $cell = $this->dc($cell, array(-$i, 1));
            }


            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

            $filename = date('Y-m-d_H-i-s') . '_' . md5(microtime()) . 'xlsx';

            ob_start();
            $objWriter->save('php://output');
            $_SESSION['excel'] = ob_get_contents();
            ob_clean();
            $_SESSION['filename'] = 'Заявки '.date('Y-m-d_His').'.xlsx';
            echo $filename;


        } else if (isset($_GET["filename"])) {
            $filename = $_SESSION['filename'];
            if (isset($_SESSION['excel'])) {
                $excel = $_SESSION['excel'];
                $_SESSION['excel'] = null;
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Length: ' . strlen($excel));
                header('Connection: close');
                echo $excel;
                Yii::app()->end();
            }
        }
        Yii::app()->end();


    }

    private function dc($c, $delta)
    {
        preg_match_all('/([a-zA-Z]+)(\d+)/', $c, $m);
        $letters = $m[1][0];
        $digits = $m[2][0];
        $digits += $delta[1];

        $letter_postition = $this->lettersArray[0][$letters];
        $letter_postition += $delta[0];
        if (isset($this->lettersArray[1][$letter_postition])) {
            $letters = $this->lettersArray[1][$letter_postition];
        } else {
            throw new Exception('Cannot represent negative letters!!!');
        }

//        print $c.'('.json_encode($delta).')->'.$letters.$digits.'<br/>';
        return $letters . $digits;
    }

    private function decrementLetters($l)
    {

    }

    private function lettersArrayPrepare()
    {
        $lettersArray = array();
        $numbersArray = array();
        $a = 'A';
        for ($i = 0; $i < 10000; $i++) {
            $lettersArray[$a] = $i;
            $numbersArray[$i] = $a;
            $a++;
        }
        $this->lettersArray = array($lettersArray, $numbersArray);
    }

    public function actionReceive()
    {
        /** @var Extcomponents $model */
        $model = $this->loadModel($_POST['requestid']);
        /**
         * @property integer $id
         * @property integer $initiatoruserid
         * @property string $updatedate
         * @property integer $partnumberid
         * @property string $operation
         * @property integer $qty
         * @property integer $userid
         * @property integer $prevqty
         * @property integer $postqty
         * @property string $description
         * @property integer $storeid
         */
        $condition = new CDbCriteria();
        $condition->compare('partnumberid', $model->partnumberid);
        $condition->compare('storeid', $_POST['storeid']);
        $condition->compare('place', $_POST['place']);
        $transaction=Yii::app()->db->beginTransaction();
        if(!empty($model->partnumberid)) {
            $store = Store::model()->find($condition);
            if (is_null($store)) {
                $store = new Store();
                $store->partnumberid = $model->partnumberid;
                $store->storeid = $_POST['storeid'];
                $store->qty = 0;
            }
            $correction_model = new Storecorrection();
            $correction_model->initiatoruserid = Yii::app()->user->id;
            $correction_model->userid = 0;
            $correction_model->updatedate = date('Y.m.d H:i:s.000000');
            $correction_model->partnumberid = $model->partnumberid;
            $correction_model->operation = 'add';
            $correction_model->qty = $_POST['amount'];
            $correction_model->prevqty = $store->qty;
            $store->qty += $_POST['amount'];
            $store->place = $_POST['place'];
            $correction_model->postqty = $store->qty;
            $correction_model->storeid = $store->storeid;

            /*if (empty($_POST['installerid'])) {
                $installer = new Installer();
                $installer->name = $_POST['installername'];
                $installer->phone = 'N/A';
                $installer->created_at = date('Y-m-d');
                if ($installer->save()) {
                    $model->installerid = $installer->id;
                }
            }*/
            $correction_model->description = implode(";\n", array(
                ltrim($model->requestid, '0'),
                $model->installer->name,
                $model->description,
                $model->deficite
            ));
            $correction_model_saved = $correction_model->save();
            $correction_model_errors = $correction_model->errors;
            $store_saved = $store->save();
            $store_errors = $store->errors;
        }else{
            $correction_model = new StorecorrectionExt();
            $correction_model->initiatoruserid = Yii::app()->user->id;
            $correction_model->created_at = date('Y.m.d H:i:s.000000');
            $correction_model->partnumber = $model->partnumber;
            $correction_model->qty = $_POST['amount'];

            $correction_model->description = implode(";\n", array(
              ltrim($model->requestid, '0'),
              $model->installer->name,
              $model->description,
              $model->deficite
            ));
            $correction_model_saved = $correction_model->save();
            $correction_model_errors = $correction_model->errors;
            $store_saved = true;
        }
        if(!empty($model->description)){
            $model->description.="<br />";
        }
        //$model->description.='Принято '.$_POST['amount'].'шт. '.date('d.m.Y');
        $model->installerid = $_POST['installerid'];

        $model->delivered += $_POST['amount'];
        if ($model->delivered >= $model->amount) {
            $model->status = 4;
        }





        if ($store_saved && $correction_model_saved && $model->save()) {
            print json_encode(array('success'=>true));
            $transaction->commit();
        } else {
            print json_encode(array('success'=>false,'error'=>array($model->errors,$store_errors,$correction_model_errors)));
            $transaction->rollback();
        }

        /**
         * Заявка":"000002.СБ.18",
         * "ID":"4",
         * "Партномер":"GTXO-92V/GS+24.00MHz",
         * "ID+компонента":"9213",
         * "Кол-во":"455",
         * "Пользователь":"&nbsp;",
         * "Назначение":"fsdafds",
         * "Добавлено":"2018-11-30+16:07:18",
         * "Сдано":"&nbsp;",
         * "Скомплектовать+до":"2018-11-01+00:00:00",
         * "Монтаж+до":"2018-11-10+00:00:00",
         * "Дефицит":"fdsafsd",
         * "Примечание":"fdsafds",
         * "Монтаж+с":"2018-11-24+00:00:00",
         * "Приоритет":"0",
         * "Статус":3,
         * "pq_rowselect":false
         */

        Yii::app()->end();
    }


    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Extcomponents the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model = Extcomponents::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param Extcomponents $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'extcomponents-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function actionGenerateSome(){
        $n = Yii::app()->request->getParam('n',10000);
        print 'Preparing to generate '.$n;
        $done = 0;
        $start_time = time();
        $stms = 0;
        $stmn = 1;
        $fcms = 0;
        $fcmn = 1;
        $fums = 0;
        $fumn = 1;
        while ((time()-$start_time)<29 && $n>0){
            $model = new Extcomponents();
            if(rand(1,100)>20){
                $saveTime = microtime(true);
                $comp = Component::model()->findByPk(rand(1,25882));
                $fcms+=(microtime(true)-$saveTime);
                $fcmn++;
                if($comp!=null){
                    $model->partnumber = $comp->partnumber;
                    $model->partnumberid = $comp->partnumberid;
                }else{
                    print 'Component not found'.'<br />';
                    continue;
                }
            }else{
                $model->partnumber = md5(microtime());
            }
            $model->amount = rand(1,100);
            $model->purpose = md5(microtime().'X');
            if(rand(1,100)>95){
                $model->priority = rand(0,1);
            }else{
                $model->priority = 0;
            }
            $saveTime = microtime(true);
            $user = User::model()->findByPk(rand(1,143));
            $fums+=(microtime(true)-$saveTime);
            $fumn++;
            if(is_null($user)){
                print 'User not found'.'<br />';
                continue;
            }
            $model->userid = $user->id;
            $saveTime = microtime(true);
            if($model->save()){
                $done++;
            }else{
                print json_encode($model->errors).'<br/>';
            }
            $stms+=(microtime(true)-$saveTime);
            $stmn++;
            $n--;
        }
        print 'Прошло времени '.(time()-$start_time).'<br/>';
        print 'Сгенерировано '.$done.'<br/>';
        print 'Среднее время генерации '.($stms/$stmn).'<br/>';
        print 'Среднее время поиска компонента '.($fcms/$fcmn).'<br/>';
        print 'Среднее время поиска юзера '.($fums/$fumn).'<br/>';
        print '<script type="application/javascript">
location.reload(); 
</script>';
    }

    public function getDirections()
    {
        $sortVar = 'sort';
        $_directions = array();
        if (isset($_GET[$sortVar]) && is_string($_GET[$sortVar])) {
            $attributes = explode('-', $_GET[$sortVar]);
            foreach ($attributes as $attribute) {
                if (($pos = strrpos($attribute, '.')) !== false) {
                    $descending = substr($attribute, $pos + 1) === 'desc';
                    if ($descending)
                        $attribute = substr($attribute, 0, $pos);
                } else
                    $descending = false;

                $_directions[$attribute] = $descending;
            }
        }

        return $_directions;
    }
    function actionStoreCorrectionList(){
        $raw_id = Yii::app()->request->getPost('id',0);
        $id = intval($raw_id);
        if($id==0){
            $this->j('message',true);
        }
        $model = Extcomponents::model()->findByPk($id);
        if(is_null($model)){
            $this->j(sprintf('Строка %s не найдена.',$raw_id),false);
        }
        if(empty($model->partnumberid)){
            //Левый компонент не из STMS
            $storeCorrectionModel = StorecorrectionExt::model()->with('user','user.userinfo')->findAllByAttributes(array(
                'partnumber'=>$model->partnumber
            ));
            $message = 'Левый '.$model->partnumber;
        }else{
            $storeCorrectionModel = Storecorrection::model()->with('user','user.userinfo','component','storemodel')->findAllByAttributes(array(
                'partnumberid'=>$model->partnumberid
            ));
            $message = 'STMS';
        }
        if(is_null($storeCorrectionModel)){
            $this->j(array('data'=>array(),'message'=>$message,'totalRecords'=>0),1);
        }
        $result = array('message'=>$message);
        $result['totalRecords'] = count($storeCorrectionModel);
        /** @var Storecorrection $storecorrection */
        foreach ($storeCorrectionModel as $storecorrection){
            $row = array();
            $row['id'] = $storecorrection->id;
            $row['user'] = $storecorrection->user->userinfo->fullname;
            if(empty($row['user'])){
                $row['user'] = $storecorrection->user->username;
            }
            $row['created_at'] = $storecorrection->updatedate;
            $row['partnumber'] = $storecorrection->partnumber;
            $row['amount'] = $storecorrection->qty;
            $row['description'] = $storecorrection->description;
            $row['operation'] = $storecorrection->operation;
            $row['prevqty'] = $storecorrection->prevqty;
            $row['postqty'] = $storecorrection->postqty;
            $row['store'] = $storecorrection->store;
            $result['data'][] = $row;
        }

        $this->j($result,1);
    }

    function actionStatusesList(){
        $this->j(Extcomponents::getStatuses());
    }

    function actionStatusesMatrix(){
        $this->j(Extcomponents::getStatusesMatrix());
    }

}


