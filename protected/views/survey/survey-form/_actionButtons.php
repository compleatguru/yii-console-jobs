<?php

if(empty($_POST['CreativeProject']['creative_project_id'])){
    $isNew = true;
}

$actions = array(
    'Reset' => array(
        'htmlOptions'=>array('style'=>'background:red;','class'=>'survey-action'),
        'action'=>'',
        'options'=>array('disabled'=> !$isNew,),
        ),
    'Preview' => array(
        'htmlOptions'=>array('style'=>'background:blue;','class'=>'survey-action'),
        'action'=>'',
        'options'=>array('disabled'=> !$isNew,),
        ),
    'Setup' => array(
        'htmlOptions'=>array('style'=>'background:blue;','class'=>'survey-action'),
        'action'=>'',
        'options'=>array('disabled'=> !$isNew,),
        ),
    'Save' => array(
        'htmlOptions'=>array('style'=>'background:blue;','class'=>'survey-action'),
        'action'=>'',        
        'options'=>array('disabled'=> !$isNew,),
        ),
    'Launch' => array(
        'htmlOptions'=>array('style'=>'background:green;','class'=>'survey-action'),
        'action'=>'',
        'options'=>array('disabled'=> $isNew,),
        ),
);

switch($page){
    case 'attributesassociation':
    case 'imagecomparison':
        unset($actions['Setup']);
        break;
}
?>
<div class="full center" style="padding-top:2.5em;">
<?php
foreach ($actions as $action => $properties) {
    $this->widget('zii.widgets.jui.CJuiButton', array(
        'buttonType' => 'submit',
        'name' => $action,
        'caption' => $action,
        'options'=>$properties['options'],
//        'onclick' => new CJavaScriptExpression('function(){alert("'.$action.' button clicked"); this.blur(); return false;}'),
        'htmlOptions'=>$properties['htmlOptions']
    ));
}
?>
</div>