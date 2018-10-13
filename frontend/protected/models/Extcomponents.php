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
 *
 * The followings are the available model relations:
 * @property Component $component
 * @property User $user
 */
class Extcomponents extends CActiveRecord
{
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
			array('partnumber, amount, userid', 'required'),
			array('amount, userid, delivered, priority, requestid', 'numerical', 'integerOnly'=>true),
			array('partnumber', 'length', 'max'=>255),
			array('purpose, created_at, assembly_to, install_to, deficite, description, install_from', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, partnumberid, partnumber, amount, userid, purpose, created_at, delivered, assembly_to, install_to, deficite, description, install_from, priority, requestid', 'safe', 'on'=>'search'),
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
			'component' => array(self::BELONGS_TO, 'Component', 'partnumberid'),
			'user' => array(self::BELONGS_TO, 'User', 'userid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'partnumberid' => 'Partnumberid',
			'partnumber' => 'Partnumber',
			'amount' => 'Amount',
			'userid' => 'Userid',
			'purpose' => 'Purpose',
			'created_at' => 'Created At',
			'delivered' => 'Delivered',
			'assembly_to' => 'Assembly To',
			'install_to' => 'Install To',
			'deficite' => 'Deficite',
			'description' => 'Description',
			'install_from' => 'Install From',
			'priority' => 'Priority',
			'requestid' => 'Requestid',
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
}
