<?php
/* @var $this SiteController */

$this->pageTitle = Yii::app()->name;
if(!Yii::app()->user->isGuest)
    $this->breadcrumbs = array(
        'Survey Management'=>Yii::app()->createUrl('survey/index'),
        'Default Home Page'
        );
?>
<div class="centerscreen">
    <div class="full">
        <div class="full">
            <?php echo CHtml::image('images/HomePageHero.png', 'AIP Survey Home Page', array('class' => 'center')) ?>
        </div>
        <div class="full home center">
            <div class="home about"><?php echo CHtml::link(CHtml::image('images/about.png', 'About', array('class' => 'center')),array('site/page','view'=>'about')) ?></div>
            <div class="home register"><?php echo CHtml::link(CHtml::image('images/register.png', 'Register', array('class' => 'center')),array('user/register')) ?></div>
            <div class="home login"><?php echo CHtml::link(CHtml::image('images/login.png', 'Login', array('class' => 'center')),array('site/login')) ?></div>
        </div>
    </div>
</div>
<div class="clear"></div>