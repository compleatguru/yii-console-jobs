<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle = Yii::app()->name . ' - Forget Password';
$this->breadcrumbs = array(
    'Forget Password',
);
?>
<div class="center">
    <div class="full">
        <?php echo CHtml::image('images/HomePageHero.png', 'AIP Survey Home Page', array('class' => 'center')) ?>
    </div>
    <h1 class="center">Forget Password</h1>

    <p class="">Please fill out your login username:</p>

    <div class="form">
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'login-form',
            'enableClientValidation' => false,
            'clientOptions' => array(
                'validateOnSubmit' => false,
            ),
        ));
        ?>

        <p class="note">Fields with <span class="required">*</span> are required.</p>

        <div class="row">
            <?php echo $form->labelEx($model, 'username'); ?>
            <?php echo $form->textField($model, 'username'); ?>
            <?php echo $form->error($model, 'username'); ?>
        </div>

        <div class="row buttons">
            <?php echo CHtml::submitButton('submit'); ?>
        </div>

        <?php $this->endWidget(); ?>
    </div><!-- form -->
</div>