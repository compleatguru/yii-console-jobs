<?php

/**
 * This is the model class for table "m_category_attribute".
 *
 * The followings are the available columns in table 'm_category_attribute':
 * @property integer $attribute_id
 * @property integer $language_id
 * @property integer $category_id
 * @property string $attribute_text
 * @property boolean $display_flag
 *
 * The followings are the available model relations:
 * @property MCreativeCategory $category
 */
class MCategoryAttribute extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'm_category_attribute';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('language_id, category_id, display_flag', 'required'),
			array('language_id, category_id', 'numerical', 'integerOnly'=>true),
			array('attribute_text', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('attribute_id, language_id, category_id, attribute_text, display_flag', 'safe', 'on'=>'search'),
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
			'category' => array(self::BELONGS_TO, 'MCreativeCategory', 'category_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'attribute_id' => 'Attribute',
			'language_id' => 'Language',
			'category_id' => 'Category',
			'attribute_text' => 'Attribute Text',
			'display_flag' => 'Display Flag',
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

		$criteria->compare('attribute_id',$this->attribute_id);
		$criteria->compare('language_id',$this->language_id);
		$criteria->compare('category_id',$this->category_id);
		$criteria->compare('attribute_text',$this->attribute_text,true);
		$criteria->compare('display_flag',$this->display_flag);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MCategoryAttribute the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
