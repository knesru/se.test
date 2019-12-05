<?php

/**
 * This is the model class for table "{{component}}".
 *
 * The followings are the available columns in table '{{component}}':
 * @property integer $partnumberid
 * @property string $partnumber
 * @property integer $type
 * @property integer $pathid
 * @property string $updated
 * @property integer $is_assembly
 *
 * The followings are the available model relations:
 * @property Path $path
 * @property Typelist $type0
 * @property Localreplacements[] $localreplacements
 * @property Componentproperty $componentproperty
 * @property Preprocessing[] $preprocessings
 * @property Proccomplist[] $proccomplists
 * @property Compreviewed[] $comprevieweds
 * @property Distributorpartnolist[] $distributorpartnolists
 * @property Issuedcomplist[] $issuedcomplists
 * @property Requiretransfer[] $requiretransfers
 * @property Reqcomplist[] $reqcomplists
 * @property Requireprocessing[] $requireprocessings
 * @property Receivedcomplist[] $receivedcomplists
 * @property Replacement[] $replacements
 * @property Replacement[] $replacements1
 * @property Store[] $stores
 * @property Returncontent[] $returncontents
 * @property Tobuyqty[] $tobuyqties
 * @property Assemblytemplates[] $assemblytemplates
 * @property Assembly[] $assemblies
 * @property Assembly[] $assemblies1
 * @property Templateconfig[] $templateconfigs
 * @property Transfercontent[] $transfercontents
 */
class Component extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{component}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type, pathid, is_assembly', 'numerical', 'integerOnly'=>true),
			array('partnumber', 'length', 'max'=>64),
			array('updated', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('partnumberid, partnumber, type, pathid, updated, is_assembly', 'safe', 'on'=>'search'),
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
			'path' => array(self::BELONGS_TO, 'Path', 'pathid'),
			'type0' => array(self::BELONGS_TO, 'Typelist', 'type'),
			'localreplacements' => array(self::HAS_MANY, 'Localreplacements', 'replid'),
			'componentproperty' => array(self::HAS_ONE, 'Componentproperty', 'partnumberid'),
			'preprocessings' => array(self::HAS_MANY, 'Preprocessing', 'partnumberid'),
			'proccomplists' => array(self::HAS_MANY, 'Proccomplist', 'partnumberid'),
			'comprevieweds' => array(self::HAS_MANY, 'Compreviewed', 'partnumberid'),
			'distributorpartnolists' => array(self::HAS_MANY, 'Distributorpartnolist', 'partnumberid'),
			'issuedcomplists' => array(self::HAS_MANY, 'Issuedcomplist', 'partnumberid'),
			'requiretransfers' => array(self::HAS_MANY, 'Requiretransfer', 'partnumberid'),
			'reqcomplists' => array(self::HAS_MANY, 'Reqcomplist', 'partnumberid'),
			'requireprocessings' => array(self::HAS_MANY, 'Requireprocessing', 'partnumberid'),
			'receivedcomplists' => array(self::HAS_MANY, 'Receivedcomplist', 'partnumberid'),
			'replacements' => array(self::HAS_MANY, 'Replacement', 'partnumberid'),
			'replacements1' => array(self::HAS_MANY, 'Replacement', 'replacementid'),
			'stores' => array(self::HAS_MANY, 'Store', 'partnumberid'),
			'returncontents' => array(self::HAS_MANY, 'Returncontent', 'partnumberid'),
			'tobuyqties' => array(self::HAS_MANY, 'Tobuyqty', 'partnumberid'),
			'assemblytemplates' => array(self::HAS_MANY, 'Assemblytemplates', 'replid'),
			'assemblies' => array(self::HAS_MANY, 'Assembly', 'assemblyid'),
			'assemblies1' => array(self::HAS_MANY, 'Assembly', 'partid'),
			'templateconfigs' => array(self::HAS_MANY, 'Templateconfig', 'aid'),
			'transfercontents' => array(self::HAS_MANY, 'Transfercontent', 'partnumberid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'partnumberid' => 'Partnumberid',
			'partnumber' => 'Partnumber',
			'type' => 'Type',
			'pathid' => 'Pathid',
			'updated' => 'Updated',
			'is_assembly' => 'Is Assembly',
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

//		$criteria->with = 'componentproperty';
//		for($i=1;$i<=20;$i++){
//		    $criteria->compare('p_'.$i,$this->componentproperty->{'p_'.$i});
//        }
//        $criteria->compare('availabilid',$this->componentproperty->availabilid);

        $criteria->compare('partnumberid',$this->partnumberid);
		$criteria->compare('partnumber',$this->partnumber,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('pathid',$this->pathid);
		$criteria->compare('updated',$this->updated,true);
		$criteria->compare('is_assembly',$this->is_assembly);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public function getComps()
    {
        $criteria=new CDbCriteria();
        $criteria->with = 'componentproperty';
        $criteria->compare('partnumber','RES-0201',true);
        $criteria->order='updated DESC';

        return new CActiveDataProvider(Component::model(), array(
            'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>Yii::app()->params['postsPerPage'],
            ),
        ));
	}

	public static function getComponentByPN($pn){
        $criteria = new CDbCriteria();
        $criteria->condition = 'upper(partnumber) = upper(\''.$pn.'\')';
        return Component::model()->find($criteria);
    }

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Component the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
