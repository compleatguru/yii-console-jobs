<?php
/* @var $this UserController */
/* @var $model MCreativeUser */

$this->breadcrumbs=array(
	'Update Profile',
);

$this->menu=array(
	array('label'=>'List MCreativeUser', 'url'=>array('index')),
	array('label'=>'Create MCreativeUser', 'url'=>array('create')),
	array('label'=>'View MCreativeUser', 'url'=>array('view', 'id'=>$model->creative_user_id)),
	array('label'=>'Manage MCreativeUser', 'url'=>array('admin')),
);
?>

<h1>Update User # <?php echo $model->creative_user_id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>