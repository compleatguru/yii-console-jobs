<?php

/**
 * This is the model class for table "creative_project_image_answer".
 *
 * The followings are the available columns in table 'creative_project_image_answer':
 * @property integer $creative_project_id
 * @property integer $question_id
 * @property string $uid
 * @property string $media_file_rate_1
 * @property string $media_file_rate_2
 * @property string $media_file_rate_3
 * @property string $media_file_rate_4
 * @property string $media_file_rate_5
 * @property string $update_date
 *
 * The followings are the available model relations:
 * @property CreativeProjectAttributeQuestion $creativeProject
 * @property CreativeProjectAttributeQuestion $question
 */
class CreativeProjectImageAnswer extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'creative_project_image_answer';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('creative_project_id, question_id, uid, media_file_rate_1, media_file_rate_2, media_file_rate_3, media_file_rate_4, media_file_rate_5, update_date', 'required'),
			array('creative_project_id, question_id', 'numerical', 'integerOnly'=>true),
			array('uid', 'length', 'max'=>16),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('creative_project_id, question_id, uid, media_file_rate_1, media_file_rate_2, media_file_rate_3, media_file_rate_4, media_file_rate_5, update_date', 'safe', 'on'=>'search'),
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
			'creativeProject' => array(self::BELONGS_TO, 'CreativeProjectAttributeQuestion', 'creative_project_id'),
			'question' => array(self::BELONGS_TO, 'CreativeProjectAttributeQuestion', 'question_id'),
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
			'uid' => 'Uid',
			'media_file_rate_1' => 'Media File Rate 1',
			'media_file_rate_2' => 'Media File Rate 2',
			'media_file_rate_3' => 'Media File Rate 3',
			'media_file_rate_4' => 'Media File Rate 4',
			'media_file_rate_5' => 'Media File Rate 5',
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
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('creative_project_id',$this->creative_project_id);
		$criteria->compare('question_id',$this->question_id);
		$criteria->compare('uid',$this->uid,true);
		$criteria->compare('media_file_rate_1',$this->media_file_rate_1,true);
		$criteria->compare('media_file_rate_2',$this->media_file_rate_2,true);
		$criteria->compare('media_file_rate_3',$this->media_file_rate_3,true);
		$criteria->compare('media_file_rate_4',$this->media_file_rate_4,true);
		$criteria->compare('media_file_rate_5',$this->media_file_rate_5,true);
		$criteria->compare('update_date',$this->update_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CreativeProjectImageAnswer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
