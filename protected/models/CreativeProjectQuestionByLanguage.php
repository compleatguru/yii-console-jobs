<?php

/**
 * This is the model class for table "creative_project_question_by_language".
 *
 * The followings are the available columns in table 'creative_project_question_by_language':
 * @property integer $creative_project_id
 * @property integer $question_id
 * @property integer $language_id
 * @property string $question_text
 * @property integer $country_id
 *
 * The followings are the available model relations:
 * @property CreativeProject $creativeProject
 */
class CreativeProjectQuestionByLanguage extends CActiveRecord
{
    public $current_question_text;
    
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'creative_project_question_by_language';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('creative_project_id, question_id, language_id, question_text, country_id', 'required'),
			array('creative_project_id, question_id, language_id', 'numerical', 'integerOnly'=>true),
      array('country_id','length','max'=>2),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('creative_project_id, question_id, language_id, question_text, country_id', 'safe', 'on'=>'search'),
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
			'creativeProject' => array(self::BELONGS_TO, 'CreativeProject', 'creative_project_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'creative_project_id' => 'Creative Project',
			'question_id' => 'Question',
			'language_id' => 'Language',
			'question_text' => 'Question Text',
			'country_id' => 'Country',
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
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('creative_project_id',$this->creative_project_id);
		$criteria->compare('question_id',$this->question_id);
		$criteria->compare('language_id',$this->language_id);
		$criteria->compare('question_text',$this->question_text,true);
		$criteria->compare('country_id',$this->country_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CreativeProjectQuestionByLanguage the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
     protected function afterFind() {
        $this->current_question_text = $this->question_text;
        parent::afterFind();
    }
}
