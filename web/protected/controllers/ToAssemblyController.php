<?php

class ToAssemblyController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/main';

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
                'actions' => array('index', 'newIndex', 'view', 'export', 'requestslist', 'componentslist','receive'),
                'users' => array('*'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('create', 'update'),
                'users' => array('@'),
            ),
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('admin', 'delete', 'request', 'removecomponent'),
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
            $data = $_POST['Extcomponents'];
        } elseif (
            isset($_POST['list']) &&
            ($data = json_decode($_POST['list'], true)) &&
            isset($data['addList']) &&
            count($data['addList']) > 0) {
            $data = $data['addList'];
        }

        if (isset($data)) {
            $model->attributes = $_POST['Extcomponents'];
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
            if ($model->save()) {
                if (Yii::app()->request->isAjaxRequest) {
                    print 'OKAY';
                    Yii::app()->end();
                }
                $this->redirect(array('view', 'id' => $model->id));
            } else {
                if (Yii::app()->request->isAjaxRequest) {
                    print json_encode($model->errors);
                    Yii::app()->end();
                }
            }
        }

        $this->render('create', array(
            'model' => $model,
        ));
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
            $data = $data['addList'][0];
        }

        if (isset($_POST['list'])) {
            if ($data = json_decode($_POST['list'], true)) {
                if (isset($data['updateList']) && count($data['updateList']) > 0) {
                    if (isset($data['updateList'][0]['id'])) {
                        $id = $data['updateList'][0]['id'];
                        $new_status = $data['updateList'][0]['status'];
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
            $model->attributes = $data['updateList'][0];

            if(isset($new_status)) {
                $old_status = $model->status;
                $options = array(
                    0 => 'Не активен',
                    1 => 'Комплектация',
                    2 => 'Скомпонован',
                    3 => 'На монтаже',
                    4 => 'Закрыт',
                    5 => 'Отмена'
                );
                if (($old_status == 4 or $old_status == 5) && $new_status!=$old_status) {
                    print('{"status":"error","message":"Нельзя менять статус "' . $options[$old_status] . '}');
                    Yii::app()->end();
                }
                $model->status = $new_status;
                if ($new_status == 4) {
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
                    $condition->compare('partnumberid', $data['updateList'][0]['ID компонента']);
                    $condition->compare('storeid', 1);
                    $store = Store::model()->find($condition);
                    if (is_null($store)) {
                        $store = new Store();
                        $store->partnumberid = $data['updateList'][0]['ID компонента'];
                        $store->storeid = 1;
                        $store->qty = 0;
                    }
                    $correction_model = new Storecorrection();
                    $correction_model->initiatoruserid = Yii::app()->user->id;
                    $correction_model->userid = 0;
                    $correction_model->updatedate = date('Y.m.d H:i:s.000000');
                    $correction_model->partnumberid = $data['updateList'][0]['ID компонента'];
                    $correction_model->operation = 'add';
                    $correction_model->qty = $data['updateList'][0]['Кол-во'];
                    $correction_model->prevqty = $store->qty;
                    $store->qty += $data['updateList'][0]['Кол-во'];
                    $correction_model->postqty = $store->qty;
                    $correction_model->storeid = $store->storeid;
                    $correction_model->description = $data['updateList'][0]['Заявка'] . "; Описание: " . $data['updateList'][0]['Примечание'];

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
                    $status_save_ok = ($store->save() && $correction_model->save());
                }
                $status_save_ok = true;
            }else{
                $status_save_ok = true;
            }
            if ($model->save() && $status_save_ok) {
                print $_POST['list'];
                Yii::app()->end();
            } else {
                print 'WAT?!';
                print_r($model->errors);
            }
            //$this->redirect(array('list','id'=>$model->id));
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    public function actionRequest()
    {
        $ids = $_POST['ids'];
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
            $requestModel = Extcomponents::model()->findByPk($requestids[0]);
            $requestId = $requestModel->requestid;
            $id_parts = explode('.', $requestId);
            $new_id = $id_parts[0];
        } else {
            print 'ERR';
            Yii::app()->end();
        }
        foreach ($ids as $id) {
            $extcomponent = Extcomponents::model()->findByPk($id);
            $extcomponent->requestid = str_pad($new_id, 6, 0, STR_PAD_LEFT) . '.СБ.' . date('y');
            $extcomponent->save(false);
        }
        Yii::app()->end();
    }

    public function actionRemoveComponent()
    {
        $requestids = $_POST['requestid'];
        foreach ($requestids as $requestid) {

//            print_r($model->errors);
//            print_r($model->attributes);
            $model = Extcomponents::model()->findByPk($requestid);
            $model->requestid = null;
            $model->save();
//            print_r($model->attributes);
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
        $criteria2->order = 'priority desc, t.id asc';

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
        $model = Extcomponents::model();
        $result = array('data' => array());
        /** @var Extcomponents $request */
        foreach ($model->getRequests()->getData() as $request) {
            $result['data'][] = $request->attributes;
        }
        print json_encode($result);
    }

    public function actionComponentslist()
    {
        $model = Extcomponents::model();
        $result = array('data' => array());
        /** @var Extcomponents $request */
        foreach ($model->getNewComponents()->getData() as $request) {
            $result['data'][] = $request->attributes;
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

    public function actionExport()
    {
        if (isset($_POST["excel"]) && isset($_POST["extension"])) {
            $extension = $_POST["extension"];
            if ($extension == "csv" || $extension == "xml") {
                session_start();
                $_SESSION['excel'] = $_POST['excel'];
                $filename = "pqGrid." . $extension;
                echo $filename;
            }
        } else if (isset($_GET["filename"])) {
            $filename = $_GET["filename"];
            if ($filename == "pqGrid.csv" || $filename == "pqGrid.xml") {
                $filename = str_replace('.xml', '.xlsx', $filename);
                session_start();
                if (isset($_SESSION['excel'])) {
                    $excel = $_SESSION['excel'];
                    $excel = str_replace('&nbsp;', '', $excel);
                    $excel = iconv('utf-8', 'windows-1251', $excel);
                    $_SESSION['excel'] = null;
                    header('Content-Disposition: attachment; filename="' . $filename . '"');
                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Length: ' . strlen($excel));
                    header('Connection: close');
                    echo $excel;
                    Yii::app()->end();
                }
            }
        }
    }

    public function actionReceive()
    {
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
        $store = Store::model()->find($condition);
        if (is_null($store)) {
            $store = new Store();
            $store->partnumberid = $model->partnumberid;
            $store->storeid = 1;
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
        $correction_model->postqty = $store->qty;
        $correction_model->storeid = $store->storeid;
        $correction_model->description = ltrim($model->requestid, '0') . "; Описание: " . $model->description . "; Дефицит: " . $model->deficite;


        $model->delivered+=$_POST['amount'];
        if($model->delivered>$model->amount){
            $model->status = 4;
        }

        if($store->save() && $correction_model->save() && $model->save()){
            print 'OK';
        }else{
            print 'ERR';
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

}



