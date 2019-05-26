<?php

class SiteController extends Controller
{
    public $layout = 'main';

    /**
     * Declares class-based actions.
     */
    public function actions()
    {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    /**
     * Displays the contact page
     */
    public function actionContact()
    {
        $model = new ContactForm;
        if (isset($_POST['ContactForm'])) {
            $model->attributes = $_POST['ContactForm'];
            if ($model->validate()) {
                $headers = "From: {$model->email}\r\nReply-To: {$model->email}";
                mail(Yii::app()->params['adminEmail'], $model->subject, $model->body, $headers);
                Yii::app()->user->setFlash('contact', 'Thank you for contacting us. We will respond to you as soon as possible.');
                $this->refresh();
            }
        }
        $this->render('contact', array('model' => $model));
    }

    /**
     * Displays the login page
     */
    public function actionLogin()
    {
        if (!defined('CRYPT_BLOWFISH') || !CRYPT_BLOWFISH)
            throw new CHttpException(500, "This application requires that PHP was compiled with Blowfish support for crypt().");

        $model = new LoginForm;

        // if it is ajax validation request
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        // collect user input data
        if (isset($_POST['LoginForm'])) {
            Yii::log('!!!!!!!!!!!!!!!!!!'.print_r($_POST['LoginForm'],1),'error');
            $model->attributes = Yii::app()->request->getPost('LoginForm');
            // validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login()) {
                if(Yii::app()->request->isAjaxRequest){
                    $this->j(array('name'=>Yii::app()->user->name,'url'=>Yii::app()->user->returnUrl));
                    Yii::app()->end();
                }else {
                    $this->redirect(Yii::app()->user->returnUrl);
                }
            }
        }
        // display the login form
        $this->render('login', array('model' => $model));
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $model = Component::model();
        $this->render('index', array('model' => $model));
        /*$criteria=new CDbCriteria(array(
            'condition'=>'status='.Post::STATUS_PUBLISHED,
            'order'=>'update_time DESC',
            'with'=>'commentCount',
        ));
        if(isset($_GET['tag']))
            $criteria->addSearchCondition('tags',$_GET['tag']);

        $dataProvider=new CActiveDataProvider('Post', array(
            'pagination'=>array(
                'pageSize'=>Yii::app()->params['postsPerPage'],
            ),
            'criteria'=>$criteria,
        ));

        $this->render('index',array(
            'dataProvider'=>$dataProvider,
        ));*/
    }
}
