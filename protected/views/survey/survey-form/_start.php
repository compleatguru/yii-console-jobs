<?php
/* @var $creativeProject CreativeProject */
/* @var $SurveyTargetingForm SurveyTargetingForm */
/* @var $form CActiveForm */

// setup knockout language list
$languageCountryList = array();
foreach ($SurveyTargetingForm->language as $languageid) {
    $country_id = substr($languageid, 0, 2);
    $languages = $this->actionLanguage($country_id, false);
    foreach ($languages as $l) {


        if (count($l['children']) == 1) {
            $languageCountryList[$country_id] = $l;
        } else {
            $languageCountryList[$country_id] = $l;
            $languageCountryList[$country_id]['children'] = array_values($l['children']);
        }
    }
}

$languageList = false;
foreach ($languageCountryList as $country) {
    $languageList[] = $country;
}
?>
<?php
$cs = Yii::app()->clientScript;
$cs->registerCss('_init_multi', "
    .multiselect {
                width: 450px;
                height: 210px;
            }
   .select2-input { min-width: 150px;}
.chosen-container { min-width: 350px; }

#SurveyTargetingForm_country li {
    display: block;
    float: left;
    width: 18%;
}
            ")
        ->registerScriptFile('jquery.min.js', CClientScript::POS_HEAD)
        ->registerScriptFile('jquery-ui.min.js', CClientScript::POS_HEAD)
        ->registerScriptFile('knockout.js', CClientScript::POS_HEAD);

$ct = new CodeTable();
?>
<p class="note">Fields with <span class="required">*</span> are required.</p>
<div class="row">
    <?php // echo $form->errorSummary($creativeProject)   ?>
    <?php // echo $form->errorSummary($SurveyTargetingForm) ?>
</div>

<div class="row">
    <h2>Survey Period</h2>    
    From:
    <?php
    // Date Rage date picker
    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
        'model' => $creativeProject,
        'attribute' => 'launch_date',
        // additional javascript options for the date picker plugin
        'options' => array(
            'dateFormat' => 'dd-mm-yy',
            'showAnim' => 'fold',
            'changeMonth' => true,
            'changeYear' => true,
            'onClose' => 'js:function( selectedDate ) {
                            $( "#' . get_class($creativeProject) . '_close_date" ).datepicker( "option", "minDate", selectedDate );
                            }'
        ),
        'htmlOptions' => array(
            'style' => 'height:20px;'
        ),
        'scriptFile' => false
    ));
    ?>
    To:
    <?php
    // Date Rage date picker
    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
        'model' => $creativeProject,
        'attribute' => 'close_date',
        // additional javascript options for the date picker plugin
        'options' => array(
            'dateFormat' => 'dd-mm-yy',
            'showAnim' => 'fold',
            'changeMonth' => true,
            'changeYear' => true,
            'onClose' => 'js:function( selectedDate ) {
                            $( "#' . get_class($creativeProject) . '_launch_date" ).datepicker( "option", "maxDate", selectedDate );
                            }'
        ),
        'htmlOptions' => array(
            'style' => 'height:20px;'
        ),
        'scriptFile' => false
    ));
    ?>
    <?php echo $form->error($creativeProject, 'launch_date') ?>
    <?php echo $form->error($creativeProject, 'close_date') ?>
</div>
<div class="row">
    <h2><?php echo $form->labelEx($creativeProject, 'category_id') ?></h2>    
    <?php echo $form->radioButtonList($creativeProject, 'category_id', CHtml::listData(MCreativeCategory::model()->findAll(), 'category_id', 'category_name'), array('separator' => '&nbsp;')) ?>
    <?php echo $form->error($creativeProject, 'category_id') ?>
</div>
<div class="row">
    <h2><?php echo $form->labelEx($creativeProject, 'template_id') ?></h2>
    <?php echo $form->radioButtonList($creativeProject, 'template_id', CHtml::listData(MCreativeTemplate::model()->findAll(), 'template_id', 'template_name'), array('separator' => '&nbsp;')) ?>
    <?php echo $form->error($creativeProject, 'template_id') ?>
</div>
<div class="row">
    <h2><?php echo $form->labelEx($SurveyTargetingForm, 'country') ?></h2>
    <?php
    echo $form->checkBoxList($SurveyTargetingForm, 'country', $ct->country(), array(
        'separator' => '',
        'data-bind' => 'checked: Country',
        'template' => '<li>{input} {label}</li>',
        'encode' => false,
    ))
    ?>    
    <div class="clear"></div>
    <?php echo $form->error($SurveyTargetingForm, 'country') ?>
</div>    
<div class="row">
    <h2><?php echo $form->labelEx($SurveyTargetingForm, 'language') ?></h2>    
    <select id="language" name="<?php echo get_class($SurveyTargetingForm) . '[language][]' ?>" data-bind="foreach: LanguageList, selectedOptions: Language" multiple="multiple" class="chosen-select" data-placeholder="Select Languages">
        <optgroup data-bind="attr:{label: text}, foreach: children">
            <option data-bind="text: text, option: id"></option>
        </optgroup>
    </select>
    <?php echo $form->error($SurveyTargetingForm, 'language') ?>
</div>
<div class="row"><h2><?php echo $form->labelEx($SurveyTargetingForm, 'title') ?></h2>
    <span data-bind="if: Language() == ''">Select the Languages and enter Survey Title for each language</span>
    <?php
    // render Survey Title for editing
    ?>
    <?php /*
      <div data-bind="foreach: Language">
      <br><span data-bind="text: LanguageLabel($data)"></span><span class="required">*</span>
      <br><input class="text-input" type="text" data-bind="attr: {name: $data, placeholder: 'Enter Title for ' + LanguageLabel($data)}" size="100" required/>
      </div>
     *
     */ ?>
    <div data-bind="foreach: Language_Title">
        <br><span data-bind="text: name"></span><span class="required">*</span>
        <br><input class="text-input" type="text" data-bind="attr: {name: id, placeholder: 'Enter Title for ' +  name, value:title}" size="100" required/>
    </div>
    <?php echo $form->error($SurveyTargetingForm, 'title') ?>
</div>
<div class="row"><h2>Target Conditions</h2></div>
<div class="row">
    <div><?php echo $form->labelEx($SurveyTargetingForm, 'prefecture_city') ?>
        <div class="row">
            <?php // echo CHtml::dropDownList('prefecture_region[]', null, array(), array('id'=>'prefecturecity','multiple'=>'multiple','class'=>"chosen-select",'data-placeholder'=>'Select Prefecture/Region'))  ?>
            <?php
            // $ct->data(CodeTable::COUNTRY_PREFECTURE)
//        echo CHtml::dropDownList('prefecture_region[]', null, $ct->data(CodeTable::COUNTRY_PREFECTURE));
//                $this->widget('ext.jui.JuiMultiSelect2', array('options' => array(), 'name' => 'prefecture_region[]', 'htmlOptions' => array('id' => 'prefecturecity')));
            ?>
            <?php
            $this->widget('ext.jui.chosen.JuiChosen', array('model' => $SurveyTargetingForm, 'attribute' => 'prefecture_city', 'data' => array(), 'htmlOptions' => array('multiple' => 'multiple', 'id' => 'prefecturecity', 'data-placeholder' => 'Select Prefectures / Cities',
//                    'data-bind'=>'option: PrefectureCityList, optionText:text , optionValue:id'
        )))
            ?>
            <?php echo $form->error($SurveyTargetingForm, 'prefecture_city') ?>
        </div>
    </div>
</div>
<div class="row">
    <?php echo $form->labelEx($SurveyTargetingForm, 'gender') ?>
    <?php echo $form->checkBoxList($SurveyTargetingForm, 'gender', $ct->data(CodeTable::GENDER), array('separator' => '&nbsp;')) ?>
    <?php echo $form->error($SurveyTargetingForm, 'gender') ?>
</div>
<div class="row">
    <?php echo $form->labelEx($SurveyTargetingForm, 'marital_status') ?>
    <?php echo $form->checkBoxList($SurveyTargetingForm, 'marital_status', $ct->data(CodeTable::MARRIAGE), array('separator' => '&nbsp;')) ?>
    <?php echo $form->error($SurveyTargetingForm, 'marital_status') ?>
</div>
<div class="row">
    <?php echo CHtml::tag('span', array(), 'Age') ?><span class="required">*</span>
    <?php echo $form->dropDownList($SurveyTargetingForm, 'age_min', array('10' => '10', '20' => '20', '30' => '30')) ?>
    <?php echo CHtml::label('(Min)', 'ageMin') ?>
    <?php echo $form->dropDownList($SurveyTargetingForm, 'age_max', array('10' => '10', '20' => '20', '30' => '30')) ?>
    <?php echo CHtml::label('(Max)', 'ageMax') ?>
    <?php echo $form->error($SurveyTargetingForm, 'age_min') ?>
    <?php echo $form->error($SurveyTargetingForm, 'age_max') ?>
</div>
<div class="row">
    <h2><?php echo $form->label($SurveyTargetingForm, 'sample') ?>
        <span id="sample_value" style="font-size: 18px"><?php
            if (!empty($SurveyTargetingForm->sample)):
                echo $SurveyTargetingForm->sample;
            else:
                ?>Please select sample size<?php endif ?></span>
    </h2>
    <?php
    $this->widget('zii.widgets.jui.CJuiSliderInput', array(
        'model' => $SurveyTargetingForm,
        'attribute' => 'sample',
        // additional javascript options for the slider plugin
        'options' => array(
            'range' => false,
            'min' => 0,
            'max' => 500,
            'step' => 10,
            'stop' => 'js:function(event, ui) {$("#sample_value").text(ui.value);}'
        ),
        'htmlOptions' => array(
//            'style' => 'height:20px;',
        ),
        'scriptFile' => false
    ));
    $cs->registerScript('slider_label', '$("#' . get_class($SurveyTargetingForm) . '_sample_min_slider")'
            . ' .prepend(\'<span class="ui-slider-inner-label" style="position: absolute; left:0px; top:15px; text-shadow:none; color:black; font-weight:normal">' . '0' . '</span>\')'
            . '.append(\'<span class="ui-slider-inner-label" style="position: absolute; right:0px; top:15px; text-shadow:none; color:black; font-weight:normal">' . '500' . '</span>\');	')
    ?>
    <p><?php echo $form->error($SurveyTargetingForm, 'sample_min') ?></p>    
</div>

<script>

    function populatePrefectureCityList() {
        $.ajax("<?php echo Yii::app()->createAbsoluteUrl($this->id . '/prefecturecity') ?>" + "&country=" + Country(), {
            success: function(data) {
                var select = new HTML5Select('#prefecturecity');

                var result = new Array();

                data = JSON.parse(data);
                for (_country in data) {
                    var _prefectureList = new Array();
                    for (Prefecture in data[_country]) {
                        var _prefecture = new mapItem(data[_country][Prefecture]);
                        _cities = new Array();
                        for (Item in data[_country][Prefecture]['city']) {
                            var cityList = data[_country][Prefecture]['city'][Item];
                            for (cityId in cityList) {
                                _cities.push(new option(cityId, cityList[cityId]));
                            }
                        }
                        _prefectureList.push(new Soptgroup(_prefecture.name, _cities));
                    }
                    result.push(new Soptgroup(_country, _prefectureList));
                }

                var s = $("<select></select>");
                $.each(result, function(i) {
                    var _country = result[i].label;
                    var children = result[i].children;
                    $.each(children, function(j) {
                        var optgroup1 = new $('<optgroup>');
                        var inner = children[j].children;
                        $.each(inner, function(x) {
                            var option = new $("<option></option>");
                            option.val(inner[x].id);
                            option.text(inner[x].label);
                            optgroup1.append(option);
                        });
                        optgroup1.attr('label', ucwords(_country) + ":" + children[j].label);
                        s.append(optgroup1);
                    });
                });
                $("#prefecturecity").empty().html(s.html());
                select.setSelectedOptions();
                $("#prefecturecity").trigger('chosen:updated');
            } // end success
        }); // end .ajax
    }

    function populateCountryLanguageList() {
        $.ajax("<?php echo Yii::app()->createAbsoluteUrl($this->id . '/language') ?>" + "&country=" + Country(), {
            success: function(data) {
                data = JSON.parse(data);
                var select = new HTML5Select('#language');
                LanguageList(data);
                var optgroup = $("#language optgroup");
                $.each(optgroup, function(i) {
                    // Set selected if only one option
                    var g = $(optgroup[i]);
                    if (g.find("option").length === 1) {
                        g.find("option").attr('selected', true);
                    }
                });
                select.setSelectedOptions();
                $("#language").trigger("chosen:updated");
            }
        }); // end .ajax
    }

    var option = function(id, value) {
        this.id = id;
        this.label = value;
    };
    var optgroup = function(label, children) {
        this.label = ko.observable(label);
        this.children = ko.observableArray(children);
    };
    var Soptgroup = function(label, children) {
        this.label = label;
        this.children = children;
    };

    var mapItem = function(item) {
        this.id = item.id;
        this.name = item.name;
    };

    function ucwords(str) {
        return (str + '').replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function($1) {
            return $1.toUpperCase();
        });
    }

    function LanguageLabel(id) {
        var str = $('#language option[value="' + id + '"]').text().split("-");
        return str[1];
    }

    var HTML5Select = function(selectID) {
        this.saveOptions = function() {
            return $(select + ' option:selected');
        };
        this.setSelectedOptions = function() {
            $.each(selected, function(i) {
                var option = selected[i];
                $(select + ' option[value="' + option.value + '"]').attr('selected', true);
            });
            this.saveOptions();
            $(select).trigger('change');
        };
        var select = selectID;
        var selected = this.saveOptions();
    };

    ko.bindingHandlers.option = {
        update: function(element, valueAccessor) {
            var value = ko.utils.unwrapObservable(valueAccessor());
            ko.selectExtensions.writeValue(element, value);
        }
    };

// TODO: remove debug
    var myViewModel = function() {
        Country = ko.observableArray(<?php echo!empty($SurveyTargetingForm->country) ? json_encode($SurveyTargetingForm->country) : '' ?>);
        LanguageList = ko.observableArray(<?php echo $languageList ? json_encode($languageList) : '' ?>);
        Language = ko.observableArray(<?php echo !empty($SurveyTargetingForm->title) ? json_encode(array_keys($SurveyTargetingForm->title)) : '' ?>);

        var _titles = [];

        Language_Title = ko.computed(function() {
            var id, label, foundTitle, o, title;

            var selectedLanguages = Language();
            if (selectedLanguages.length === 0)
                return;

            _titles = [];
            for (var i = 0; i < selectedLanguages.length; i++) {
                id = selectedLanguages[i];
                label = LanguageLabel(id);
                o = $("input[name='" + id.substring(2) + "']");
                if (o instanceof jQuery) {
                    title = $(o).val();
                }
                foundTitle = _titles.filter(function(title) {
                    return title.name === label;
                });
                if (foundTitle.length === 0) {
                    _titles.push({id: id.substring(2), name: label, title: title});
                }
            }
            return _titles;
        }, this, {deferEvaluation: true});

        Country.subscribe(function() {
            if (Country().length === 0) {
                var selector = new Array('#language', '#prefecturecity');
                $.each(selector, function(i) {
                    $(selector[i] + ' option').remove();
                    $(selector[i] + ' optgroup').remove();
                    $(selector[i]).trigger('change').trigger('chosen:updated');
                });
                return;
            }

            populateCountryLanguageList();
            populatePrefectureCityList();
        }); // end Country.subscribe

    }; // end myViewModel

    var myModel = new myViewModel();

    $(window).bind("load", function() {
        ko.applyBindings(myModel, document.getElementById('creative-project-creativeprojectform-form'));

<?php if (!empty($SurveyTargetingForm->language)):
            foreach($SurveyTargetingForm->language as $languageid):
?>
            $('#language option[value="<?php echo $languageid?>"]').attr('selected', true);
            $('#language').trigger('chosen:updated');
<?php
endforeach;
endif ?>

<?php
if (!empty($SurveyTargetingForm->title)):
    $storedid = array();
    foreach ($SurveyTargetingForm->title as $languageid => $title) {
        $id = substr($languageid, 2);
        if (!in_array($id, $storedid)):
            ?>
                    $('input[type="text"][name="<?php echo $id ?>"]').val("<?php echo $title ?>");
            <?php
            $storedid[] = $id;
        endif;
    }
endif
?>

<?php
if (!empty($SurveyTargetingForm->country)):
//    Yii::log('Countries:' . print_r($SurveyTargetingForm->country, true));
    ?>
            populatePrefectureCityList('<?php echo implode(",", $SurveyTargetingForm->country) ?>');
            function updatePrefectureCity() {
    <?php foreach ($SurveyTargetingForm->prefecture_city as $cityId): ?>
                    $("option[value='<?php echo $cityId ?>']").attr("selected", true);<?php print "\n"; ?>
    <?php endforeach ?>
                $('#prefecturecity').trigger('chosen:updated');
            }
            window.setTimeout(updatePrefectureCity, 1000);
<?php endif ?>

    });

</script>