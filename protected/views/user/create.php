<?php
/* @var $this UserController */
/* @var $model MCreativeUser */

$this->breadcrumbs=array(
	'Mcreative Users'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List MCreativeUser', 'url'=>array('index')),
	array('label'=>'Manage MCreativeUser', 'url'=>array('admin')),
);
?>

<h1>Registration</h1>

<?php $this->renderPartial('_registerform', array('model'=>$model)); ?>