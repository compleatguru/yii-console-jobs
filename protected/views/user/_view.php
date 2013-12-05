<?php
/* @var $this UserController */
/* @var $data MCreativeUser */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('creative_user_id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->creative_user_id), array('view', 'id'=>$data->creative_user_id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('email')); ?>:</b>
	<?php echo CHtml::encode($data->email); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('password')); ?>:</b>
	<?php echo CHtml::encode($data->password); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('first_name')); ?>:</b>
	<?php echo CHtml::encode($data->first_name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('last_name')); ?>:</b>
	<?php echo CHtml::encode($data->last_name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('job_title')); ?>:</b>
	<?php echo CHtml::encode($data->job_title); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('user_country')); ?>:</b>
	<?php echo CHtml::encode($data->user_country); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('company_name')); ?>:</b>
	<?php echo CHtml::encode($data->company_name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('company_address')); ?>:</b>
	<?php echo CHtml::encode($data->company_address); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('contact_number')); ?>:</b>
	<?php echo CHtml::encode($data->contact_number); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('industry')); ?>:</b>
	<?php echo CHtml::encode($data->industry); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('account_status')); ?>:</b>
	<?php echo CHtml::encode($data->account_status); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('account_create_date')); ?>:</b>
	<?php echo CHtml::encode($data->account_create_date); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('account_delete_date')); ?>:</b>
	<?php echo CHtml::encode($data->account_delete_date); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('last_login_date')); ?>:</b>
	<?php echo CHtml::encode($data->last_login_date); ?>
	<br />

	*/ ?>

</div>