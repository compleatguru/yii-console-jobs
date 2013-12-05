<?php

class SiteController extends Controller {

    /**
     * @return array action filters
     */
    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
            'changeLayout'
        );
    }

    public function filterChangeLayout($filterChain) {
        if (!Yii::app()->user->isGuest)
            $this->layout = 'user';
        $filterChain->run();
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            /*
              array('allow',  // allow all users to perform 'index' and 'view' actions
              'actions'=>array('index','view',),
              'users'=>array('*'),
              ),
              array('allow', // allow authenticated user to perform 'create' and 'update' actions
              'actions'=>array('create','update'),
              'users'=>array('@'),
              ),
              array('allow', // allow admin user to perform 'admin' and 'delete' actions
              'actions'=>array('admin','delete'),
              'users'=>array('admin'),
              ),
             */
            array('allow',
                'actions' => array('login', 'forgetpassword', 'resetpassword'),
                'users' => array('?'), // guest
            ),
            array('allow',
                'actions' => array('logout'),
                'users' => array('@') // authenticated user
            ),
            array('allow',
                'actions' => array('index', 'page', 'flush', 'api'),
                'users' => array('*'), // all
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Declares class-based actions.
     */
    public function actions() {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex() {
        // renders the view file 'protected/views/site/index.php'
        // using the default layout 'protected/views/layouts/main.php'
        $this->render('index');
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError() {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    /**
     * Displays the contact page
     */
    public function actionContact() {
        $model = new ContactForm;
        if (isset($_POST['ContactForm'])) {
            $model->attributes = $_POST['ContactForm'];
            if ($model->validate()) {
                $name = '=?UTF-8?B?' . base64_encode($model->name) . '?=';
                $subject = '=?UTF-8?B?' . base64_encode($model->subject) . '?=';
                $headers = "From: $name <{$model->email}>\r\n" .
                        "Reply-To: {$model->email}\r\n" .
                        "MIME-Version: 1.0\r\n" .
                        "Content-Type: text/plain; charset=UTF-8";

                mail(Yii::app()->params['adminEmail'], $subject, $model->body, $headers);
                Yii::app()->user->setFlash('contact', 'Thank you for contacting us. We will respond to you as soon as possible.');
                $this->refresh();
            }
        }
        $this->render('contact', array('model' => $model));
    }

    /**
     * Displays the login page
     */
    public function actionLogin() {
        $model = new LoginForm;

        // if it is ajax validation request
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
        // collect user input data
        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];

            // validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login()) {

                switch (Yii::app()->user->returnUrl) {
                    case Yii::app()->baseUrl . '/index.php':
                    case Yii::app()->baseUrl:
                        Yii::app()->user->returnUrl = Yii::app()->createUrl('survey/index');
                }
                $this->redirect(Yii::app()->user->returnUrl);
            }
        }
        // display the login form
        $this->render('login', array('model' => $model));
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout() {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

    public function actionForgetPassword() {
        $model = new LoginForm('forgetpassword');
        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];

            $model->validate();
            $user = $model->isValidUser();
            if (!is_null($user)) {
                // send email to reset password
                $service = new EmailService;
                if($service->emailUserResetPassword($user)){
                    Yii::app()->user->setFlash(CodeTable::FLASH_MESSAGE_SUCCESS, 'Email has been sent');
                }
                else{
                   Yii::app()->user->setFlash(CodeTable::FLASH_MESSAGE_ERROR, 'Email was not sent');
                }
            } else {
                $model->addError('username', 'Unknown user: ' . $model->username);
                $model->username = null;
            }
        }
        $this->render('forgetpassword', array('model' => $model));
    }

    /**
     * Adopt from PanelSite Reset Password
     * email=<email>&dob=<dob>&d=<long_date>&sig1=<sig1>&sig2=<sig2>
Signature is generated by hashing the following
sig1: SHA1(email=<email>&dob=<dob>&d=<long_date>)
sig2: SHA1(id=<panel_member_id>&email=<email>&dob=<dob>&d=<long_date>)
     * @param type $key
     */
    public function actionResetPassword($key) {
        $model = new LoginForm('resetpassword');
        if ($_POST['LoginForm']) {
            $model->attributes = $_POST['LoginForm'];
            
            if($model->validate()){
                // reset user password
                Yii::app()->user->setFlash(CodeTable::FLASH_MESSAGE_SUCCESS, 'Email has been sent');
            }
            
        }
        $model->password=$model->password2=null;
        $this->render('resetpassword',array('model' => $model));
    }

    public function actionFlush() {
        Yii::app()->cache->flush();
        $this->redirect(Yii::app()->homeUrl);
    }

    public function actionApi() {
        Yii::app()->cache->flush();

        $ct = new CodeTable();
        $out = $ct->getCountryPrefectureCity(array('HK','FR','US','SG'),array("12001002","12001004","03001009","03002009","03002028","03004013","06001000"));
        echo json_encode($out);
//        print_r($ct->getLanguageDescription('FR13'));
//        print_r($ct->data(CodeTable::COUNTRY_PREFECTURE));
//        $codeTable->data(CodeTable::COUNTRIES);
//        print_r($ct->data(CodeTable::MARRIAGE));
//        print_r($ct->data(CodeTable::GENDER));
    }

}
