<?php

class UserController extends Controller {
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
//    public $layout = '//layouts/column2';

    /**
     * @return array action filters
     */
    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations            
        );
    }

    public function filterAccessControl($filterChain) {
        $rules = $this->accessRules();
        // For non-Guest
        if (!Yii::app()->user->isGuest) {
            switch (strtolower($this->action->id)) {
                case 'update':
                    $rules[] = array('allow');
                    break;

                default: $rules[] = array('deny');
            }
        } elseif (Yii::app()->user->isGuest) {
            // For Guest
            switch (strtolower($this->action->id)) {
                case 'register': $rules[] = array('allow');
                    break;
                default: $rules[] = array('deny');
            }
        } else
            $rules[] = array('deny');

//        Yii::log('Rules applied' . print_r($rules, true));

        $filter = new CAccessControlFilter;
        $filter->setRules($rules);
        $filter->filter($filterChain);
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id) {
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionRegister() {
        $model = new User;

        // Uncomment the following line if AJAX validation is needed
        $this->performAjaxValidation($model);

        if (isset($_POST['User'])) {
            $model->attributes = $_POST['User'];
            if ($model->registerUser()) {
                Yii::app()->user->returnUrl = Yii::app()->createUrl('survey/index');
                $_POST['LoginForm']['username'] = $model->email;
                $_POST['LoginForm']['password'] = $_POST['User']['password']; // $model->password is encrypted when registering

                $this->forward('site/login');
            }
        }

        $model->password = $model->password2 = '';

        $this->render('create', array(
            'model' => $model,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate() {
        $model = $this->loadModel(Yii::app()->user->id);
        $model->scenario = 'update';

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['User'])) {
            $model->attributes = $_POST['User'];
            $connection = Yii::app()->db;
            $transaction = $connection->beginTransaction();
            $status = false;
            try {
                $status = $model->save(true, array('first_name', 'last_name', 'job_title', 'user_country', 'company_name', 'company_address', 'contact_number', 'industry'));
                if ($status) {
                    $transaction->commit();
                }
            } catch (Exception $ex) {

            }
            if ($status) {
                Yii::app()->user->setFlash(CodeTable::FLASH_MESSAGE_SUCCESS, 'Your profile has been updated');
                $this->redirect(array('survey/index'));
            }
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return MCreativeUser the loaded model
     * @throws CHttpException
     */
    public function loadModel($id) {
        $model = User::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param MCreativeUser $model the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'mcreative-user-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
