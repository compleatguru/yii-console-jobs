<?php

/**
 * This is the model class for table "creative_project".
 *
 * The followings are the available columns in table 'creative_project':
 * @property integer $creative_project_id
 * @property integer $creative_user_id
 * @property integer $category_id
 * @property integer $template_id
 * @property integer $project_status
 * @property string $launch_date
 * @property string $close_date
 * @property string $create_date
 * @property string $update_date
 *
 * The followings are the available model relations:
 * @property MCreativeUser $creativeUser
 * @property MCreativeCategory $category
 * @property MCreativeTemplate $template
 * @property CreativeProjectCountryLanguage[] $creativeProjectCountryLanguages
 * @property CreativeProjectQuestionByLanguage[] $creativeProjectQuestionByLanguages
 * @property CreativeProjectAttributeQuestion[] $creativeProjectAttributeQuestions
 * @property CreativeProjectTargetCondition[] $creativeProjectTargetConditions
 * @property CreativeProjectQuestion[] $creativeProjectQuestions
 * @property CreativeProjectQuestionMedia[] $creativeProjectQuestionMedias
 * @property CreativeProjectQuestionRating[] $creativeProjectQuestionRatings
 */
class CreativeProject extends BaseModel {

    /**
     * Project Status Code
     */
    const PROJECT_STATUS_SAVED = 10;
    const PROJECT_STATUS_PAUSE = 20;
    const PROJECT_STATUS_CLOSED = 30;
    const PROJECT_STATUS_LAUNCH_CREATIVE = 101;
    const PROJECT_STATUS_LAUNCH_UNIPASS = 102;
    const PROJECT_STATUS_LAUNCH_UNIPASS_ERROR = 103;
    const PROJECT_STATUS_LAUNCH_QUALTRICS = 104;
    const PROJECT_STATUS_LAUNCH_QUALTRICS_ERROR = 105;
    const PROJECT_STATUS_ERROR = 999;
    const TEMPLATE_ATTRIBUTE = 0;
    const TEMPLATE_IMAGE = 1;

    public $currentTemplate;

    public function behaviors() {
        return array(
            'CTimestampBehavior' => array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'create_time_attribute',
                'updateAttribute' => 'update_time_attribute',
            )
        );
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'creative_project';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('creative_user_id, category_id, template_id, project_status', 'required'),
            array('launch_date', 'dateRangeValidation', 'compareAttribute' => 'close_date', 'operator' => '<'),
            array('creative_user_id, category_id, template_id, project_status', 'numerical', 'integerOnly' => true),
            array('launch_date,close_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('creative_project_id, creative_user_id, category_id, template_id, project_status, launch_date, close_date, create_date, update_date', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'creativeUser' => array(self::BELONGS_TO, 'MCreativeUser', 'creative_user_id'),
            'category' => array(self::BELONGS_TO, 'MCreativeCategory', 'category_id'),
            'template' => array(self::BELONGS_TO, 'MCreativeTemplate', 'template_id'),
            'creativeProjectCountryLanguages' => array(self::HAS_MANY, 'CreativeProjectCountryLanguage', 'creative_project_id'),
            'creativeProjectQuestionByLanguages' => array(self::HAS_MANY, 'CreativeProjectQuestionByLanguage', 'creative_project_id'),
            'creativeProjectAttributeQuestions' => array(self::HAS_MANY, 'CreativeProjectAttributeQuestion', 'creative_project_id'),
            'creativeProjectTargetConditions' => array(self::HAS_MANY, 'CreativeProjectTargetCondition', 'creative_project_id'),
            'creativeProjectQuestions' => array(self::HAS_MANY, 'CreativeProjectQuestion', 'creative_project_id'),
            'creativeProjectQuestionMedias' => array(self::HAS_MANY, 'CreativeProjectQuestionMedia', 'creative_project_id'),
            'creativeProjectQuestionRatings' => array(self::HAS_MANY, 'CreativeProjectQuestionRating', 'creative_project_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'creative_project_id' => 'Creative Project',
            'creative_user_id' => 'Creative User',
            'category_id' => 'Category',
            'template_id' => 'Template',
            'project_status' => 'Project Status',
            'launch_date' => 'Launch Date',
            'close_date' => 'Close Date',
            'create_date' => 'Create Date',
            'update_date' => 'Update Date',
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

        $criteria->compare('creative_project_id', $this->creative_project_id);
        $criteria->compare('creative_user_id', $this->creative_user_id);
        $criteria->compare('category_id', $this->category_id);
        $criteria->compare('template_id', $this->template_id);
        $criteria->compare('project_status', $this->project_status);
        $criteria->compare('launch_date', $this->launch_date, true);
        $criteria->compare('close_date', $this->close_date, true);
        $criteria->compare('create_date', $this->create_date, true);
        $criteria->compare('update_date', $this->update_date, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CreativeProject the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function dateRangeValidation($attribute, $params) {
        if (YII_DEBUG)
            Yii::log('Params' . print_r($params, true));
        $validateParamMap = array('compareAttribute' => 'Missing compareAttribute', 'operator' => 'Missing operator');
        foreach ($validateParamMap as $attr => $errMsg) {
            if (empty($params[$attr])) {
                throw new CException($errMsg);
            }
        }

        $compAttr = $params['compareAttribute'];

        if ($this->hasErrors($attribute) || $this->hasErrors($compAttr) || empty($this->$compAttr))
            return;

        $start = strtotime($this->$attribute);
        $end = strtotime($this->$compAttr);
        $diff = $end - $start;
        switch ($params['operator']) {
            case '<': if ($diff < 0)
                    $this->addError($attribute, $this->getAttributeLabel($attribute) . ' cannot be greater than ' . $this->getAttributeLabel($compAttr));
                break;
            case '>': if ($diff > 0)
                    $this->addError($attribute, $this->getAttributeLabel($attribute) . ' cannot be greater than ' . $this->getAttributeLabel($compAttr)); break;
            default: throw new CException('unknown operator');
        }
    }

    public function isUserProject($project_id) {
        if (empty($project_id))
            return false;

        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array('creative_project_id' => $project_id, 'creative_user_id' => Yii::app()->user->id));
        return $this->exists($criteria);
    }

    public function saveProject() {
        $this->convertDateAttribute(false);
        Yii::log('Saving Project' . print_r($this->attributes, true));
        $attributes = array('creative_user_id', 'category_id', 'template_id', 'project_status', 'creative_project_id', 'launch_date', 'close_date');
        foreach ($attributes as $key => $attribute) {
            if (is_null($this->$attribute))
                unset($attributes[$key]);
        }
        return $this->save(true, $attributes);
    }

    private function convertDateAttribute($isSQLResult = true) {
        $Dateattributes = array('launch_date', 'close_date', 'create_date', 'update_date');
        foreach ($Dateattributes as $attribute) {
            if (!empty($this->$attribute)) {
                if (!$isSQLResult)
                    $this->$attribute = date('d-m-Y', strtotime($this->$attribute));
                else
                    $this->$attribute = date('d-m-Y', strtotime($this->$attribute));
            } else
                $this->$attribute = null;
        }
    }

    protected function afterFind() {
        $this->convertDateAttribute();
        $this->currentTemplate = $this->template_id;
        parent::afterFind();
    }

    public function projectStatus($status = null) {
        $data = array(
            'Save' => CreativeProject::PROJECT_STATUS_SAVED,
            'Launch' => CreativeProject::PROJECT_STATUS_LAUNCH_CREATIVE
        );

        if (empty($status))
            return $data;
        else
            return array_search($status, $data);
    }

    public function deleteProject() {
        $this->creative_user_id = Yii::app()->user->id;
        $criteria = new CDbCriteria();
        $criteria->addInCondition('creative_project_id', $this->creative_project_id);

        $status = array();

        $status[] = creativeProjectTargetCondition::model()->deleteAll($criteria);
        $status[] = CreativeProjectCountryLanguage::model()->deleteAll($criteria);
        $status[] = creativeProjectImageQuestion::model()->deleteAll($criteria);
        $status[] = creativeProjectAttributeQuestion::model()->deleteAll($criteria);

        $status[] = creativeProjectQuestionMedia::model()->deleteAll($criteria);
        $status[] = creativeProjectQuestionByLanguage::model()->deleteAll($criteria);

        $status[] = creativeProjectQuestion::model()->deleteAll($criteria);

        $criteria->addColumnCondition(array('creative_user_id' => $this->creative_user_id));
        $status[] = $this->deleteAll($criteria);
        Yii::log('Delete Project Status' . $status);
    }

    public function getUserProject($userid) {
        return $this->findByAttributes(array('creative_user_id' => $userid));
    }

}
