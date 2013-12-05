<?php

/**
 * Controller is only active for login users only.
 * If not, redirect them for login.
 */
class SurveyController extends Controller {

    const TAB_CREATION = 0;
    const TAB_NEW = 1;
    const TAB_STATS = 2;
    const TAB_EDIT = 3;

    public $layout = "user";

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
                case 'index':
                case 'prefecturecity':
                case 'language':
                case 'newsurvey':
                    if (!empty(Yii::app()->user->id) && !Yii::app()->user->isGuest)
                        $rules[] = 'allow';
                    else
                        $rules[] = 'deny';
                    break;
                case 'editsurvey':
                case 'imagecomparison':
                case 'attributesassociation':
                    if (CreativeProject::model()->isUserProject($this->actionParams['id']))
                        $rules[] = array('allow');
                    else {
                        $rules[] = array('deny');
                    }
                    break;

                default: $rules[] = array('deny');
            }
        } else // For Guest
            $rules[] = array('deny');

//        Yii::log('Rules applied' . print_r($rules, true));

        $filter = new CAccessControlFilter;
        $filter->setRules($rules);
        $filter->filter($filterChain);
    }

    /**
     * User Survey List
     */
    public function actionIndex() {
        if (YII_DEBUG)
            Yii::log('running survey/index');
        /**
         * Get all surveys created by User
         */
        $project = $this->getPOSTData(new CreativeProject);
        /* @var $project CreativeProject */
        $project->creative_user_id = Yii::app()->user->id;
        if (Yii::app()->request->isAjaxRequest) {
            Yii::log('_POST:' . print_r($_POST, true));

            if (isset($_POST['action']) && isset($_POST['creative_project_id'])) {
                switch (strtolower($_POST['action'])) {
                    case 'delete':
                        $project->creative_project_id = $_POST['creative_project_id'];
                        $project->deleteProject();
                        if (YII_DEBUG)
                            Yii::log('delete projects: ' . print_r($project->creative_project_id, true));
                        $project->creative_project_id = null;
                }
            }
        }
        $this->render('index', array('dataProvider' => $project->search(), 'activeTab' => self::TAB_CREATION));
    }

    public function actionNewSurvey() {

        /* @var  $creativeProject CreativeProject */
        $creativeProject = $this->getPOSTData(new CreativeProject());

        /* @var  $SurveyTargetingForm SurveyTargetingForm */
        $SurveyTargetingForm = $this->getPOSTData(new SurveyTargetingForm);

        /* @var  $creativeProjectCountryLanguage CreativeProjectCountryLanguage */
        $creativeProjectCountryLanguage = $this->getPOSTData(new CreativeProjectCountryLanguage());

        if (!empty($_POST)) {
            Yii::log('_POST', print_r($_POST, true));
        }

        // uncomment the following code to enable ajax-based validation
        /*
          if(isset($_POST['ajax']) && $_POST['ajax']==='creative-project-creativeprojectform-form')
          {
          echo CActiveForm::validate($model);
          Yii::app()->end();
          }
         */

        if (isset($_POST['CreativeProject']) &&
                $creativeProject->validate()
        ) {
            $connection = Yii::app()->db;
            $transaction = $connection->beginTransaction();
            try {
                Yii::log('Project Attributes:' . print_r($creativeProject->attributes));
                Yii::log('Saving Project');
                Yii::log('SurveyTargetingForm' . print_r($SurveyTargetingForm->attributes, true));
                Yii::log('$creativeProjectCountryLanguage' . print_r($creativeProjectCountryLanguage->attributes, true));
                if ($creativeProject->saveProject()) {
                    $projectId = $creativeProject->creative_project_id;
                    $SurveyTargetingForm->creative_project_id = $creativeProjectCountryLanguage->creative_project_id = $projectId;
                    $SurveyTargetingForm->saveProjectCountryLanguage();
                    $SurveyTargetingForm->saveTargetingCondition();
                    $transaction->commit();
                } else {
                    Yii::log('Saving Project Error' . print_r($creativeProject->getErrors(), true));
                    $transaction->rollback();
                }
            } catch (Exception $e) { // an exception is raised if a query fails
                $transaction->rollback();
                Yii::app()->user->setFlash(CodeTable::FLASH_MESSAGE_ERROR, "Unable to save Survey Project");
            }
        }


        // Set Flash Message
        if ($creativeProject->hasErrors() || $SurveyTargetingForm->hasErrors() || $creativeProjectCountryLanguage->hasErrors()) {
            Yii::app()->user->setFlash(CodeTable::FLASH_MESSAGE_ERROR, "Unable to save Survey Project");
        } elseif (!empty($creativeProject->creative_project_id)) {
            Yii::app()->user->setFlash(CodeTable::FLASH_MESSAGE_SUCCESS, "New Survey Project is saved successfully!");
            // Set Navigation
            if (isset($_POST['Setup'])) {
                if (!empty($creativeProject->creative_project_id))
                    $this->processRedirect($creativeProject);
            }
        }

        Yii::log('Creative Project:' . print_r($creativeProject->getErrors(), true));
        Yii::log('Survey Targeting Form:' . print_r($SurveyTargetingForm->getErrors(), true));
        Yii::log('Creative Project Country Language:' . print_r($creativeProjectCountryLanguage->getErrors(), true));

        // since we are still in this page.
        $creativeProject->creative_project_id = null;
        $this->render('index', array('creativeProject' => $creativeProject, 'SurveyTargetingForm' => $SurveyTargetingForm, 'creativeProjectCountryLanguage' => $creativeProjectCountryLanguage, 'page' => 'start', 'activeTab' => self::TAB_NEW));
    }

    private function getPOSTData($model) {
        if (!is_a($model, 'CModel'))
            throw new CException('Unknown Model:' . $model);

        $className = get_class($model);


        // Special Handling
        switch ($className) {
            case 'CreativeProject':
                if (isset($_POST[$className])) {
                    $model->attributes = array_map(array('MyStringLibrary', 'mtrim'), $_POST[$className]);
                    $actionButtonNameList = array(
                        'Setup' => CreativeProject::PROJECT_STATUS_SAVED,
                        'Save' => CreativeProject::PROJECT_STATUS_SAVED,
                        'Launch' => CreativeProject::PROJECT_STATUS_LAUNCH_CREATIVE
                    );

                    foreach ($actionButtonNameList as $actionName => $value) {
                        if (isset($_POST[$actionName]))
                            $model->project_status = $value;
                    }

                    $model->creative_user_id = Yii::app()->user->id;
                }
                break;
            case 'SurveyTargetingForm':
                if (isset($_POST['SurveyTargetingForm'])) {
                    $model->language = $model->title = $model->prefecture = array();
                    $model->attributes = $_POST['SurveyTargetingForm'];

                    if (!empty($model->language)) {
                        foreach ($model->language as $languageid) {
                            $id = substr($languageid, 2);
                            Yii::log('Language ID: ' . $id);
                            if (isset($_POST[$id])) {
                                Yii::log('Language ID: ' . $id);
                                $model->title[$languageid] = $_POST[$id];
                            }
                        }
                    }
                }
                break;
            case 'CreativeProjectQuestionMedia':
                if (isset($_POST['CreativeProjectQuestionMedia'])) {
                    Yii::log('CreativeProjectQuestionMedia  POST Data:' . print_r($_POST, true));
                    $model->attributes = array_map(array('MyStringLibrary', 'mtrim'), $_POST[$className]);
                    /* @var $model creativeProjectQuestionMedia */
                    if (!isset($_POST['DeleteImage']))
                        $model->path = $model->current_path; // retain current image
                }
                break;
            default:if (isset($_POST[$className])) {
                    $model->attributes = array_map(array('MyStringLibrary', 'mtrim'), $_POST[$className]);
                }
        }
//        if(YII_DEBUG)
        Yii::log('POST Init ' . $className . ' Attributes' . print_r($model->attributes, true));
        return $model;
    }

    public function actionEditSurvey($id) {
        if (YII_DEBUG)
            Yii::log('Post Data:' . print_r($_POST, true));

        $creativeProject = CreativeProject::model()->findByPk($id);
        /* @var  $creativeProject CreativeProject */
        $creativeProject = $this->getPOSTData($creativeProject);

        /* @var  $SurveyTargetingForm SurveyTargetingForm */
        $SurveyTargetingForm = new SurveyTargetingForm();

        $SurveyTargetingForm->init($creativeProject->creativeProjectCountryLanguages, $creativeProject->creativeProjectTargetConditions);

        $SurveyTargetingForm = $this->getPOSTData($SurveyTargetingForm);

        if (YII_DEBUG)
            Yii::log('$SurveyTargetingForm' . print_r($SurveyTargetingForm->attributes, true));


        if (isset($_POST['CreativeProject'])) {
            $SurveyTargetingForm->creative_project_id = $creativeProject->creative_project_id = $id;
            $SurveyTargetingForm->scenario = 'update';
            $connection = Yii::app()->db;
            $transaction = $connection->beginTransaction();
            try {
                if ($creativeProject->saveProject() && $SurveyTargetingForm->validate()) {
                    $SurveyTargetingForm->saveProjectCountryLanguage($creativeProject->creativeProjectCountryLanguages);
                    $SurveyTargetingForm->saveTargetingCondition($creativeProject->creativeProjectTargetConditions);
                    $transaction->commit();
                    Yii::app()->user->setFlash(CodeTable::FLASH_MESSAGE_SUCCESS, "Project#{$creativeProject->creative_project_id} is saved successfully!");
                }
            } catch (Exception $e) { // an exception is raised if a query fails
                $transaction->rollback();
                Yii::app()->user->setFlash(CodeTable::FLASH_MESSAGE_ERROR, "Unable to save Survey Project");
            }
        }

        // Set Navigation
        if (isset($_POST['Setup']) &&
                !$creativeProject->hasErrors() &&
                !$SurveyTargetingForm->hasErrors()) {
            $this->processRedirect($creativeProject);
        }

        Yii::log('Errors: ' . print_r($creativeProject->getErrors(), true) . ': ' . print_r($SurveyTargetingForm->getErrors(), true));


        $this->render('index', array('creativeProject' => $creativeProject, 'SurveyTargetingForm' => $SurveyTargetingForm, 'page' => 'start', 'activeTab' => self::TAB_EDIT));
    }

    public function actionStatistics() {
        $survey = new CreativeProject;
        $this->render('index', array('dataProvider' => $survey->search(), 'activeTab' => self::TAB_STATS));
    }

    /**
     *
     * @param CreativeProject $model
     */
    private function processRedirect($model) {
        switch ($model->template_id) {
            case CreativeProject::TEMPLATE_ATTRIBUTE:
                $this->redirect(array('survey/attributesassociation', 'id' => $model->creative_project_id));
            case CreativeProject::TEMPLATE_IMAGE:
                $this->redirect(array('survey/imagecomparison', 'id' => $model->creative_project_id));
        }
    }

    public function actionImageComparison($id) {
        /* @var $creativeProjectQuestion CreativeProjectQuestion */
        $creativeProjectQuestion = $this->getPOSTData(CreativeProjectQuestion::model()->getProjectAttributeQuestion($id));

        /* @var $creativeProjectQuestionMedia CreativeProjectQuestionMedia */
        $creativeProjectQuestionMedia = $creativeProjectQuestion->creativeProjectQuestionMedias;

        /* @var $creativeProjectQuestionByLanguage CreativeProjectQuestionByLanguage */
        $creativeProjectQuestionByLanguage = $creativeProjectQuestion->creativeProjectQuestionByLanguages;

        if (empty($creativeProjectQuestionByLanguage)) {
            $projectLanguages = CreativeProjectCountryLanguage::model()->findAllByAttributes(array('creative_project_id' => $id));
            /* @var $languageModel CreativeProjectCountryLanguage */
            foreach ($projectLanguages as $languageModel) {
                $model = new CreativeProjectQuestionByLanguage;
                $model->attributes = array(
                    'creative_project_id' => $id,
                    'question_id' => $creativeProjectQuestion->question_id,
                    'country_id' => $languageModel->country_id,
                    'language_id' => $languageModel->language_id,
                );
                $creativeProjectQuestionByLanguage[] = $model;
            }
        }

        $creativeProjectQuestionByLanguage = $this->getPOSTQuestion($creativeProjectQuestionByLanguage);

        $creativeProjectQuestionRating = $creativeProjectQuestion->creativeProjectQuestionRating;
        if (!empty($creativeProjectQuestionRating)) {
            $creativeProjectQuestionRating = $this->getPOSTData($creativeProjectQuestion->creativeProjectQuestionRating);
        } else
            $creativeProjectQuestionRating = $this->getPOSTData(new CreativeProjectQuestionRating);

        Yii::log('creativeProjectQuestion:' . print_r($creativeProjectQuestion, true));
        Yii::log('creativeProjectQuestionRating:' . print_r($creativeProjectQuestionRating, true));
        Yii::log('creativeProjectQuestionByLanguage:' . print_r($creativeProjectQuestionByLanguage, true));
        Yii::log('creativeProjectQuestionMedia:' . print_r($creativeProjectQuestionMedia, true));

        if (isset($_POST['CreativeProjectQuestionRating'])) {
            $mediaModelList = $this->saveImageRater($creativeProjectQuestionMedia, $creativeProjectQuestion);
            if (YII_DEBUG)
                Yii::log('Media Model List ' . print_r($mediaModelList, true));

            $connection = Yii::app()->db;
            $transaction = $connection->beginTransaction();
            try {
                $errors = array();

                if ($creativeProjectQuestion->validate())
                    $creativeProjectQuestion->save();
                else
                    $errors['creativeProjectQuestion'] = $creativeProjectQuestion->getErrors();

                foreach ($creativeProjectQuestionByLanguage as $model) {
                    if ($model->current_question_text != $model->question_text) {
                        if ($model->validate())
                            $model->save();
                        else
                            $errors['creativeProjectQuestionByLanguage'][] = $model->getErrors();
                    }
                }

                foreach ($mediaModelList as $model) {
                    if ($model->validate())
                        $model->save();
                    else
                        $errors['creativeProjectQuestionMedia'] = $model->getErrors();
                }

                if ($creativeProjectQuestionRating->isNewRecord) {
                    $creativeProjectQuestionRating->attributes = array('creative_project_id' => $creativeProjectQuestion->creative_project_id,
                        'question_id' => $creativeProjectQuestion->question_id);
                }

                if ($creativeProjectQuestionRating->validate())
                    $creativeProjectQuestionRating->save();
                else
                    $errors['creativeProjectQuestionRating'] = $creativeProjectQuestionRating->getErrors();

                if (empty($errors)) {
                    $transaction->commit();
                    Yii::app()->user->setFlash(CodeTable::FLASH_MESSAGE_SUCCESS, "Project#{$creativeProjectQuestion->creative_project_id} is saved successfully!");

                    // refresh
                    $creativeProjectQuestion->refresh();
                    $creativeProjectQuestionMedia = $creativeProjectQuestion->creativeProjectQuestionMedias;
                } else {
                    Yii::log('Errors found:' . print_r($errors, true));
                    $transaction->rollback();
                    Yii::app()->user->setFlash(CodeTable::FLASH_MESSAGE_ERROR, "Unable to save Survey Project");
                }
            } catch (Exception $e) { // an exception is raised if a query fails
                $transaction->rollback();
                Yii::app()->user->setFlash(CodeTable::FLASH_MESSAGE_ERROR, "Unable to save Survey Project");
            }
        }

        $this->render('index', array('page' => 'imagecomparison', 'creativeProjectQuestionByLanguage' => $creativeProjectQuestionByLanguage, 'creativeProjectQuestionMedia' => $creativeProjectQuestionMedia, 'creativeProjectQuestion' => $creativeProjectQuestion, 'creativeProjectQuestionRating' => $creativeProjectQuestionRating, 'activeTab' => self::TAB_EDIT));
    }

    public function actionAttributesAssociation($id) {
        /* @var $creativeProjectQuestion CreativeProjectQuestion */
        $creativeProjectQuestion = $this->getPOSTData(CreativeProjectQuestion::model()->getProjectAttributeQuestion($id));

        if (YII_DEBUG) {
            Yii::log('Creative Project Question:' . print_r($creativeProjectQuestion->attributes, true));
            Yii::log('Creative Project Question Medias:' . print_r($creativeProjectQuestion->creativeProjectQuestionMedias, true));
        }

        /* @var $creativeProjectQuestionMedia CreativeProjectQuestionMedia */
        if (empty($creativeProjectQuestion->creativeProjectQuestionMedias) || count($creativeProjectQuestion->creativeProjectQuestionMedias) > 1) {
            $mediaModel = new CreativeProjectQuestionMedia;
        } elseif (count($creativeProjectQuestion->creativeProjectQuestionMedias) == 1) {
            $mediaModel = $creativeProjectQuestion->creativeProjectQuestionMedias[0];
        }

        $creativeProjectQuestionMedia = $this->getPOSTData($mediaModel);

        Yii::log('Config:' . print_r($config, true));
        Yii::log('Media:' . print_r($mediaModel->attributes, true));
        Yii::log('Current Media Storage Path:' . print_r($mediaModel->current_path, true));
        Yii::log(print_r($_POST, true));
        Yii::log(print_r($_FILES, true));

        // store image file
        if (isset($_POST['CreativeProjectQuestionMedia'])) {

            $fileStoreService = new FileStoreService;
            if (!isset($_POST['DeleteImage']) && $_FILES['CreativeProjectQuestionMedia']['size']['path'] > 0) {
                $uploadedFile = CUploadedFile::getInstance($creativeProjectQuestionMedia, 'path');
                $dest = $fileStoreService->saveFile($uploadedFile);
                if ($dest !== false)
                    $creativeProjectQuestionMedia->path = $dest;
                else
                    throw new CException('Unable to save file: ' . print_r($_FILES, true));
            }
            elseif (isset($_POST['DeleteImage']) && !empty($creativeProjectQuestionMedia->current_path)) {
                $fileStoreService->deleteFile($creativeProjectQuestionMedia->current_path);
            }

            Yii::log('$creativeProjectQuestionMedia :' . print_r($creativeProjectQuestionMedia->attributes, true));
            Yii::log('Validation error: ' . print_r($creativeProjectQuestionMedia->getErrors(), true));

            $connection = Yii::app()->db;
            $transaction = $connection->beginTransaction();
            try {
                $creativeProjectQuestionMedia->attributes = array(
                    'creative_project_id' => $creativeProjectQuestion->creative_project_id,
                    'question_id' => $creativeProjectQuestion->question_id,
                    'index' => 0,
                );
                if ($creativeProjectQuestion->validate() && $creativeProjectQuestionMedia->validate()) {
                    if (($creativeProjectQuestion->current_question_type_id == $creativeProjectQuestion->question_id) || $creativeProjectQuestion->isNewRecord)
                        $creativeProjectQuestion->save();
                    if ($creativeProjectQuestionMedia->path != $creativeProjectQuestionMedia->current_path) {
                        if (!empty($creativeProjectQuestionMedia->path))
                            $creativeProjectQuestionMedia->save();
                        elseif (isset($_POST['DeleteImage']))
                            $creativeProjectQuestionMedia->delete();
                    }


                    $transaction->commit();
                    Yii::app()->user->setFlash(CodeTable::FLASH_MESSAGE_SUCCESS, "Project#{$creativeProjectQuestion->creative_project_id} is saved successfully!");
                } else
                    throw new CException('Validation Error.' . print_r($creativeProjectQuestion->getErrors(), true) . print_r($creativeProjectQuestionMedia->getErrors(), true));
            } catch (Exception $ex) {
                $transaction->rollback();
                Yii::app()->user->setFlash(CodeTable::FLASH_MESSAGE_ERROR, "Unable to save Survey Project");
            }

            Yii::log($creativeProjectQuestionMedia->path);
        }

        $this->render('index', array('page' => 'attributesassociation', 'creativeProjectQuestionMedia' => $creativeProjectQuestionMedia, 'creativeProjectQuestion' => $creativeProjectQuestion, 'activeTab' => self::TAB_EDIT));
    }

    public function actionLanguage($country, $json = true) {
        if (is_array($country))
            $list = $country;
        else
            $list = explode(',', $country);

        $list = array_map('html_entity_decode', $list);

//        if(YII_DEBUG)
//        Yii::log('actionLanguage: $list' . print_r($list, true));

        if (empty($list))
            $list[] = $country;

        $codeTable = new CodeTable;

        $out = array();
        asort($list);
        foreach ($list as $country_id) {
            $countryName = $codeTable->country($country_id);
            Yii::log('Source:' . $country_id);
            $languages = $codeTable->data(CodeTable::COUNTRY_LANGUAGE, $country_id);
            // Format for select2
            $children = array();
            foreach ($languages as $language) {
                $children[] = array('id' => $country_id . $language['id'], 'text' => $countryName . '-' . $language['name']);
            }

            // Format to select2
            if (!empty($children)) {
                $out[] = array(
                    'id' => $country_id,
                    'text' => $countryName,
                    'children' => $children
                );
            }
        }
        if ($json)
            echo json_encode($out);
        else
            return $out;
    }

    public function actionCountry($countryid) {
        $ct = new CodeTable();

        if (!empty($countryid))
            $countryName = $ct->country($countryid);

        if (!empty($countryName)) {
            header("application/json; charset=utf-8");
            echo json_encode($countryName);
        }
    }

    public function actionPrefectureCity($country, $json = true) {
        if (empty($country)) {
            echo json_encode('');
        }

        if (is_array($country))
            $list = $country;
        else
            $list = explode(',', $country);

        if (empty($list))
            $list[] = $country;
        $out = array();
        asort($list);
        foreach ($list as $country_id) {
            $cacheID = CodeTable::COUNTRY_PREFECTURE . '.' . $country_id;
            $data = Yii::app()->cache->get($cacheID);
            if (!$data) {
                $codeTable = new CodeTable;
                $data = $codeTable->data(CodeTable::COUNTRY_PREFECTURE, $country_id);
                if (!empty($data)) {
                    Yii::app()->cache->set($cacheID, $data, CodeTable::CACHE_DURATION);
                }
            }
            $out[$country_id] = $data;
        }

        if ($json) {
            header("application/json; charset=utf-8");
            echo json_encode($out);
        } else
            return $out;
    }

    public function actionTest() {
        $cs = Yii::app()->clientScript;
        $cs->registerScriptFile('knockout.js');
        $this->render('test');
    }

    /**
     *
     * @param CreativeProjectQuestionMedia $creativeProjectQuestionMedia
     * @param CreativeProjectQuestion $creativeProjectQuestion
     */
    private function saveImageRater($creativeProjectQuestionMedia, $creativeProjectQuestion) {
        $result = array();

        if (count($_POST) > 1) {
            if (count($_FILES) >= 1) {
                $fileStoreService = new FileStoreService;
                Yii::log('FILES' . print_r($_FILES, true));
                $fileIdLength = strlen("rater");
                foreach ($_FILES as $index => $file) {
                    $uploadedFile = CUploadedFile::getInstanceByName($index);
                    $mediaIndex = substr($index, $fileIdLength, 1);
                    Yii::log('Media Index:' . $mediaIndex);
                    if ($uploadedFile->error == UPLOAD_ERR_OK) {
                        $dest = $fileStoreService->saveFile($uploadedFile);
                        if ($dest !== false) {
                            if (empty($creativeProjectQuestionMedia)) {
                                $model = new CreativeProjectQuestionMedia;
                                $model->attributes = array(
                                    'creative_project_id' => $creativeProjectQuestion->creative_project_id,
                                    'question_id' => $creativeProjectQuestion->question_id,
                                    'index' => $mediaIndex,
                                    'path' => $dest
                                );
                            } else {
                                $found = false;
                                foreach ($creativeProjectQuestionMedia as $model) {
                                    if ($model->index == $mediaIndex && !$found) {
                                        $fileStoreService->deleteFile($model->path);
                                        $model->path = $dest;
                                        $found = true;
                                        Yii::log('Found Media Model:' . print_r($model->attributes, true));
                                        break;
                                    }
                                }
                                if (!$found) {
                                    // New media
                                    $model = new CreativeProjectQuestionMedia;
                                    $model->attributes = array(
                                        'creative_project_id' => $creativeProjectQuestion->creative_project_id,
                                        'question_id' => $creativeProjectQuestion->question_id,
                                        'index' => $mediaIndex,
                                        'path' => $dest
                                    );
                                }
                            }
                            if (YII_DEBUG)
                                Yii::log('Media Model:' . print_r($model->attributes, true));
                            $result[] = $model;
                        }
                    }
                    Yii::log('FILE:' . print_r($file, true));
                }
            }
            Yii::log('POST' . print_r($_POST, true));
        }
        Yii::log('Result:' . print_r($result, true));
        return $result;
        ;
    }

    /**
     *
     * @param array of CreativeProjectQuestionByLanguage  $modelList
     * @return CreativeProjectQuestionByLanguage
     */
    private function getPOSTQuestion($modelList) {
        if (isset($_POST['CreativeProjectQuestionRating'])) {
            /* @var $model CreativeProjectByLanguage */
            foreach ($modelList as $index => $model) {
                $langid = $model->language_id;
                if (isset($_POST[$langid])) {
                    $modelList[$index]->question_text = $_POST[$langid];
                }
            }
        }
        return $modelList;
    }

}
