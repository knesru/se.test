<?php

/**
 * This is the model class for table "{{storecorrection_ext}}".
 *
 * The followings are the available columns in table '{{storecorrection_ext}}':
 * @property integer $id
 * @property integer $initiatoruserid
 * @property string $created_at
 * @property integer $partnumber
 * @property integer $qty
 * @property string $description
 *
 * @property string $updatedate
 * @property string $operation
 * @property string $prevqty
 * @property string $postqty
 * @property string $store
 *
 * @property User $user
 * @property Component $component
 */
class StorecorrectionExt extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{storecorrection_ext}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('initiatoruserid, created_at, partnumber, qty, description', 'required'),
			array('initiatoruserid, qty', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, initiatoruserid, created_at, partnumber, qty, description', 'safe', 'on'=>'search'),
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
            'user' => array(self::BELONGS_TO, 'User', 'initiatoruserid'),
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
			'qty' => 'Количество',
			'description' => 'Описание',
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
		$criteria->compare('partnumber',$this->partnumber);
		$criteria->compare('qty',$this->qty);
		$criteria->compare('description',$this->description,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return StorecorrectionExt the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function getUpdatedate()
    {
        return $this->created_at;
	}

    public function getOperation()
    {
        return 'add';
	}
	public function getPostqty()
    {
        return $this->qty;
	}
	public function getPrevqty()
    {
        return 0;
	}
    public function getStore()
    {
        return '';
    }
}
