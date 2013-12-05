<?php
/* @var $this UserController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Mcreative Users',
);

$this->menu=array(
	array('label'=>'Create MCreativeUser', 'url'=>array('create')),
	array('label'=>'Manage MCreativeUser', 'url'=>array('admin')),
);
?>

<h1>Mcreative Users</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
