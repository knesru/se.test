<?php

class ToAssemblyController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/main';

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
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete','request'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Extcomponents;
		$model->created_at = date('Y-m-d H:i:s');
		$model->userid = Yii::app()->user->id;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Extcomponents']))
		{
			$model->attributes=$_POST['Extcomponents'];
			if(empty($model->created_at)){
			    $model->created_at = null;
            }
            if(empty($model->assembly_to)){
			    $model->assembly_to = null;
            }
            if(empty($model->install_to)){
			    $model->install_to = null;
            }
            if(empty($model->install_from)){
			    $model->install_from = null;
            }
			if($model->save()) {
                $this->redirect(array('view', 'id' => $model->id));
            }
        }

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id=null)
	{
	    if(empty($id)){
	        $this->actionCreate();
	        Yii::app()->end();
        }
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Extcomponents']))
		{
			$model->attributes=$_POST['Extcomponents'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

    public function actionRequest()
    {
        $id = $_POST['id'];
        $model = new Extcomponents();
        $criteria=new CDbCriteria;
        //select requestid from extcomponents where requestid is not null order by substr(requestid,10) desc, requestid desc limit 1
        $criteria->select='requestid';
        $criteria->condition = 'requestid is not null';
        $criteria->order = 'substr(requestid,10) desc, requestid desc';
        $criteria->limit = 1;
        $row = $model->model()->find($criteria);
        $maxRequestId = $row['requestid'];
        $new_id = 1;
        if(!empty($maxRequestId)) {
            $id_parts = explode('.' , $maxRequestId);
            $year = intval($id_parts[2]);
            if($year==intval(date('y'))){
                $new_id = intval($id_parts[0]) + 1;
            }
        }
        $extcomponent = Extcomponents::model()->findByPk($id);
        $extcomponent->requestid = str_pad($new_id,6,0, STR_PAD_LEFT).'.СБ.'.date('y');
        $extcomponent->save(false);
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
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
	    $criteria = new CDbCriteria();
		$model = Extcomponents::model();
		$model->scenario = 'search';
		$model->attributes = Yii::app()->request->getPost(get_class($model));
		$this->render('index',array(
			'dataProvider'=>$model->search(),
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Extcomponents('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Extcomponents']))
			$model->attributes=$_GET['Extcomponents'];

		$this->render('admin',array(
			'model'=>$model,
		));
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
		$model=Extcomponents::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Extcomponents $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='extcomponents-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
