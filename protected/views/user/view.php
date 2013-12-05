<?php
/* @var $this UserController */
/* @var $model MCreativeUser */

$this->breadcrumbs=array(
	'Mcreative Users'=>array('index'),
	$model->creative_user_id,
);

$this->menu=array(
	array('label'=>'List MCreativeUser', 'url'=>array('index')),
	array('label'=>'Create MCreativeUser', 'url'=>array('create')),
	array('label'=>'Update MCreativeUser', 'url'=>array('update', 'id'=>$model->creative_user_id)),
	array('label'=>'Delete MCreativeUser', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->creative_user_id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage MCreativeUser', 'url'=>array('admin')),
);
?>

<h1>View MCreativeUser #<?php echo $model->creative_user_id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'creative_user_id',
		'email',
		'password',
		'first_name',
		'last_name',
		'job_title',
		'user_country',
		'company_name',
		'company_address',
		'contact_number',
		'industry',
		'account_status',
		'account_create_date',
		'account_delete_date',
		'last_login_date',
	),
)); ?>
