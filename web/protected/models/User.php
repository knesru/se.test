<?php

/**
 * The followings are the available columns in table 'tbl_user':
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $profile
 *
 * @property bool $STMSnative
 * @property AdvUserInfo $userinfo
 */
class User extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return static the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'sf_guard_user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('username, password', 'required'),
			array('username, password', 'length', 'max'=>128),
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
//			'posts' => array(self::HAS_MANY, 'Post', 'author_id'),
            'userinfo' => array(self::HAS_ONE, 'AdvUserInfo', 'advuser_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Id',
			'username' => 'Username',
			'password' => 'Password',
            'salt'=> 'Salt',
            'is_active'=> 'Active',
            'is_super_admin'=> 'SuperAdmin',
            'last_login'=> 'Last login',
            'creaded_at'=> 'Create date',
            'updated_at'=> 'Update date',
            'STMSnative'=> 'Доступ к STMS'
		);
	}

	/**
	 * Checks if the given password is correct.
	 * @param string the password to be validated
	 * @return boolean whether the password is valid
	 */
	public function validatePassword($password)
	{
        $use_crowd = true;
        /*if($use_crowd && $this->username && strtolower($this->username) != "admin")
        {
            $crowduserRet = null;
            $crowdManager = new CrowdManager();
            if($crowdManager->IsCrowdPrincipalExist($this->username, $crowduserRet))
            {
                //Try to authrnticate
                if($crowdManager->authenticatePrincipal($this->username, $password))
                {
                    //Auth success
                    //Try to find specified user in DB
                    $user = $this->getTable()->retrieveByUsername($this->username);

                    if($user)
                    {
                        //user was found, validate information in DB
                        if(!$this->validateCrowdAndDBuser($crowdManager, $user, $crowduserRet))
                        {
                            return true;
                        }
                    }
                    else
                    {
                        //user not found, create record in DB
                        $this->createCrowdUserInDB($crowdManager, $crowduserRet);
                        $user = $this->getTable()->retrieveByUsername($this->username);
                    }

                    return true;
                }
                else
                {
                    $err_string = $crowdManager->GetLastError();
                }
            }
            else
            {
                #$err_string = _("The specifed Crowd username dosn't exist");
                $err_string = $crowdManager->GetLastError();
            }
        }*/
        return true;
		return sha1($this->salt.$password)===$this->password;
	}

	/**
	 * Generates the password hash.
	 * @param string password
	 * @return string hash
	 */
	public function hashPassword($password)
	{
		return sha1($this->salt.$password);
	}

    public function getSTMSnative()
    {
        return false;
	}
}
