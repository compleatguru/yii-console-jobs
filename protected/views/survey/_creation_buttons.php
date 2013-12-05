<?php
$actions = array('Preview'=>'',
    'Setup'=>'',
    'Copy'=>'',
    'Delete'=>'function(){'
    .'if(!confirm("Are you sure you want to delete selected items?")) return false;'
    .'var checked = $("#creation-grid input:checked");'
    .'var data = {"action":"delete","creative_project_id[]":[]};'
    .'var items="";'
    .'$.each(checked,function(){ data["creative_project_id[]"].push($(this).val());});'
    .'console.log(data);'
    . '$("#'.$widget->id.'").yiiGridView("update",'
    . '{type:"POST",'
    .'data:data,'
    . 'success: function(data){'
    .'$("#'.$widget->id.'").yiiGridView("update");'
    . '}'
    . '});'
    . '}',
    'Launch'=>'',
    'Pause'=>'',
    'Close'=>'',
    'Statistics'=>'',
);
foreach($actions as $action=>$jsAction){
$this->widget('zii.widgets.jui.CJuiButton',array(
    'buttonType'=>'button',
    'name'=>'btn['.$action.']',
    'caption'=>$action,
    'onclick'=>new CJavaScriptExpression($jsAction),
//    'onclick'=>new CJavaScriptExpression('function(){alert("Save button clicked"); this.blur(); return false;}'),
));
}
?>
<script>

</script>
<?php /*
<script>
    jQuery('#panel-member-point-info-hk-grid').yiiGridView('update', {
		type: 'POST',
		url: jQuery(this).attr('href'),
		success: function(data) {
			jQuery('#panel-member-point-info-hk-grid').yiiGridView('update');
			afterDelete(th, true, data);
		},
		error: function(XHR) {
			return afterDelete(th, false, XHR);
		}
	});
</script>
 *
 */?>