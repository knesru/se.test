<?php

class ActionhistoryController extends Controller
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
				'actions'=>array('index','view','create','update'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','list','log'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
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
		$model=new Actionhistory;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		if(isset($_POST['Actionhistory']))
		{
			$model->attributes=$_POST['Actionhistory'];
			if(Yii::app()->user->id) {
                $model->initiatoruserid = Yii::app()->user->id;
            }else{
			    $model->initiatoruserid = 0;//GUEST
            }
            $model->created_at = date('Y-m-d H:i:s');
			if($model->save()){
			    $this->j('');
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
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Actionhistory']))
		{
			$model->attributes=$_POST['Actionhistory'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
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
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Actionhistory');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

    /**
     * Lists all models.
     */
    public function actionList($allusers = false)
    {
        $criteria = new CDbCriteria();
        $criteria->order = 't.created_at asc';
        $result = array('data' => array());
        $criteria->limit = 100;
        if (!$allusers && !empty(Yii::app()->user->id)) {
            $criteria->limit = 1000;
            $criteria->compare('initiatoruserid', Yii::app()->user->id);
        }else{
            $criteria->order = 't.created_at desc';
        }
        $criteria->with = array(
            'user.userinfo' => array('together' => true,)
        );
        $model = Actionhistory::model()->findAll($criteria);
        foreach ($model as $item) {
            $username = $item->user->userinfo->fullname;
            if(empty($username)){
                $username = $item->user->username;
            }
            if(empty($username)){
                $username = 'Гость';
            }
            $result['data'][] = array(
                'user' => $username,
                'userid' => $item->initiatoruserid,
                'description' => $item->description,
                'severity' => $item->severity,
                'created_at' => $item->created_at
            );
        }
        if(Yii::app()->request->isAjaxRequest) {
            $this->j($result);
        }
        return $result;
    }

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Actionhistory('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Actionhistory']))
			$model->attributes=$_GET['Actionhistory'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionLog()
    {
        $this->render('log',array('logData'=>$this->actionList(true)));
    }

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Actionhistory the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Actionhistory::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Actionhistory $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='actionhistory-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
