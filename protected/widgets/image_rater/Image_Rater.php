<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Image_Rater
 *
 * @author David
 */
class Image_Rater extends CWidget {

    public $model, $attribute, $name;
    public $options;
    public $htmlOptions;
    private $_assetsUrl;

    const WIDGET_CSS_CLASS = 'chosen-select';

    public function getAssetsUrl() {
        if ($this->_assetsUrl === null)
            $this->_assetsUrl = Yii::app()->getAssetManager()->publish(
                    Yii::getPathOfAlias('application.widgets.image_rater.assets'));
        return $this->_assetsUrl;
    }

    public function init() {
        if (empty($this->name)) {
            if (empty($this->model) || empty($this->attribute))
                throw new CException('Missing model or attribute');
        }
        elseif (!empty($this->name) && (!empty($this->model) || !empty($this->attribute)))
            throw new CException('Either use a model->attribute or a name');

        if (!empty($this->name)) {
            $this->htmlOptions['id']= $this->name;
        } elseif (!empty($model) && !empty($attribute)) {
            /* @var $model CActiveRecord */
            CHtml::resolveNameID($this->model, $this->attribute, $this->htmlOptions);
        }
        $this->options['id']=$this->htmlOptions['id'];
    }

    public function run() {        
        if(empty($this->htmlOptions['id'])) throw new CException('missing ID');

        $id = $this->htmlOptions['id'];
        $cs = Yii::app()->clientScript;
        $cs->registerCssFile($this->assetsUrl . '/css/image_rater.css')
                ->registerScriptFile($this->assetsUrl . '/js/image_rater.js',  CClientScript::POS_HEAD)
                ->registerScript($id.'_image_rater_init','$("#'.$id.'").image_rater('.CJavaScript::encode($this->options).');',  CClientScript::POS_END);
        $this->render('index', array('model' => $this->model, 'attribute' => $this->attribute, 'name' => $this->name, 'htmlOptions' => $this->htmlOptions));
    }

}
