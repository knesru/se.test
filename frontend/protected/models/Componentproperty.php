<?php

/**
 * This is the model class for table "{{componentproperty}}".
 *
 * The followings are the available columns in table '{{componentproperty}}':
 * @property integer $partnumberid
 * @property string $p_1
 * @property string $p_2
 * @property string $p_3
 * @property string $p_4
 * @property string $p_5
 * @property string $p_6
 * @property string $p_7
 * @property string $p_8
 * @property string $p_9
 * @property string $p_10
 * @property string $p_11
 * @property string $p_12
 * @property string $p_13
 * @property string $p_14
 * @property string $p_15
 * @property string $p_16
 * @property string $p_17
 * @property string $p_18
 * @property string $p_19
 * @property string $p_20
 * @property integer $availabilid
 * @property integer $unitsid
 *
 * The followings are the available model relations:
 * @property Tavailabilitylist $availabil
 * @property Component $partnumber
 * @property Unitslist $units
 */
class Componentproperty extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{componentproperty}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('partnumberid', 'required'),
			array('partnumberid, availabilid, unitsid', 'numerical', 'integerOnly'=>true),
			array('p_1, p_2, p_3, p_4, p_5, p_7, p_8, p_9, p_11, p_12, p_13, p_14, p_15, p_16, p_17, p_18, p_19, p_20', 'length', 'max'=>255),
			array('p_6', 'length', 'max'=>4096),
			array('p_10', 'length', 'max'=>1024),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('partnumberid, p_1, p_2, p_3, p_4, p_5, p_6, p_7, p_8, p_9, p_10, p_11, p_12, p_13, p_14, p_15, p_16, p_17, p_18, p_19, p_20, availabilid, unitsid', 'safe', 'on'=>'search'),
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
			'availabil' => array(self::BELONGS_TO, 'Tavailabilitylist', 'availabilid'),
			'partnumber' => array(self::BELONGS_TO, 'Component', 'partnumberid'),
			'units' => array(self::BELONGS_TO, 'Unitslist', 'unitsid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'partnumberid' => 'Partnumberid',
			'p_1' => 'Part Label',
			'p_2' => 'Value',
			'p_3' => 'Tolerance',
			'p_4' => 'Description',
			'p_5' => 'Manufacturer',
			'p_6' => 'Attachment',
			'p_7' => 'Package',
			'p_8' => 'User',
			'p_9' => 'Marking',
			'p_10' => 'Comment',
			'p_11' => 'Case',
			'p_12' => 'Process Type',
			'p_13' => 'Design Doc Name',
			'p_14' => 'Pcb Name',
			'p_15' => 'Engineer Name',
			'p_16' => 'Date',
			'p_17' => 'Eqiuv Part Number',
			'p_18' => 'P 18',
			'p_19' => 'P 19',
			'p_20' => 'P 20',
			'availabilid' => 'Availabilid',
			'unitsid' => 'Unitsid',
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

		$criteria->compare('partnumberid',$this->partnumberid);
		$criteria->compare('p_1',$this->p_1,true);
		$criteria->compare('p_2',$this->p_2,true);
		$criteria->compare('p_3',$this->p_3,true);
		$criteria->compare('p_4',$this->p_4,true);
		$criteria->compare('p_5',$this->p_5,true);
		$criteria->compare('p_6',$this->p_6,true);
		$criteria->compare('p_7',$this->p_7,true);
		$criteria->compare('p_8',$this->p_8,true);
		$criteria->compare('p_9',$this->p_9,true);
		$criteria->compare('p_10',$this->p_10,true);
		$criteria->compare('p_11',$this->p_11,true);
		$criteria->compare('p_12',$this->p_12,true);
		$criteria->compare('p_13',$this->p_13,true);
		$criteria->compare('p_14',$this->p_14,true);
		$criteria->compare('p_15',$this->p_15,true);
		$criteria->compare('p_16',$this->p_16,true);
		$criteria->compare('p_17',$this->p_17,true);
		$criteria->compare('p_18',$this->p_18,true);
		$criteria->compare('p_19',$this->p_19,true);
		$criteria->compare('p_20',$this->p_20,true);
		$criteria->compare('availabilid',$this->availabilid);
		$criteria->compare('unitsid',$this->unitsid);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Componentproperty the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
