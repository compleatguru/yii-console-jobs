<?php
 /* @var $form CActiveForm */
 /* @var  $creativeProjectQuestion CreativeProjectQuestion */
 /* @var  $creativeProjectQuestionMedia CreativeProjectQuestionMedia */


$cs = Yii::app()->clientScript;
$cs->registerScriptFile('filesize.js');
?>
<div id="_attribute_association_">
    <div class="row">        
        <div>
            <?php $this->widget('application.widgets.image_uploader.ImageUploader',array('model'=>$creativeProjectQuestionMedia,'attribute'=>'path')) ?>
        </div>
    </div>
    <div>

    </div>    
</div>
<?php echo $form->dropDownList($creativeProjectQuestion, 'question_type_id' ,array('1'=>'Please select all attributes that can be associated to the media above.'), array('prompt' => 'Please select a question'))?>
<div class="row">
<h2>Attributes</h2> (Available options to surveyee)
<div>
<?php echo CHtml::checkBoxList('attributes', null, array('Accessible', 'Aspirational'), array('disabled'=>'disabled')) ?>
</div>
</div>