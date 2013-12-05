<?php
/* @var $form CActiveForm */
/* @var $creativeProjectQuestionMedia CreativeProjectQuestionMedia */
/* @var $creativeProjectQuestion CreativeProjectQuestion */
/* @var $creativeProjectQuestionByLanguage CreativeProjectQuestionByLanguage */
/* @var $creativeProjectQuestionRating CreativeProjectQuestionRating */
/* @var $imageRatingForm ImageRatingForm */

$cs = Yii::app()->clientScript;
$cs->registerCssFile(Yii::app()->baseUrl . '/css/imagecompare.css')
        ->registerScriptFile('jquery.min.js', CClientScript::POS_HEAD)
        ->registerScriptFile('jquery-ui.min.js', CClientScript::POS_HEAD)
        ->registerScriptFile('knockout.js', CClientScript::POS_HEAD)
?>
<div id="imagecompare">
    <h2>Layout Type</h2>
    <div class="row">
        <?php echo CHtml::label('Layout', 'layout') ?>
        <?php
        echo $form->radioButtonList($creativeProjectQuestionRating, 'media_layout', array(
            0 => 'Horizontal',
            1 => 'Vertical'), array('separator' => '&nbsp;', 'data-bind' => 'checked: layout',))
        ?>
    </div>
    <div class="row">
        <?php echo CHtml::label('Type', 'rating_type') ?>
        <?php
        echo $form->radioButtonList($creativeProjectQuestionRating, 'rating_type', array(
            'Slider',
//                'Heatmap'
                ), array('separator' => '&nbsp;'))
        ?>
    </div>
    <div class="row">
        <?php
        echo CHtml::radioButtonList('question_type', null, array(
            'Question',
//                'Heatmap'
        ))
        ?>
        Select tag with question with radio button
    </div>
    <div class="row span-10">
        <div style="display:inline;float:left;width:33%;">
            <?php echo $form->textField($creativeProjectQuestionRating, 'min_value', array('placeholder' => 'min value', 'size' => 7)) ?>
        </div>
        <div style="display:inline;float:left;width:33%;margin-left:0%">
            <?php echo $form->dropDownList($creativeProjectQuestionRating, 'measurement_unit', array('%')) ?>
        </div>
        <div style="display:inline;float:left;width:33%;">
            <?php echo $form->textField($creativeProjectQuestionRating, 'max_value', array('placeholder' => 'max value', 'size' => 7)) ?>
        </div>        
    </div>
    <div class="clear"></div>
    <div class="row">
        <div data-bind="foreach: Language_Title">
            <br><span data-bind="text: name"></span>
            <br><input class="text-input" type="text" data-bind="attr: {name: id, placeholder: 'Enter Question for ' +  name, value:value}" size="100" required/>
        </div>
    </div>
    <div class="row">
        <div id="Images" title="Image Compare Section">            
            <?php if (empty($creativeProjectQuestionMedia)): ?>
                <?php $this->widget('application.widgets.image_rater.Image_Rater', array('name' => 'rater1')) ?>
                <div data-bind="css: vsLayout" style="padding:5px;text-align: center;margin:0 auto;">vs</div>
                <?php $this->widget('application.widgets.image_rater.Image_Rater', array('name' => 'rater2')) ?>
            <?php else: ?>
                <?php foreach ($creativeProjectQuestionMedia as $index => $media): ?>
                    <?php $this->widget('application.widgets.image_rater.Image_Rater', array('name' => 'rater' . ($index + 1), 'options' => array('imageSrc' => $media->path))) ?>
                    <?php if ($index + 1 < count($creativeProjectQuestionMedia)): ?>
                        <div data-bind="css: vsLayout" style="padding:5px;text-align: center;margin:0 auto;">vs</div>
                    <?php endif ?>
                <?php endforeach ?>
            <?php endif ?>
        </div>
        <div class="clear"></div>
    </div>
    <div class="row"
         <div data-bind="visible: RaterCounter() < 5"><?php
                 $this->widget('zii.widgets.jui.CJuiButton', array(
                     'buttonType' => 'button',
                     'name' => 'Add 1 more',
                     'caption' => 'Add 1 more',
                     'htmlOptions' => array('data-bind' => 'click: addImageRater'),
                         )
                 )
                 ?>
        </div>
    </div>
    <div class="clear"></div>
    <script>
        // TODO: remove debug
        function add_ImageRater() {
            console.log("add image rater");
            id = "rater" + RaterCounter();
            $("#Images")
                    .append(
                            $("<div></div>")
                            .attr("data-bind", "css: vsLayout")
                            .css({"padding": "5px", "text-align": "center", "margin": "0 auto"})
                            .addClass("vs vertical")
                            .append("vs")
                            )
                    .append(
                            $("<div></div>")
                            .attr("id", id)
                            );
            $("div#" + id).image_rater({id: id});
            console.log("end image rater");
        }

        var myViewModel = function() {
            layout = ko.observable("<?php echo $creativeProjectQuestionRating->media_layout ?>");
<?php Yii::log('Questions by Language: '.print_r($creativeProjectQuestionByLanguage,true));
            $ct = new CodeTable();
            $storeid = array();
            $result = array();
            foreach($creativeProjectQuestionByLanguage as $model){
                if(!in_array($model->language_id, $storeid)){
                $result[] = array(
                    'id'=>$model->language_id,
                    'name'=>$ct->getLanguageDescription($model->country_id.$model->language_id),
                    'value'=>$model->question_text
                    );
                $storeid[]=$model->language_id;
                }
            }
        ?>
            Language_Title = <?php echo !empty($result) ? CJavaScript::encode($result) : '[]'?>;


            RaterCounter = ko.observable(<?php echo empty($creativeProjectQuestionMedia) ? 2 : count($creativeProjectQuestionMedia) ?>);

            addImageRater = function() {
                RaterCounter(RaterCounter() + 1);
                add_ImageRater();
            };

            compareLayout = ko.computed(function() {
                switch (parseInt(layout())) {
                    case 0:
                        //                console.log("horizontal");
                        return "imagecompare horizontal";
                    case 1:
                        //                console.log("vertical");
                        return "imagecompare vertical";
                }
            }, this);

            vsLayout = ko.computed(function() {
                switch (parseInt(layout())) {
                    case 0:
                        return "horizontal";
                    case 1:
                        return "vs vertical";
                }
            }, this);
        };
        var myModel = new myViewModel;
        ko.applyBindings(myModel, document.getElementById('imagecompare'));
    </script>