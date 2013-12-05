<?php

/**
 * Description of JuiChosen
 *
 * @author David
 */
class JuiChosen extends CWidget {

    /**
     * Options for the <SELECT> element
     * @var array
     */
    public $options;
    public $data;
    public $name;
    public $model;
    public $attribute;
    public $htmlOptions = array();

    private $_assetsUrl;

    const WIDGET_CSS_CLASS = 'chosen-select';

    public function getAssetsUrl() {
        if ($this->_assetsUrl === null)
            $this->_assetsUrl = Yii::app()->getAssetManager()->publish(
                    Yii::getPathOfAlias('ext.jui.chosen.assets'));
        return $this->_assetsUrl;
    }

    public function run() {
        $cs = Yii::app()->clientScript;
        $cs
                ->registerCssFile($this->assetsUrl . '/css/chosen.css')
//                ->registerCssFile($this->assetsUrl . '/css/chosen.min.css')
                ->registerScriptFile($this->assetsUrl . '/js/chosen.jquery.min.js')
//                ->registerScriptFile($this->assetsUrl . '/js/chosen.jquery.js') // for dev
                ->registerScript('_chosen-select_init', "$('.".self::WIDGET_CSS_CLASS."').chosen();", CClientScript::POS_END);

        if(empty($this->name) && empty($this->model) && empty($this->attribute)) throw new CException('missing $name, $model or $attribute');
        
        if(isset($this->htmlOptions['class'])){
            $CSSclass = $this->htmlOptions['class'];
            if(stripos($CSSclass,self::WIDGET_CSS_CLASS) === false)
            $this->htmlOptions['class'] = $CSSclass.' '.self::WIDGET_CSS_CLASS;
        }
        else $this->htmlOptions = array_merge ($this->htmlOptions, array('class'=>self::WIDGET_CSS_CLASS));

        if($this->name){
        echo CHtml::dropDownList($this->name, false, $this->data, $this->htmlOptions);
        }
        else{
            echo CHtml::activeDropDownList($this->model, $this->attribute, $this->data, $this->htmlOptions);
        }
        echo '<div class="clear"></div>';
    }

    public function init() {
        if (!is_array($this->options))
            $this->options = array(
//                'no_results_text'=>"No record found"
            );
    }

}
