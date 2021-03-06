<?php

class ComponentController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column2';

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
                'actions' => array('index', 'view', 'ajaxList', 'ajaxComponent'),
                'users' => array('*'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('create', 'update', 'storesList', 'getplace','installersList'),
                'users' => array('@'),
            ),
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('admin', 'delete'),
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
        $model = new Component;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Component'])) {
            $model->attributes = $_POST['Component'];
            if ($model->save())
                $this->redirect(array('view', 'id' => $model->partnumberid));
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
    public function actionUpdate($id)
    {
        $model = $this->loadModel($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Component'])) {
            $model->attributes = $_POST['Component'];
            if ($model->save())
                $this->redirect(array('view', 'id' => $model->partnumberid));
        }

        $this->render('update', array(
            'model' => $model,
        ));
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
    public function actionIndex()
    {
        $dataProvider = new CActiveDataProvider('Component');
        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    public function actionAjaxList()
    {
        $criteria = new CDbCriteria();
//        $criteria->compare('partnumber',Yii::app()->request->getParam('term'),true);
        $criteria->addCondition('partnumber ilike :term');
        $criteria->params = array(':term' => Yii::app()->request->getParam('term') . "%");
        $criteria->limit = 50;

        $listData = array();
        $models = Component::model()->findAll($criteria);
        foreach ($models as $model) {
            $listData[] = array('label' => $model->partnumber, 'value' => $model->partnumber);
        }

        echo json_encode($listData);
        Yii::app()->end();
    }

    public function actionStoresList()
    {
        $criteria = new CDbCriteria();
//        $criteria->compare('partnumber',Yii::app()->request->getParam('term'),true);
        $criteria->addCondition('name ilike :term');
        $criteria->params = array(':term' => Yii::app()->request->getParam('term') . "%");
        $criteria->limit = 50;

        $listData = array();
        $models = Storelist::model()->findAll($criteria);
        foreach ($models as $model) {
            $listData[] = array('label' => $model->name, 'value' => $model->storeid);
        }

        echo json_encode($listData);
        Yii::app()->end();
    }

    public function actionInstallersList()
    {
        $criteria = new CDbCriteria();
//        $criteria->compare('partnumber',Yii::app()->request->getParam('term'),true);
        $criteria->addCondition('name ilike :term or phone ilike :term');
        $criteria->params = array(':term' => Yii::app()->request->getParam('term') . "%");
        $criteria->limit = 50;

        $listData = array();
        $models = Installer::model()->findAll($criteria);
        foreach ($models as $model) {
            $listData[] = array('label' => $model->name, 'value' => $model->id);
        }

        echo json_encode($listData);
        Yii::app()->end();
    }

    public function actionGetPlace()
    {
        /*select storeid, place from tstore where partnumberid = 6305 and storeid = 1;*/

        /** @var CDbCriteria $criteria */
        $criteria = new CDbCriteria();
        $criteria->addCondition('place ilike :term');
        $criteria->params = array(
            ':term' => Yii::app()->request->getParam('term') . "%",
        );
        $partnumberid = intval(Yii::app()->request->getParam('partnumberid'));
        if (!empty($partnumberid)) {
            $criteria->addCondition('partnumberid=:partnumberid');
            $criteria->params[':partnumberid'] = $partnumberid;
        }


        $storeid = intval(Yii::app()->request->getParam('storeid'));
        if (!empty($storeid)) {
            $criteria->addCondition('storeid = :storeid');
            $criteria->params[':storeid'] = $storeid;
        }


        $models = Store::model()->findAll($criteria);
        $listData = array();
        foreach ($models as $model) {
            $listData[] = array('label' => empty($model->place)?'<пусто>':$model->place, 'value' => $model->place);
        }

        echo json_encode($listData);
        Yii::app()->end();
    }

    public function actionAjaxComponent()
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'upper(partnumber) = upper(\''.Yii::app()->request->getParam('partnumber').'\')';
        $model = Component::model()->findAll($criteria);
        if(is_null($model)){
            print '';
            Yii::app()->end();
        }
        echo json_encode($model[0]->partnumberid);
        Yii::app()->end();
    }

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        $model = new Component('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Component']))
            $model->attributes = $_GET['Component'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Component the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model = Component::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param Component $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'component-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}
