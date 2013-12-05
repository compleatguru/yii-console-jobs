<?php

/**
 * This is the model class for table "creative_project_target_condition".
 *
 * The followings are the available columns in table 'creative_project_target_condition':
 * @property integer $creative_project_id
 * @property string $category
 * @property string $value
 * @property string $update_date
 *
 * The followings are the available model relations:
 * @property CreativeProject $creativeProject
 * @property MTargetCondition $category0
 */
class CreativeProjectTargetCondition extends BaseModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'creative_project_target_condition';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('creative_project_id, category, value', 'required'),
			array('creative_project_id', 'numerical', 'integerOnly'=>true),
			array('category', 'length', 'max'=>32),
			array('value', 'length', 'max'=>128),
      array('category','safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('creative_project_id, category, value', 'safe', 'on'=>'search'),
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
			'category0' => array(self::BELONGS_TO, 'MTargetCondition', 'category'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'creative_project_id' => 'Creative Project',
			'category' => 'Category',
			'value' => 'Value',
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
		$criteria->compare('category',$this->category,true);
		$criteria->compare('value',$this->value,true);
		$criteria->compare('update_date',$this->update_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CreativeProjectTargetCondition the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

   public function getSurveyProjectTargetCondition(){
       $criteria = new CDbCriteria();
       $criteria->addInCondition('category', $this->getCategoryList());
       $criteria->addColumnCondition(array('creative_project_id',$this->creative_project_id));
       return $this->findAll();
   }

   public function getCategoryList(){ return array('age_max','age_min','city','marital_status','prefecture','sample','gender'); }
}
