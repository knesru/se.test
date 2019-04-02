<?php

/**
 * This is the model class for table "{{actionhistory}}".
 *
 * The followings are the available columns in table '{{actionhistory}}':
 * @property integer $id
 * @property integer $initiatoruserid
 * @property string $created_at
 * @property string $partnumber
 * @property string $ext_id
 * @property string $requestid
 * @property string $action
 * @property string $description
 * @property string $severity
 */
class Actionhistory extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{actionhistory}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('initiatoruserid, created_at, description, severity', 'required'),
			array('initiatoruserid', 'numerical', 'integerOnly'=>true),
			array('partnumber, ext_id, requestid, action, severity', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, initiatoruserid, created_at, partnumber, ext_id, requestid, action, description, severity', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'initiatoruserid' => 'Пользователь',
			'created_at' => 'Добавлено',
			'partnumber' => 'Партномер',
			'ext_id' => 'Строка',
			'requestid' => 'Заявка',
			'action' => 'Действие',
			'description' => 'Описание',
			'severity' => 'Строгость',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('initiatoruserid',$this->initiatoruserid);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('partnumber',$this->partnumber,true);
		$criteria->compare('ext_id',$this->ext_id,true);
		$criteria->compare('requestid',$this->requestid,true);
		$criteria->compare('action',$this->action,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('severity',$this->severity,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Actionhistory the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
