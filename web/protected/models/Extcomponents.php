<?php

/**
 * This is the model class for table "extcomponents".
 *
 * The followings are the available columns in table 'extcomponents':
 * @property integer $id
 * @property integer $partnumberid
 * @property string $partnumber
 * @property integer $amount
 * @property integer $userid
 * @property string $purpose
 * @property string $created_at
 * @property integer $delivered
 * @property string $assembly_to
 * @property string $install_to
 * @property string $deficite
 * @property string $description
 * @property string $install_from
 * @property integer $priority
 * @property integer $requestid
 * @property integer $status
 * @property integer $installerid
 *
 * The followings are the available model relations:
 * @property Component $component
 * @property User $user
 * @property Installer $installer
 */
class Extcomponents extends ARModel
{

    //C_ for CHANGE_
    const C_ALLOW     = 'allow';
    const C_NO        = 'no';
    const C_AUTO      = 'auto';
    const C_DENY      = 'deny';
    const C_SAME      = 'same';
    const C_INVALID   = 'invalid';

    //S_ for STATUS_
    const S_NEW = 0;
    const S_COMPLECTATION = 1;
    const S_COMPLETE = 2;
    const S_INSTALLING = 3;
    const S_CLOSED = 4;
    const S_CANCEL = 5;
    const S_DISASSEMBLY = 6;

    public static function getStatuses()
    {
        $options = array(
            self::S_NEW => 'Неактивен',
            self::S_COMPLECTATION => 'Комплектация',
            self::S_COMPLETE => 'Скомпонован',
            self::S_INSTALLING => 'На монтаже',
            self::S_CLOSED => 'Закрыт',
            self::S_CANCEL => 'Отмена',
            self::S_DISASSEMBLY => 'Разобрать',
        );
        return $options;
    }

    public static function getStatusesColors()
    {
        $options = array(
            self::S_NEW => 'status-new',
            self::S_COMPLECTATION => 'status-complectation',
            self::S_COMPLETE => 'status-complete',
            self::S_INSTALLING => 'status-installing',
            self::S_CLOSED => 'status-closed',
            self::S_CANCEL => 'status-cancel',
            self::S_DISASSEMBLY => 'status-disassembly',
        );
        return $options;
    }

    public static function getStatusName($status){
        $options = self::getStatuses();
        if(isset($options[$status])){
            return $options[$status];
        }
        return '';
    }

    public function tStatus()
    {
        return self::getStatusName($this->status);
    }

    public static function getStatusesMatrix()
    {
        $matrix = array(
          0=>array(                  1=> self::C_ALLOW,2=> self::C_ALLOW ,  3=> self::C_ALLOW,4=> self::C_DENY,5=> self::C_DENY ,  6=> self::C_ALLOW),
          1=>array(0=> self::C_ALLOW,                  2=> self::C_ALLOW ,  3=> self::C_ALLOW,4=> self::C_AUTO,5=> self::C_DENY ,  6=> self::C_ALLOW),
          2=>array(0=> self::C_ALLOW,1=> self::C_ALLOW,                     3=> self::C_ALLOW,4=> self::C_AUTO,5=> self::C_DENY ,  6=> self::C_ALLOW),
          3=>array(0=> self::C_ALLOW,1=> self::C_ALLOW,2=> self::C_ALLOW ,                    4=> self::C_AUTO,5=> self::C_DENY ,  6=> self::C_ALLOW),
          4=>array(0=> self::C_DENY ,1=> self::C_DENY ,2=> self::C_DENY  ,  3=> self::C_DENY ,                 5=> self::C_DENY ,  6=> self::C_DENY ),
          5=>array(0=> self::C_DENY ,1=> self::C_DENY ,2=> self::C_DENY  ,  3=> self::C_DENY ,4=> self::C_DENY,                    6=> self::C_DENY ),
          6=>array(0=> self::C_ALLOW,1=> self::C_ALLOW,2=> self::C_ALLOW ,  3=> self::C_ALLOW,4=> self::C_DENY,5=> self::C_ALLOW,                   ),
        );
        return $matrix;
    }


    /**
     * @param $to
     * @param null $from
     * @return bool
     */
    public function canChangeStatus($to, $from=null)
    {
        if(is_null($from)){
            $from = $this->status;
        }

        if($from==$to){
            return self::C_SAME;
        }

        $matrix = self::getStatusesMatrix();
        if(!isset($matrix[$from])){
            return self::C_INVALID;
        }
        if(!isset($matrix[$from][$to])){
            return self::C_INVALID;
        }
        return $matrix[$from][$to];
    }

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'extcomponents';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('partnumber, amount, userid, purpose', 'required', 'message'=>'Поле «{attribute}» не может быть пустым.'),
			array('amount, userid, delivered, priority', 'numerical', 'integerOnly'=>true, 'message'=>'Поле «{attribute}» должно быть числом.'),
			array('partnumber', 'length', 'max'=>255, 'message'=>'Поле «{attribute}» не должно превышать 255 символов.'),
            //array('requestid','numerical', 'allowEmpty'=>true),
            array('requestid','default','setOnEmpty' => true, 'value' => null),
			array('assembly_to, install_to,install_from','myDateValidators'),
			array('purpose,partnumberid, created_at, assembly_to, install_to, deficite, description, install_from', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, partnumberid, partnumber, amount, purpose, created_at, delivered, assembly_to, install_to, deficite, description, install_from, priority, requestid, installer, status', 'safe', 'on'=>'search'),
		);
	}

    public function myDateValidators($attribute,$params)
    {
//        if ($params['strength'] === self::WEAK)
//            $pattern = '/^(?=.*[a-zA-Z0-9]).{5,}$/';
//        elseif ($params['strength'] === self::STRONG)
//            $pattern = '/^(?=.*\d(?=.*\d))(?=.*[a-zA-Z](?=.*[a-zA-Z])).{5,}$/';

        if(empty($this->$attribute)){
            return;
        }

        if(!$this->isNewRecord){
            if($this->getOldVal($attribute)==$this->$attribute){
                return;
            }
        }

        $date = date_parse_from_format('Y-m-d',$this->$attribute);
        if($date['year']<intval(date('Y'))){
            $this->addError($attribute, 'Дата не может быть раньше текущей');
            return false;
        }
        if($date['year']==intval(date('Y')) && $date['month']<intval(date('m'))){
            $this->addError($attribute, 'Дата не может быть раньше текущей');
            return false;
        }
        if($date['month']==intval(date('m')) && $date['year']==intval(date('Y')) && $date['day']<intval(date('d'))){
            $this->addError($attribute, 'Дата не может быть раньше текущей');
            return false;
        }
    }

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'component' => array(self::BELONGS_TO, 'Component', 'partnumberid'),
			'user' => array(self::BELONGS_TO, 'User', 'userid'),
			'installer' => array(self::BELONGS_TO, 'Installer', 'installerid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'partnumberid' => 'Порядковый номер',
			'partnumber' => 'Наименование',
			'amount' => 'Кол-во',
			'userid' => 'Пользователь',
			'purpose' => 'Назначение',
			'created_at' => 'Добавлено',
			'delivered' => 'Сдано',
			'assembly_to' => 'Скомплектовать до',
			'install_to' => 'Монтаж до',
			'deficite' => 'Дефицит',
			'description' => 'Примечание',
			'install_from' => 'Монтаж с',
			'priority' => 'Приоритет',
			'requestid' => 'Заявка',
			'status' => 'Статус',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		//$criteria->with = ;
        $criteria->with = array(
            'user.userinfo' => array('together' => true, ),
            'component' => array('together' => true, ),
        );

		$criteria->compare('id',$this->id);
		$criteria->compare('partnumberid',$this->partnumberid);
		$criteria->compare('partnumber',$this->partnumber,true);
		$criteria->compare('amount',$this->amount);
		$criteria->compare('userid',$this->userid);
		$criteria->compare('purpose',$this->purpose,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('delivered',$this->delivered);
		$criteria->compare('assembly_to',$this->assembly_to,true);
		$criteria->compare('install_to',$this->install_to,true);
		$criteria->compare('deficite',$this->deficite,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('install_from',$this->install_from,true);
		$criteria->compare('priority',$this->priority);
		$criteria->compare('requestid',$this->requestid);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>100000
            ),
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Extcomponents the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * @param $criteria
     * @return CActiveDataProvider
     */
    public function findbyCriteria($criteria,$pagesize=10000)
    {
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>$pagesize,
                'pageVar'=>'pq_curpage'
            ),
        ));
    }

    public static function removeRequest($id){
        /** @var Extcomponents $model */
        $model = self::model()->findByPk($id);
        $model->requestid = null;
        $model->save();
    }

    public function getNewComponents($params=array())
    {
        $default_params = array(
            'show_closed' => false,
            'page_size' => 10000,
            'page' => 0,
            'filter'=>array()
        );
        $params = array_merge($default_params,$params);
        $show_closed = $params['show_closed'];
        $criteria = new CDbCriteria();
        /*
         *  pq_filter[mode]	OR
            pq_filter[data][0][dataIndx]	partnumber
            pq_filter[data][0][value]	p_
            pq_filter[data][0][condition]	contain
            pq_filter[data][0][dataType]	string
            pq_filter[data][0][cbFn]
         * */

        if(!empty($params['filter'])){
            $filter = $params['filter'];
            $mode = $filter['mode'];
            $tables = array(
                'partnumber'=>'t',
                'user'=>'userinfo',
            );
            $search_columns = array(
                'user'=>'fullname',
            );

            foreach ($filter['data'] as $datum){
                $table = 't';
                $search_column = $datum['dataIndx'];
                if(isset($tables[$datum['dataIndx']])) {
                    $table = $tables[$datum['dataIndx']];
                }
                if(isset($search_columns[$datum['dataIndx']])) {
                    $search_column = $search_columns[$datum['dataIndx']];
                }

                if($datum['dataType']=='date'){
                    $search_column.='::date';
                    if(!empty($datum['value'])) {
                        $datum['value'] = date('Y-m-d', date_create_from_format('m/d/Y', $datum['value'])->getTimestamp());
                    }
                    if(!empty($datum['value2'])) {
                        $datum['value2'] = date('Y-m-d', date_create_from_format('m/d/Y', $datum['value2'])->getTimestamp());
                    }
                }

                if($datum['dataType']=='bool') {
                    $datum['value'] = ($datum['value']=='true'?1:0);
                }

                if($datum['condition']=='contain'){
                    $criteria->addCondition($table.'.'.$search_column.' ilike \'%'.$datum['value'].'%\'',$mode);
                }elseif ($datum['condition']=='between'){
                    $criteria->addBetweenCondition($table.'.'.$search_column,$datum['value'],$datum['value2'],$mode);
                }elseif ($datum['condition']=='equal'){
                    $criteria->compare($table.'.'.$search_column,$datum['value'],false,$mode);
                }elseif ($datum['condition']=='gte'){
                    //NOT correct, but Oleg asked for that
                    $criteria->compare($table.'.'.$search_column,$datum['value'],false,$mode);
                }

            }
        }
        $criteria->addCondition('requestid is null');
        if(!$show_closed) {
            $criteria->addCondition('status != 4 and status != 5');
        }
        $criteria->with = array(
            'user.userinfo' => array('together' => true, ),
            'component' => array('together' => true, ),
        );
        return $this->findbyCriteria($criteria,$params['page_size']);
    }

    public function getRequests($params=false)
    {
        $default_params = array(
            'show_closed' => false,
            'page_size' => 10000,
            'page' => 0,
            'filter'=>array()
        );
        $params = array_merge($default_params,$params);
        $show_closed = $params['show_closed'];
        $criteria = new CDbCriteria();
        if(!empty($params['filter'])){
            $filter = $params['filter'];
            $mode = $filter['mode'];
            $tables = array(
                'partnumber'=>'t',
                'user'=>'userinfo',
            );
            $search_columns = array(
                'user'=>'fullname',
            );

            foreach ($filter['data'] as $datum){
                $table = 't';
                $search_column = $datum['dataIndx'];
                if(isset($tables[$datum['dataIndx']])) {
                    $table = $tables[$datum['dataIndx']];
                }
                if(isset($search_columns[$datum['dataIndx']])) {
                    $search_column = $search_columns[$datum['dataIndx']];
                }

                if($datum['dataType']=='date'){
                    $search_column.='::date';
                    if(!empty($datum['value'])) {
                        $datum['value'] = date('Y-m-d', date_create_from_format('m/d/Y', $datum['value'])->getTimestamp());
                    }
                    if(!empty($datum['value2'])) {
                        $datum['value2'] = date('Y-m-d', date_create_from_format('m/d/Y', $datum['value2'])->getTimestamp());
                    }
                }

                if($datum['dataType']=='bool') {
                    $datum['value'] = ($datum['value']=='true'?1:0);
                }

                if($datum['condition']=='contain'){
                    $criteria->addCondition($table.'.'.$search_column.' ilike \'%'.$datum['value'].'%\'',$mode);
                }elseif ($datum['condition']=='between'){
                    $criteria->addBetweenCondition($table.'.'.$search_column,$datum['value'],$datum['value2'],$mode);
                }elseif ($datum['condition']=='equal'){
                    $criteria->compare($table.'.'.$search_column,$datum['value'],false,$mode);
                }elseif ($datum['condition']=='gte'){
                    //NOT correct, but Oleg asked for that
                    $criteria->compare($table.'.'.$search_column,$datum['value'],false,$mode);
                }

            }
        }
        $criteria->addCondition('requestid is not null');
        if(!$show_closed) {
            $criteria->addCondition('status != 4 and status != 5');
        }
        $criteria->with = array(
            'user.userinfo' => array('together' => true, ),
            'component' => array('together' => true, ),
        );
        return $this->findbyCriteria($criteria,$params['page_size']);
    }

}