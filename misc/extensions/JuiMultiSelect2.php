<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of JuiMultiSelect2
 *
 * @author David
 */
class JuiMultiSelect2 extends CWidget {

    /**
     * Options for the <SELECT> element
     * @var array
     */
    public $options;
    public $name;
    public $htmlOptions = array();

    private $_assetsUrl;

    public function getAssetsUrl() {
        if ($this->_assetsUrl === null)
            $this->_assetsUrl = Yii::app()->getAssetManager()->publish(
                    Yii::getPathOfAlias('ext.jui.assets.juimultiselect2'));
        return $this->_assetsUrl;
    }

    public function run() {
        $cs = Yii::app()->clientScript;
        $cs->registerCssFile('jquery-ui.css')
                ->registerScriptFile('jquery.min.js')
                ->registerScriptFile('jquery-ui.min.js')
                ->registerCssFile($this->assetsUrl . '/css/jquery-multiselect-2.0.css')
                ->registerScriptFile($this->assetsUrl . '/js/jquery-multiselect-2.0.js')                
                ->registerScript('_juimultiselect2_init', "var defaultOptions = {
                    //availableListPosition: 'bottom',
                    moveEffect: 'blind',
                    moveEffectOptions: {direction:'vertical'},
                    moveEffectSpeed: 'fast'
                };
", CClientScript::POS_END);

        if(empty($this->name)) throw new CException('missing $name');

        if(!isset($this->htmlOptions['id'])){
            $cs->registerScript('_juimultiselect2_boot', "$('.multiselect').multiselect();",  CClientScript::POS_END);
        }
        else{
            $id = $this->htmlOptions['id'];
            $cs->registerScript('_juimultiselect2#'.$id.'_boot', "$('#$id').multiselect();",  CClientScript::POS_END);
        }
        
        echo CHtml::dropDownList($this->name, false, $this->options, array_merge($this->htmlOptions, array('class' => 'multiselect','multiple'=>'multiple')));
        echo '<div class="clear"></div>';
    }

    public function init() {
        if (!is_array($this->options))
            $this->options = array();
    }

}
