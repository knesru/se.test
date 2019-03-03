<?php

/**
 * This is the model class for table "{{storelist}}".
 *
 * The followings are the available columns in table '{{storelist}}':
 * @property integer $storeid
 * @property string $name
 * @property string $email
 * @property string $address
 * @property string $phones
 * @property string $faxes
 * @property string $info
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property Requestlist[] $requestlists
 * @property Returnlist[] $returnlists
 * @property Storetransfers[] $storetransfers
 * @property Storetransfers[] $storetransfers1
 * @property Relocation[] $relocations
 * @property Relocation[] $relocations1
 * @property Store[] $stores
 * @property Manufacturingprocess[] $manufacturingprocesses
 * @property Assemblytemplates[] $assemblytemplates
 */
class Storelist extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{storelist}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('status', 'numerical', 'integerOnly'=>true),
			array('name, email, phones, faxes', 'length', 'max'=>255),
			array('address', 'length', 'max'=>512),
			array('info', 'length', 'max'=>4096),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('storeid, name, email, address, phones, faxes, info, status', 'safe', 'on'=>'search'),
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
			'requestlists' => array(self::HAS_MANY, 'Requestlist', 'storeid'),
			'returnlists' => array(self::HAS_MANY, 'Returnlist', 'deststoreid'),
			'storetransfers' => array(self::HAS_MANY, 'Storetransfers', 'deststoreid'),
			'storetransfers1' => array(self::HAS_MANY, 'Storetransfers', 'srcstoreid'),
			'relocations' => array(self::HAS_MANY, 'Relocation', 'deststoreid'),
			'relocations1' => array(self::HAS_MANY, 'Relocation', 'srcstoreid'),
			'stores' => array(self::HAS_MANY, 'Store', 'storeid'),
			'manufacturingprocesses' => array(self::HAS_MANY, 'Manufacturingprocess', 'defstoreid'),
			'assemblytemplates' => array(self::HAS_MANY, 'Assemblytemplates', 'storeid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'storeid' => 'Storeid',
			'name' => 'Name',
			'email' => 'Email',
			'address' => 'Address',
			'phones' => 'Phones',
			'faxes' => 'Faxes',
			'info' => 'Info',
			'status' => 'Status',
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

		$criteria->compare('storeid',$this->storeid);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('phones',$this->phones,true);
		$criteria->compare('faxes',$this->faxes,true);
		$criteria->compare('info',$this->info,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Storelist the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
