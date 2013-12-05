<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SurveyTargetingForm
 *
 * @author David
 */
class SurveyTargetingForm extends CFormModel {

    public $creative_project_id;
    public $country = array();
    public $language = array();
    public $title = array();
    public $prefecture;
    public $city;
    public $prefecture_city = array();
    public $gender;
    public $marital_status;
    public $age_min;
    public $age_max;
    public $sample;
    private $creativeProjectCountryLanguage = array();
    private $country_prefecture_city;

    /**
     * Declares the validation rules.
     */
    public function rules() {
        return array(
            // required attributes
            array('creative_project_id,prefecture_city', 'required', 'on' => 'update'),
            array('prefecture_city','validatePrefectureCity'),
            array('country, language, title, prefecture, city, prefecture_city, gender, marital_status, age_min, age_max, sample', 'safe'),
            array('age_min', 'compare', 'compareAttribute' => 'age_max', 'operator' => '<='),
            array('age_max', 'compare', 'compareAttribute' => 'age_min', 'operator' => '>='),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels() {
        return array(
            'country' => 'Country',
            'language' => 'Language',
            'title' => 'Title',
            'prefecture' => 'Prefecture',
            'city' => 'City',
            'prefecture_city' => 'Prefecture / Region',
            'gender' => 'Gender',
            'marital_status' => 'Marital Status',
            'age_min' => 'Age (Min)',
            'age_max' => 'Age (Max)',
            'sample' => 'Target Sample Size',
        );
    }

    public function init($projectCountryLanguage = null, $projectTargetingCondition = null) {
        if (!empty($projectCountryLanguage)) {
            $this->creativeProjectCountryLanguage = $projectCountryLanguage;
            $this->initProjectId();
            $this->initCountryLanguage();
        }
        if (!empty($projectTargetingCondition)) {
            $this->initTargetingCondition($projectTargetingCondition);
        }
        parent::init();
    }

    private function initProjectId() {
        if (is_array($this->creativeProjectCountryLanguage) && !empty($this->creativeProjectCountryLanguage)) {
            $this->creative_project_id = $this->creativeProjectCountryLanguage[0]['creative_project_id'];
        }
    }

    private function initCountryLanguage() {
        if (is_array($this->creativeProjectCountryLanguage) && !empty($this->creativeProjectCountryLanguage)) {
            foreach ($this->creativeProjectCountryLanguage as $model) {
                /* @var $model CreativeProjectCountryLanguage */
                $languageid = $model->country_id . $model->language_id;
                $this->country[] = $model->country_id;
                $this->title[$languageid] = $model->title;
                $this->language[] = $languageid;
            }
        }
        Yii::log('Running initCountryLanguage:' . print_r($this->title, true) . print_r($this->language, true));
    }

    private function initTargetingCondition($targetingCondition) {
        if (is_array($targetingCondition) && !empty($targetingCondition)) {
            $model = new CreativeProjectTargetCondition;
            $attributes = $model->getCategoryList();
            Yii::log('Targeting Model' . print_r($targetingCondition, true));
            foreach ($attributes as $attribute) {
                if (empty($targetingCondition))
                    break;
                else {
                    $criteria = array('category' => $attribute);
                    $result = $this->getModel($targetingCondition, $criteria);
                    if (is_array($result)) {
                        $modelId = key($result);
                        $model = $result[$modelId];
                        unset($targetingCondition[$modelId]);
                    } else
                        $model = $result;

                    if (!$model->isNewRecord) {
                        switch ($attribute) {
                            case 'city': $attribute = 'prefecture_city';
                            case 'gender':
                            case 'marital_status':
                                if (stripos($model->value, ',') !== false)
                                    $this->$attribute = explode(',', $model->value);
                                else
                                    $this->$attribute = array($model->value);
                                break;
                            case 'age_min':
                            case 'age_max':
                            case 'sample':
                                $this->$attribute = $model->value;
                        }
                        Yii::log('Targeting Attribute ' . $attribute . ':' . print_r($this->$attribute, true));
                    }
                }
            }
        }
    }

    public function saveTargetingCondition($currentModel = null) {
        $ct = new CodeTable;

        $model = new CreativeProjectTargetCondition;
        $attributes = $model->CategoryList;


// Run through all attributes to save/update/delete
        foreach ($attributes as $attribute) {
            // Required for mapping data
            switch ($attribute) {
                case 'prefecture':
                    $ct = new CodeTable();
                    foreach ($this->prefecture_city as $cityId) {
                        $result = $ct->getPrefectureId($this->country, $cityId);
                        $this->prefecture[] = $result;
                    }
                    break;
                case 'city':
                    $this->$attribute = $this->prefecture_city;
                    break;
                case 'country_prefecture_city':
                    $this->$attribute = $ct->getCountryPrefectureCity($this->country, $this->prefecture_city);
                    break;
            }

            if ($attribute != 'country_prefecture_city')
                $value = $this->convertCSV($attribute);
            else
                $value = json_encode($this->$attribute);


            Yii::log('Attribute ' . $attribute);
            Yii::log('Attribute Value ' . print_r($value, true));

            // Get Model
            if (empty($currentModel)) {
                $model = new CreativeProjectTargetCondition;
                $model->creative_project_id = $this->creative_project_id;
                $model->category = $attribute;
            } else {
                $result = $this->getModel($currentModel, array('category' => $attribute, 'creative_project_id' => $this->creative_project_id));
                // set model to result
                if (is_array($result)) {
                    $modelId = key($result);
                    $model = $result[$modelId];
                } else {
                    $modelId = null;
                    $model = $result;
                }
            }

            // insert / update
            if (!empty($value)) {
                if ($model->value != $value) {
                    $model->value = $value;
                    Yii::log('Set CreativeProjectTargetCondition Attributes' . print_r($model->attributes, true));
                    if ($model->validate()) {
                        Yii::log('Saving CreativeProjectTargetCondition' . print_r($model->attributes, true));
                        $model->save();
                        if (!is_null($modelId))
                            unset($currentModel[$modelId]);
                    } else
                        throw new CException('Unable to save model. ' . print_r($model->getErrors(), true));
                }
            }
            elseif (empty($value) && !$model->isNewRecord) {
                // delete
                Yii::log('Delete Empty CreativeProjectTargetCondition' . print_r($model->attributes, true) . ' New Value:' . print_r($this->$attribute, true));
                $model->delete();
            }
        }
    }

    private function convertCSV($attribute) {
        $value = $this->$attribute;
        if (is_array($value)) {
            return implode(',', $value);
        } elseif (is_string($value))
            return $value;
        elseif (is_null($value))
            return '';
        else
            throw new CException('Unable to convert value: ' . print_r($value) . ' for ' . $attribute);
    }

    private function getModel(array $modelList, array $criteria) {
        $filter = function($model) use ($criteria) {
            $valid = 0;
            foreach ($criteria as $attribute => $value) {
                if ($model->$attribute == $value) {
                    $valid++;
                }
            }
            return $valid == count($criteria);
        };
        $result = array_filter($modelList, $filter);
        Yii::log('getModel Result' . print_r($result, true));
        if (count($result) == 1) {
            return $result;
        } elseif (empty($result)) {
            reset($modelList);
            Yii::log('Get  Model ' . print_r($modelList, true));
            $className = get_class($modelList[key($modelList)]);
            Yii::log('New Model ' . $className);
            $m = new $className;
            $m->attributes = $criteria;
            $m->creative_project_id = $this->creative_project_id;
            Yii::log('Attributes' . print_r($m->attributes, true));
            return $m;
        }
    }

    public function saveProjectCountryLanguage($currentModel = null) {
        Yii::log('saveProjectCountryLanguage:' . print_r($this->title, true) . print_r($currentModel, true));

        if (empty($this->country)) {
            $this->title = array();
            $this->language = array();

            if (is_array($currentModel))
                foreach ($currentModel as $model) {
                    $model->delete();
                }
            $currentModel = array();
        }

        if (is_array($this->title)) {
// Update / Delete Language
            foreach ($this->title as $langid => $title) {

                $criteria = array('country_id' => substr($langid, 0, 2),
                    'language_id' => substr($langid, 2),
                    'creative_project_id' => $this->creative_project_id
                );

                if (!empty($currentModel))
                    $result = $this->getModel($currentModel, $criteria);
                else {
                    $result = new CreativeProjectCountryLanguage;
                    $result->attributes = $criteria;
                }

                if (is_array($result)) {
                    $modelId = key($result);
                    $model = $result[$modelId];
                } else {
                    $modelId = null;
                    $model = $result;
                }

//                Yii::log('Language Model:' . print_r($model->attributes, true));
                if ($model->title != $title) {
                    $model->title = $title;

                    if ($model->validate()) {
                        Yii::log('Saving CreativeProjectCountryLanguage' . print_r($model->attributes, true));
                        $model->save();
                        if (!is_null($modelId))
                            unset($currentModel[$modelId]);
                    } else
                        throw new CException('Unable to save model. ' . print_r($model->getErrors(), true));
                }
                elseif ($model->title == $title && !is_null($modelId)) { // no change but record found.
                    Yii::log('Unset index ' . $modelId . ' on ' . print_r($currentModel, true));
                    unset($currentModel[$modelId]);
                }
            }

            if (!empty($currentModel)) {
                foreach ($currentModel as $model) {
                    Yii::log('Delete Model:' . print_r($model->attributes, true));
                    $model->delete();
                }
            }
        }
    }

    public function validatePrefectureCity($attribute,$params){
        if($attribute != 'prefecture_city') return;

        $ct = new CodeTable;
        $country_prefecture_city = $ct->getCountryPrefectureCity($this->country, $this->prefecture_city);

        foreach($this->country as $country){
            if(!isset($country_prefecture_city[$country])) $this->addError ($attribute, 'Missing prefecture and city of '.$ct->country($country));            
        }
    }

}
