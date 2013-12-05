<?php

/**
 * This is the model class for table "m_creative_user".
 *
 * The followings are the available columns in table 'm_creative_user':
 * @property integer $creative_user_id
 * @property string $email
 * @property string $password
 * @property string $first_name
 * @property string $last_name
 * @property string $job_title
 * @property string $user_country
 * @property string $company_name
 * @property string $company_address
 * @property string $contact_number
 * @property string $industry
 * @property string $account_status
 * @property string $account_create_date
 * @property string $account_delete_date
 * @property string $last_login_date
 *
 * The followings are the available model relations:
 * @property CreativeProject[] $creativeProjects
 */
class User extends CActiveRecord {

    // Constants
    const ACCOUNT_STATUS_REGISTERED = 0;
    const ACCOUNT_STATUS_DISABLED = 1;
    const ACCOUNT_STATUS_PENDING = 2;
    const ACCOUNT_STATUS_NEW = 4;

    public $password2;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'm_creative_user';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('email, password, password2, first_name, last_name, user_country, contact_number, industry, account_status, account_create_date', 'required','on'=>'register'),
            array('email, company_name', 'length', 'max' => 64),
            array('password, first_name, last_name, job_title', 'length', 'max' => 32),
            array('password', 'compare', 'compareAttribute' => 'password2', 'message' => 'password entries do not match','on'=>'register'),
            array('email', 'email'),
            array('email', 'unique', 'caseSensitive' => false),
            array('user_country, account_status', 'length', 'max' => 2),
            array('company_address', 'length', 'max' => 256),
            array('contact_number', 'length', 'max' => 24),
            array('industry', 'length', 'max' => 3),
            array('email, password, first_name, last_name, job_title, user_country, company_name, company_address, contact_number, industry', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('creative_user_id, email, password, first_name, last_name, job_title, user_country, company_name, company_address, contact_number, industry, account_status, account_create_date, account_delete_date, last_login_date', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'creativeProjects' => array(self::HAS_MANY, 'CreativeProject', 'creative_user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'creative_user_id' => 'Creative User',
            'email' => 'Email',
            'password' => 'Password',
            'password2' => 'Repeat Password',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'job_title' => 'Job Title',
            'user_country' => 'User Country',
            'company_name' => 'Company Name',
            'company_address' => 'Company Address',
            'contact_number' => 'Contact Number',
            'industry' => 'Industry',
            'account_status' => 'Account Status',
            'account_create_date' => 'Account Create Date',
            'account_delete_date' => 'Account Delete Date',
            'last_login_date' => 'Last Login Date',
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
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('creative_user_id', $this->creative_user_id);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('password', $this->password, true);
        $criteria->compare('first_name', $this->first_name, true);
        $criteria->compare('last_name', $this->last_name, true);
        $criteria->compare('job_title', $this->job_title, true);
        $criteria->compare('user_country', $this->user_country, true);
        $criteria->compare('company_name', $this->company_name, true);
        $criteria->compare('company_address', $this->company_address, true);
        $criteria->compare('contact_number', $this->contact_number, true);
        $criteria->compare('industry', $this->industry, true);
        $criteria->compare('account_status', $this->account_status, true);
        $criteria->compare('account_create_date', $this->account_create_date, true);
        $criteria->compare('account_delete_date', $this->account_delete_date, true);
        $criteria->compare('last_login_date', $this->last_login_date, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return MCreativeUser the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function registerUser() {
        // set other necessary fields
        $this->account_create_date = time();
        $this->user_country = substr($this->user_country, 0, 2);
        $this->account_status = self::ACCOUNT_STATUS_NEW;

        // validate first
        if (!$this->validate())
            return false;

        // special handling
        $this->password = $this->encryptPassword();
        return $this->save(false); // false - no validation required.
    }

    public function verifyUserLogin() {
        $this->password = $this->encryptPassword();
        $criteria = new CDbCriteria;
        $criteria->addColumnCondition(array('email' => $this->email, 'password' => $this->password));
        $criteria->select = 'creative_user_id,first_name,last_name';
        $result = $this->find($criteria);
        if ($result)
            return $result;
        else
            return false;
    }

    public function encryptPassword() {
        return md5($this->password);
    }

}
