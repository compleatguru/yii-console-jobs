<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BaseModel
 *
 * @author David
 */
class BaseModel extends CActiveRecord {

    public function behaviors() {
        return array(
            'CTimestampBehavior' => array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => null,
                'updateAttribute' => 'update_date',
                'setUpdateOnCreate' => true,
                'timestampExpression' => new CDbExpression("now()"),
            )
        );
    }
    
}
