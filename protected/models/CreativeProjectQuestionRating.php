<?php

/**
 * This is the model class for table "creative_project_question_rating".
 *
 * The followings are the available columns in table 'creative_project_question_rating':
 * @property integer $creative_project_id
 * @property integer $question_id
 * @property string $media_layout
 * @property string $rating_type
 * @property integer $min_value
 * @property integer $max_value
 * @property string $measurement_unit
 * @property string $update_date
 *
 * The followings are the available model relations:
 * @property CreativeProject $creativeProject
 */
class CreativeProjectQuestionRating extends BaseModel {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'creative_project_question_rating';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('creative_project_id,question_id', 'required'),
            array('creative_project_id, min_value, max_value', 'numerical', 'integerOnly' => true),
            array('media_layout, rating_type', 'length', 'max' => 1),
            array('measurement_unit', 'length', 'max' => 8),
            array('min_value','compare','operator'=>'<=','compareAttribute'=>'max_value'),
            array('max_value','compare','operator'=>'>=','compareAttribute'=>'min_value'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('creative_project_id, question_id, media_layout, rating_type, min_value, max_value, measurement_unit', 'safe', 'on' => 'search'),
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
            'creativeProjectQuestion' => array(self::BELONGS_TO, 'CreativeProjectQuestion', array('creative_project_id','question_id')),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'creative_project_id' => 'Creative Project',
            'question_id' => 'Question',
            'media_layout' => 'Media Layout',
            'rating_type' => 'Rating Type',
            'min_value' => 'Min Value',
            'max_value' => 'Max Value',
            'measurement_unit' => 'Measurement Unit',
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
        $criteria->compare('media_layout', $this->media_layout, true);
        $criteria->compare('rating_type', $this->rating_type, true);
        $criteria->compare('min_value', $this->min_value);
        $criteria->compare('max_value', $this->max_value);
        $criteria->compare('measurement_unit', $this->measurement_unit, true);
        $criteria->compare('update_date', $this->update_date, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CreativeProjectQuestionRating the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
