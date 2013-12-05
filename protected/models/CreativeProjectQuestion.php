<?php

/**
 * This is the model class for table "creative_project_question".
 *
 * The followings are the available columns in table 'creative_project_question':
 * @property integer $creative_project_id
 * @property integer $question_id
 * @property integer $question_type_id
 * @property string $update_date
 *
 * The followings are the available model relations:
 * @property CreativeProject $creativeProject
 * @property CreativeProjectQuestionMedia $creativeProjectQuestionMedias
 * @property CreativeProjectCountryLanguages $creativeProjectCountryLanguages
 * @property CreativeProjectQuestionByLanguages $creativeProjectQuestionByLanguages
 * @property CreativeProjectQuestionRating $creativeProjectQuestionRating
 *
 */
class CreativeProjectQuestion extends BaseModel {

    const QUESTION_TYPE_ATTRIBUTE_ASSOCIATION = 1;
    const QUESTION_TYPE_IMAGE_COMPARISON = 2;

    public $current_question_type_id;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'creative_project_question';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('creative_project_id, question_id', 'required'),
            array('creative_project_id, question_id, question_type_id', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('creative_project_id, question_id, question_type_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'creativeProject' => array(self::BELONGS_TO, 'CreativeProject', 'creative_project_id'),
            'creativeProjectQuestionMedias' => array(self::HAS_MANY, 'CreativeProjectQuestionMedia', array('creative_project_id', 'question_id')),
            'creativeProjectCountryLanguages' => array(self::HAS_MANY, 'CreativeProjectCountryLanguage', 'creative_project_id'),
            'creativeProjectQuestionRating' => array(self::HAS_ONE, 'CreativeProjectQuestionRating', 'creative_project_id,question_id'),
            'creativeProjectQuestionByLanguages' => array(self::HAS_MANY, 'CreativeProjectQuestionByLanguage', 'creative_project_id,question_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'creative_project_id' => 'Creative Project',
            'question_id' => 'Question',
            'question_type_id' => 'Question Type',
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
        $criteria->compare('question_id', $this->question_id);
        $criteria->compare('question_type_id', $this->question_type_id);
        $criteria->compare('update_date', $this->update_date, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CreativeProjectQuestion the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function getProjectAttributeQuestion($creative_project_id) {
        $searchAttributes = array(
            'creative_project_id' => $creative_project_id,
            'question_type_id' => self::QUESTION_TYPE_ATTRIBUTE_ASSOCIATION,
            'question_id' => 1
        );
        $result = $this->findAllByAttributes($searchAttributes);
        if (empty($result)) {
            $model = new CreativeProjectQuestion;
            $model->attributes = $searchAttributes;
            return $model;
        } elseif (count($result) == 1)
            return $result[0];
        else
            throw new CException('more than 1 result found.' . print_r($result, true));
    }

    public function changeQuestionType() {
        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array(
            'creative_project_id', $this->creative_project_id,
            'question_id' => $this->question_id,
        ));

        $status = array();

        $mediaModel = $this->creativeProjectQuestionMedias;
        foreach ($mediaModel as $model) {
            if (empty($model->path))
                unlink($model->path);
        }

        $status[] = CreativeProjectQuestionByLanguage::model()->deleteAll($criteria);
        $status[] = CreativeProjectQuestionMedia::model()->deleteAll($criteria);
        $status[] = CreativeProjectQuestionRating::model()->deleteAll($criteria);
    }

    protected function afterFind() {
        $this->current_question_type_id = $this->question_id;
        parent::afterFind();
    }

}
