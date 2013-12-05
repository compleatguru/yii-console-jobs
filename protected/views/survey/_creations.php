<?php
/* @var $dataProvider CDataProvider */
$ct = new CodeTable();
$widget = $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'creation-grid',
    'dataProvider' => $dataProvider,
    'columns' => array(
        array(
//            'header' => 'Project Name',
            'class' => 'CCheckBoxColumn',
            'selectableRows'=>$dataProvider->itemCount,
//            'name'=>'creative_project_id',
            'id'=>'creative_project_id',
        ),
        array('header' => 'Project Name',
            'class' => 'CLinkColumn',
            'labelExpression' => '$data->creative_project_id',
            'urlExpression' => 'Yii::app()->createUrl("survey/editsurvey",array("id"=>$data->creative_project_id))',
        ),
        'launch_date',
        'closed_date',
        array(
            'name' => 'project_status',
            'value' => '$data->projectStatus($data->project_status)'
        ),
        'country'
    ),
    'cssFile' => Yii::app()->baseUrl . '/css/user-dashboard-grid.css',
));

$this->renderPartial('_creation_buttons',array('widget'=>$widget));
?>
<div class="clear"></div>