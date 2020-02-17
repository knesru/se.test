<?php

/**
 * This is the model class for table "{{tasks}}".
 *
 * The followings are the available columns in table '{{tasks}}':
 * @property integer $id
 * @property string $name
 * @property string $customer
 * @property integer $userid
 * @property string $user_name
 * @property integer $managerid
 * @property string $manager_name
 * @property string $contract
 * @property string $created_at
 * @property string $delivery_date
 * @property string $store_delivery_date
 * @property string $inspection_type
 * @property integer $warranty
 * @property string $notes
 * @property string $store_acceptance_date
 * @property string $official_delivery_date
 * @property integer $statusid
 * @property string $updated_at
 *
 * The followings are the available model relations:
 * @property User $user
 * @property Products[] $products
 */
class Tasks extends CActiveRecord
{
    const PREFIX = 'СР';



    //C_ for CHANGE_
    const C_ALLOW     = 'allow';
    const C_NO        = 'no';
    const C_AUTO      = 'auto';
    const C_DENY      = 'deny';
    const C_SAME      = 'same';
    const C_INVALID   = 'invalid';

    //S_ for STATUS_
    const S_NEW = 0;
    const S_SUSPENDED = 1;
    const S_CANCEL = 2;
    const S_RESTORE = 3;
    const S_CLOSED = 4;

    public static function getStatuses()
    {
        $options = array(
            self::S_NEW => 'Открыт',
            self::S_SUSPENDED => 'Приостановлен',
            self::S_CANCEL => 'Отменаен',
            self::S_RESTORE => 'Восстановить',
            self::S_CLOSED => 'Закрыт',
        );
        return $options;
    }

    public static function getStatusesColors()
    {
        $options = array(
            self::S_NEW => 'status-new',
            self::S_SUSPENDED => 'status-complectation',
            self::S_RESTORE => 'status-complete',
            self::S_CLOSED => 'status-closed',
            self::S_CANCEL => 'status-cancel',
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
            self::S_NEW      =>array(                           self::S_SUSPENDED=>self::C_ALLOW,self::S_CANCEL=>self::C_ALLOW,self::S_RESTORE=>self::C_ALLOW,self::S_CLOSED=>self::C_AUTO),
            self::S_SUSPENDED=>array(self::S_NEW=>self::C_ALLOW,                                 self::S_CANCEL=>self::C_ALLOW,self::S_RESTORE=>self::C_ALLOW,self::S_CLOSED=>self::C_AUTO),
            self::S_CANCEL   =>array(self::S_NEW=>self::C_ALLOW,self::S_SUSPENDED=>self::C_ALLOW,                              self::S_RESTORE=>self::C_ALLOW,self::S_CLOSED=>self::C_AUTO),
            self::S_RESTORE  =>array(self::S_NEW=>self::C_ALLOW,self::S_SUSPENDED=>self::C_ALLOW,self::S_CANCEL=>self::C_ALLOW,                               self::S_CLOSED=>self::C_AUTO),
            self::S_CLOSED   =>array(self::S_NEW=>self::C_ALLOW,self::S_SUSPENDED=>self::C_ALLOW,self::S_CANCEL=>self::C_ALLOW,self::S_RESTORE=>self::C_ALLOW                             ),

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
        return '{{tasks}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name', 'required'),
            array('userid, managerid, warranty, statusid', 'numerical', 'integerOnly' => true),
            array('name, customer, user_name, manager_name, contract, created_at, delivery_date, store_delivery_date, inspection_type, notes, store_acceptance_date, official_delivery_date, updated_at', 'length', 'max' => 255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, customer, userid, user_name, managerid, manager_name, contract, created_at, delivery_date, store_delivery_date, inspection_type, warranty, notes, store_acceptance_date, official_delivery_date, statusid, updated_at', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'userid'),
            'products' => array(self::HAS_MANY, 'Products', 'taskid'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Номер',
            'customer' => 'Заказчик',
            'userid' => 'ID руководителя',
            'user_name' => 'Руководитель',
            'managerid' => 'ID ведущего',
            'manager_name' => 'Ведущий',
            'contract' => 'Контракт, договор, счет',
            'created_at' => 'Дата внесения заказа',
            'delivery_date' => 'Срок поставки',
            'store_delivery_date' => 'Срок сдачи на склад',
            'inspection_type' => 'Тип приемки',
            'warranty' => 'Гарантия',
            'notes' => 'Примечания',
            'store_acceptance_date' => 'Дата поступления на склад',
            'official_delivery_date' => 'Дата отгрузки по документам',
            'statusid' => 'ID Статуса',
            'updated_at' => 'Дата последнего обновления',
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

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('customer', $this->customer, true);
        $criteria->compare('userid', $this->userid);
        $criteria->compare('user_name', $this->user_name, true);
        $criteria->compare('managerid', $this->managerid);
        $criteria->compare('manager_name', $this->manager_name, true);
        $criteria->compare('contract', $this->contract, true);
        $criteria->compare('created_at', $this->created_at, true);
        $criteria->compare('delivery_date', $this->delivery_date, true);
        $criteria->compare('store_delivery_date', $this->store_delivery_date, true);
        $criteria->compare('inspection_type', $this->inspection_type, true);
        $criteria->compare('warranty', $this->warranty);
        $criteria->compare('notes', $this->notes, true);
        $criteria->compare('store_acceptance_date', $this->store_acceptance_date, true);
        $criteria->compare('official_delivery_date', $this->official_delivery_date, true);
        $criteria->compare('statusid', $this->statusid);
        $criteria->compare('updated_at', $this->updated_at, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Tasks the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    public static function getNextPrefix()
    {
        $criteria = new CDbCriteria;

        $criteria->select = 'name';
        $criteria->condition = 'name is not null';
        $criteria->order = 'substr(name,10) desc, name desc';
        $criteria->limit = 1;
        $row = self::model()->find($criteria);
        $maxName = $row['name'];
        $new_id = 1;
        if (!empty($maxName)) {
            $name_parts = explode('.', $maxName);
            $year = intval($name_parts[2]);
            if ($year == intval(date('y'))) {
                $new_id = intval($name_parts[0]) + 1;
            }
        }
        return str_pad($new_id, 6, 0, STR_PAD_LEFT) . '.' . self::PREFIX . '.' . date('y');
    }

    public function getTasks($params=false)
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
//                'partnumber'=>'t',
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
//        $criteria->addCondition('requestid is not null');
        if(!$show_closed) {
            $criteria->addCondition('statusid != '.Tasks::S_CLOSED.' and statusid != '.Tasks::S_CANCEL);
        }
        $criteria->with = array(
            'user.userinfo' => array('together' => true, ),
//            'component' => array('together' => true, ),
        );
        return $this->findbyCriteria($criteria,$params['page_size']);
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
}
