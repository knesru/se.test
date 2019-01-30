<?php

/**
 * This is the model class for table "{{storecorrection}}".
 *
 * The followings are the available columns in table '{{storecorrection}}':
 * @property integer $id
 * @property integer $initiatoruserid
 * @property string $updatedate
 * @property integer $partnumberid
 * @property string $operation
 * @property integer $qty
 * @property integer $userid
 * @property integer $prevqty
 * @property integer $postqty
 * @property string $description
 * @property integer $storeid
 */
class Storecorrection extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{storecorrection}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('initiatoruserid, partnumberid, qty', 'required'),
			array('initiatoruserid, partnumberid, qty, userid, prevqty, postqty, storeid', 'numerical', 'integerOnly'=>true),
			array('updatedate, operation, description', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, initiatoruserid, updatedate, partnumberid, operation, qty, userid, prevqty, postqty, description, storeid', 'safe', 'on'=>'search'),
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
			'initiatoruserid' => 'Initiatoruserid',
			'updatedate' => 'Updatedate',
			'partnumberid' => 'Partnumberid',
			'operation' => 'Operation',
			'qty' => 'Qty',
			'userid' => 'Userid',
			'prevqty' => 'Prevqty',
			'postqty' => 'Postqty',
			'description' => 'Description',
			'storeid' => 'Storeid',
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
		$criteria->compare('updatedate',$this->updatedate,true);
		$criteria->compare('partnumberid',$this->partnumberid);
		$criteria->compare('operation',$this->operation,true);
		$criteria->compare('qty',$this->qty);
		$criteria->compare('userid',$this->userid);
		$criteria->compare('prevqty',$this->prevqty);
		$criteria->compare('postqty',$this->postqty);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('storeid',$this->storeid);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Storecorrection the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
