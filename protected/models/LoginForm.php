<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends CFormModel {

    public $username;
    public $password;
    public $password2;
//	public $rememberMe;

    private $_identity;

    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     */
    public function rules() {
        return array(
            // username and password are required
            array('username, password', 'required', 'on' => 'login'),
            array('username', 'required', 'on' => array('forgetpassword')),
            array('password,password2','required','on'=>'resetpassword'),
            array('password', 'compare', 'compareAttribute' => 'password2', 'message' => 'password entries do not match','on'=>'resetpassword'),
            array('username,password,password2','safe'),
            // rememberMe needs to be a boolean
//			array('rememberMe', 'boolean'),
            // password needs to be authenticated
            array('password', 'authenticate', 'on' => array('login')),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels() {
        return array(
            'password'=>'Password',
            'password2'=>'Repeat Password'
//			'rememberMe'=>'Remember me next time',
        );
    }

    /**
     * Authenticates the password.
     * This is the 'authenticate' validator as declared in rules().
     */
    public function authenticate($attribute, $params) {
        if (!$this->hasErrors()) {
            $this->_identity = new UserIdentity($this->username, $this->password);
            if (!$this->_identity->authenticate())
                $this->addError('password', 'Incorrect username or password.');
        }
    }

    /**
     * Logs in the user using the given username and password in the model.
     * @return boolean whether login is successful
     */
    public function login() {
        if ($this->_identity === null) {
            $this->_identity = new UserIdentity($this->username, $this->password);
            $this->_identity->authenticate();
        }
        if ($this->_identity->errorCode === UserIdentity::ERROR_NONE) {
            $duration = 3600 * 24 * 30; // 30 days
            Yii::app()->user->login($this->_identity, $duration);
            return true;
        } else
            return false;
    }

    public function isValidUser() {
        if (!empty($this->username)) {
            $criteria = new CDbCriteria();
            $criteria->addColumnCondition(array('email'=>$this->username));
            return User::model()->find($criteria);
        }
        return false;
    }

    public function resetUserpassword($userid){
        $user = User::model()->findByAttributes(array('email'=>$this->username));
        $user->password = $this->password;
        $user->password = $user->encryptPassword();
        $user->save(false,array('password'));
    }
}
