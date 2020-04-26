<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
    const USER_ADMIN = 'admin';
    const USER_ALL = '*';
    const USER_LOGGED = '@';

	/**
	 * @var string the default layout for the controller view. Defaults to 'column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='main';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();

    /**
     * @param $data
     * @param bool $success
     */
    public static function j($data, $success=true){
	    self::jsonAnswer($data,$success);
    }

	public static function jsonAnswer($data,$success=true){
	    if(is_string($data)){
	        $data = array('message'=>$data);
        }
        $answer = $data;
	    $answer['success']=$success;
	    print json_encode($answer);
	    Yii::app()->end();
    }
}