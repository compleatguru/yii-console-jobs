<?php

/**
 * Description of JuiDateTimepicker
 *
 * example: $this->widget('ext.jui.jQuery-Timepicker-Addon.JuiDateTimepicker');
 *
 * @author David
 */
class JuiDateTimepicker extends CWidget {

    /**
     * Options for the <SELECT> element
     * @var array
     */
    public $options;    
    public $name;
    public $model;
    public $attribute;
    public $htmlOptions = array();

    private $_assetsUrl;

    const WIDGET_CSS_CLASS = 'datetimepicker';

    public function getAssetsUrl() {
        if ($this->_assetsUrl === null)
            $this->_assetsUrl = Yii::app()->getAssetManager()->publish(
                    Yii::getPathOfAlias('ext.jui.jQuery-Timepicker-Addon.assets'));
        return $this->_assetsUrl;
    }

    public function run() {
        $cs = Yii::app()->clientScript;
        $cs->registerCssFile($this->assetsUrl . '/css/jquery-ui-timepicker-addon.min.css')
                ->registerScriptFile($this->assetsUrl . '/js/jquery-ui-timepicker-addon.min.js',  CClientScript::POS_END)
                ->registerScriptFile($this->assetsUrl.'/js/jquery-ui-sliderAccess.js',  CClientScript::POS_END);
               

        if(empty($this->name) && empty($this->model) && empty($this->attribute)) throw new CException('missing $name, $model or $attribute');
        
        if(isset($this->htmlOptions['class'])){
            $CSSclass = $this->htmlOptions['class'];
            if(stripos($CSSclass,self::WIDGET_CSS_CLASS) === false)
            $this->htmlOptions['class'] = $CSSclass.' '.self::WIDGET_CSS_CLASS;
        }
        else $this->htmlOptions = array_merge ($this->htmlOptions, array('class'=>self::WIDGET_CSS_CLASS));

        if($this->name){        
        echo CHtml::textField($this->name, '', $this->htmlOptions);
        }
        else{
            echo CHtml::activeTextField($this->model, $this->attribute, $this->htmlOptions);
        }

        if(isset($this->htmlOptions['id'])){
            $id = $this->htmlOptions['id'];
            $cs->registerScript('_'.self::WIDGET_CSS_CLASS.'_'.$id.'_init', "$('#".$id."').datetimepicker();", CClientScript::POS_END);
        }else{
            $cs->registerScript('_'.self::WIDGET_CSS_CLASS.'_init', "$('.".self::WIDGET_CSS_CLASS."').datetimepicker();", CClientScript::POS_END);
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
