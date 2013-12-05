<?php
$cs = Yii::app()->clientScript;
//$cs->registerScriptFile('jquery.js')
//      ->registerScriptFile('jqueryui.js')
//      ->registerCssFile( 'jqueryui.css' );
?>
<div class="row">
    <h2>Target Sample Size</h2>
    <p id="sample_value"></p>
    <?php
    $SurveyTargetingForm->sample_min=1;
    $SurveyTargetingForm->sample_max=499;
    $this->widget('zii.widgets.jui.CJuiSliderInput', array(
        'model' => $SurveyTargetingForm,
        'attribute'=>'sample_min',
        'maxAttribute'=>'sample_max',
        'maxValue'=>500,
        // additional javascript options for the slider plugin
        'options' => array(
            'range' => true,
            'min' => 0,
            'max' => 500,
//            'step'=>10,
//            'stop'=>'js:function(event, ui) {
//                        console.log("writing");
//                        $("#sample_value").text("Between " + ui.values[0] + " And " + ui.values[1]);}'
        ),
        'htmlOptions' => array(
//            'style' => 'height:20px;',
        ),        
    ))
    ?>
</div>


<p>
  <label for="amount">Price range:</label>
  <input type="text" id="amount" style="border:0; color:#f6931f; font-weight:bold;">
</p>


<div id="slider-range"></div>
<script>
  $(function() {
    $( "#slider-range" ).slider({
      range: true,
      min: 0,
      max: 500,
      values: [ 75, 300 ],
      slide: function( event, ui ) {
        $( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
      }
    });
    $( "#amount" ).val( "$" + $( "#slider-range" ).slider( "values", 0 ) +
      " - $" + $( "#slider-range" ).slider( "values", 1 ) );
  });
  </script>