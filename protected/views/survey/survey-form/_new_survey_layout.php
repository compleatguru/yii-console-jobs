<div class="form">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
//        'action' => Yii::app()->createUrl($this->id . '/' . $this->action->id,$this->actionParams),
        'method' => 'post',
        'id' => 'creative-project-creativeprojectform-form',
        // Please note: When you enable ajax validation, make sure the corresponding
        // controller action is handling ajax validation correctly.
        // See class documentation of CActiveForm for details on this,
        // you need to use the performAjaxValidation()-method described there.
        'enableAjaxValidation' => false,
        'htmlOptions' => array('enctype' => 'multipart/form-data',),
    ));
    ?>
    <?php echo!empty($surveyTitle) ? CHtml::tag('h1', array(), $surveyTitle) : ''; ?>
    <?php
    $this->renderPartial('survey-form/_' . $page, array(
        'creativeProject' => $creativeProject,
        'SurveyTargetingForm' => $SurveyTargetingForm,
        'creativeProjectQuestion' => $creativeProjectQuestion,
        'creativeProjectQuestionMedia' => $creativeProjectQuestionMedia,
        'creativeProjectQuestionRating' => $creativeProjectQuestionRating,
        'creativeProjectQuestionByLanguage' => $creativeProjectQuestionByLanguage,
        'form' => $form))
    ?>
    <?php $this->renderPartial('survey-form/_actionButtons', array('page' => $page)); ?>
<?php $this->endWidget(); ?>
</div>