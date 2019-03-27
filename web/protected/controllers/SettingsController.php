<?php

class SettingsController extends Controller
{
    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('save', 'load', 'reset'),
                'users' => array('@'),
            ),
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('save', 'load','reset'),
                'users' => array('admin'),
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

	public function actionIndex()
	{
		$this->render('index');
	}

	public function actionLoad()
	{
		$name = Yii::app()->request->getPost('name',array());
		$model = Settings::model()->getUserSettings('extcomponents_'.$name);
		$out = base64_decode($model->value);
//		print $model->value;
		if($out===false){
            print json_encode(array('success'=>false));
            return;
        }
        print json_encode(array('success'=>true,'data'=>json_decode($out,true)));
        return;
	}

	public function actionReset()
	{
        $name = Yii::app()->request->getPost('name',array());
        $model = Settings::model()->getUserSettings('extcomponents_'.$name);
        if($model->delete()){
            print json_encode(array('success'=>true));
            return;
        }
        print json_encode(array('success'=>false));
        return;
	}

	public function actionSave()
	{
        $name = Yii::app()->request->getPost('name',array());
        $data = Yii::app()->request->getPost('data',array());
        $model = Settings::model()->getUserSettings('extcomponents_'.$name);
        $model->value = base64_encode(json_encode($data));
        if($model->save()){
            print json_encode(array('success'=>true));
            return;
        }
        print json_encode(array('success'=>false));
        return;
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