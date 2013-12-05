<?php
/* @var $this UserController */
/* @var $model MCreativeUser */

$this->breadcrumbs=array(
	'Mcreative Users'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List MCreativeUser', 'url'=>array('index')),
	array('label'=>'Create MCreativeUser', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#mcreative-user-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Mcreative Users</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'mcreative-user-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'creative_user_id',
		'email',
		'password',
		'first_name',
		'last_name',
		'job_title',
		/*
		'user_country',
		'company_name',
		'company_address',
		'contact_number',
		'industry',
		'account_status',
		'account_create_date',
		'account_delete_date',
		'last_login_date',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
