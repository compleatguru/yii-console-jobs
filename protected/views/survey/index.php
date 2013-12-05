<?php
/* @var $this SiteController */

$projectid = !empty($this->actionParams['id']) ? $this->actionParams['id'] : false;

$breadcrumbs = array('Survey Management' => array('/survey/index'));
switch ($activeTab) {
    case SurveyController::TAB_CREATION:
        $title = 'Creations For ' . Yii::app()->user->name;
        $breadcrumbs[] = 'Creations';
        break;
    case SurveyController::TAB_NEW:
        $title = 'New Project';
        $breadcrumbs[] = 'New Survey';
        break;
    case SurveyController::TAB_EDIT:
        if ($projectid) {
            switch(strtolower($this->action->id)){
                case 'attributesassociation':
                    $breadcrumbs = array(
                        'Edit Project#'.$projectid=>array('survey/editsurvey','id'=>$projectid),
                        'Project#'.$projectid.' Attribute Association Setup'
                    );
                    break;

                case 'imagecomparison':
                    $breadcrumbs = array(
                        'Edit Project#'.$projectid=>array('survey/editsurvey','id'=>$projectid),
                        'Project#'.$projectid.' Image Comparison Setup'
                    );
                    break;

                case 'editsurvey':
                    $title = 'Edit Project#' . $projectid;
                    $breadcrumbs[] = $title;
            }
            
        } else
            throw new CException('Missing Project ID');
        break;
    case SurveyController::TAB_STATS:
        $title = 'Project Statistics for ' . Yii::app()->user->name;
        $breadcrumbs[] = 'Statistics';
        break;
    default: throw new CException('Unknown Tab: ' . $activeTab);
}

$this->pageTitle = $title;
$this->breadcrumbs = $breadcrumbs;
$tabId = 'user-survey-list';

$cs = Yii::app()->clientScript;
$cs->registerCssFile(Yii::app()->baseUrl . '/css/user-dashboard.css');
?>
<div class="view">
    <?php
// Setup Tabs
// survey form page
    if ($activeTab == SurveyController::TAB_NEW || $activeTab == SurveyController::TAB_EDIT) {        
        $surveyContent = $this->renderPartial('survey-form/_new_survey_layout', 
                array(
                    // New Survey
                    'creativeProject' => $creativeProject,
                    'SurveyTargetingForm' => $SurveyTargetingForm,

                    // Common for both Attribute Association and Image Comparison
                    'creativeProjectQuestionMedia' => $creativeProjectQuestionMedia,
                    'creativeProjectQuestion' => $creativeProjectQuestion,
                    
                    // Attribute Association: as with Common

                    // Image Comparison
                    'creativeProjectQuestionRating' => $creativeProjectQuestionRating,
                    'creativeProjectQuestionByLanguage' => $creativeProjectQuestionByLanguage,
                    
                    'page' => $page), true);
    } else
        $surveyContent = '';

    if (!$projectid) {
        $surveyTabLabel = $activeTab == SurveyController::TAB_NEW ? 'New' : CHtml::link('New', array('survey/newsurvey'));
        // New Tab
        $surveyTab = array(
            'content' => $activeTab == SurveyController::TAB_NEW ? $surveyContent : '',
            'id' => 'tab2');
    } else {
        // Edit Tab
        $surveyTabLabel = $activeTab == SurveyController::TAB_EDIT ? 'Edit Project #'.$projectid : CHtml::link('Edit', array('survey/editsurvey'));
        $surveyTab = array(
            'content' => $activeTab == SurveyController::TAB_EDIT ? $surveyContent : '',
            'id' => 'tab3');
    }

// Setup Tabs
    $tabs = array(
        // Creation Tab
        $activeTab == SurveyController::TAB_CREATION ? 'Creations' : CHtml::link('Creations', array('survey/index'))
        =>
        array(
            'content' => $activeTab == SurveyController::TAB_CREATION ? $this->renderPartial('_creations', array('dataProvider' => $dataProvider), true) : '',
            'id' => 'tab1'
        )
    );


    if ($activeTab == SurveyController::TAB_EDIT) {
        $tabs = array_merge($tabs, array(
            CHtml::link('New', array('survey/newsurvey')) => array('id'=>'tab2'),
            $surveyTabLabel => $surveyTab,
            CHtml::link('Statistics', array('survey/statistics')) => array('id'=>'tab4')
                )
        );
    } else {
        $tabs = array(
            // Creation Tab
            $activeTab == SurveyController::TAB_CREATION ? 'Creations' : CHtml::link('Creations', array('survey/index'))
            =>
            array(
                'content' => $activeTab == SurveyController::TAB_CREATION ? $this->renderPartial('_creations', array('dataProvider' => $dataProvider), true) : '',
                'id' => 'tab1'
            ),
            // Survey Tab
            $surveyTabLabel => $surveyTab,
            // Stats Tab
            $activeTab == SurveyController::TAB_STATS ? 'Statistics' : CHtml::link('Statistics', array('survey/statistics'))
            =>
            array(
                'content' => $activeTab == SurveyController::TAB_STATS ? $this->renderPartial('_statistics', array('model' => new LoginForm()), true) : '',
                'id' => 'tab3'),
        );
    }

if($activeTab == SurveyController::TAB_EDIT) $activeTab=2;

    $this->widget('zii.widgets.jui.CJuiTabs', array(
        'tabs' => $tabs,
        // additional javascript options for the tabs plugin
        'options' => array(
            'active' => $activeTab
        ),
        'id' => $tabId,
    ));
    ?>
</div>

<script>
    $(function() {
        $("#<?php echo $tabId ?>").tabs().addClass("ui-tabs-vertical ui-helper-clearfix");
        $("#<?php echo $tabId ?>>li").removeClass("ui-corner-top").addClass("ui-corner-left");
    });
</script>