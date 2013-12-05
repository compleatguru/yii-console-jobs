<?php /* @var $this Controller */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="language" content="en" />

        <!-- blueprint CSS framework -->
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
        <!--[if lt IE 8]>
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
        <![endif]-->

        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
        <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    </head>
    <body>
        <div class="container" id="page">
            <div class="header">
                <?php echo CHtml::image('images/LogoTop.png') ?>
                <span id="login_user_box">
                    Welcome, <?php echo Yii::app()->user->name ?> |
                    <?php echo CHtml::link('Profile',array('user/update')) ?> |
                    <?php echo CHtml::link('Logout', array('site/logout')) ?>
                </span>            
            </div>
            <?php if (isset($this->breadcrumbs) && !empty($this->breadcrumbs)): ?>
                <?php
                $this->widget('zii.widgets.CBreadcrumbs', array(
                    'links' => $this->breadcrumbs,
                    'htmlOptions' => array('style' => 'background-color:#C9E0ED;height:20px;padding-left:5px;'),
                ));
                ?><!-- breadcrumbs -->                
            <?php else: ?>
                <div class="breadcrumbs" style="background-color:#C9E0ED;height:20px;"></div>
            <?php endif ?>
            <?php
            foreach (Yii::app()->user->getFlashes() as $key => $message) {
                echo '<div class="flash-' . $key . '">' . $message . "</div>\n";
            }
            ?>
            <?php echo $content ?>
        </div>
    </body>
</html>
