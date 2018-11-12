<?php

/**
 * ExtcomponentsForm class.
 * ContactForm is the data structure for keeping
 * contact form data. It is used by the 'contact' action of 'SiteController'.
 */
class ExtcomponentsForm extends CFormModel
{
    public $id;
    public $partnumberid;
    public $partnumber;
    public $amount;
    public $userid;
    public $purpose;
    public $created_at;
    public $delivered;
    public $assembly_to;
    public $install_to;
    public $deficite;
    public $description;
    public $install_from;
    public $priority;
    public $requestid;


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
            array('purpose,partnumberid, created_at, assembly_to, install_to, deficite, description, install_from', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, partnumberid, partnumber, amount, purpose, created_at, delivered, assembly_to, install_to, deficite, description, install_from, priority, requestid', 'safe', 'on'=>'search'),
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
}