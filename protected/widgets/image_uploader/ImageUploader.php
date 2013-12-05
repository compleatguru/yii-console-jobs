<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ImageUploader
 *
 * @author David
 */
class ImageUploader extends CWidget {
    public $model;
    public $attribute;

    public $previewId;
    public $previewWidth='320';
    public $previewHeight='240';

    public function init(){
        $cs = Yii::app()->clientScript;
        if(empty($cs->scriptMap['filesize.js']))
            $cs->scriptMap['filesize.js']=Yii::app()->baseUrl.'/js/filesize.min.js';

        $this->configPreview();        
    }

    private function configPreview(){
        if(empty($this->previewId)){
            if(!empty($this->model) && !empty($this->attribute)){
                $this->previewId = 'preview'.get_class($this->model).'_'.$this->attribute;
            }
        }
    }

    public function run(){
        $this->render('index', array('model'=>$this->model,'attribute'=>$this->attribute,'previewId'=>$this->previewId,'previewWidth'=>$this->previewWidth,'previewHeight'=>$this->previewHeight));
    }
}
