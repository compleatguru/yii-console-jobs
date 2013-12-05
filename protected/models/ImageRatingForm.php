<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ImageRatingForm
 *
 * @author David
 */
class ImageRatingForm extends CFormModel {
    public $creative_project_id;
    public $question_id;
    public $index;
    public $path;

    public $language_id;
    public $question_text;

    public $questions = array();

    public $images = array();

     /**
     * Declares the validation rules.
     */
    public function rules() {
        return array(
            // required attributes
//            array('creative_project_id,question_id,index,path', 'required'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels() {
        return array(
          
        );
    }
}
