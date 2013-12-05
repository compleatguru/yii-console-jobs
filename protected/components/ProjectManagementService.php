<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProjectManagementService
 *
 * @author David
 */
class ProjectManagementService extends CComponent {

    private $timestamp;

    private function getUserParams($encode = false) {
        if (Yii::app()->user->isGuest)
            throw new CException('Blocked for Guest');
        if (empty(Yii::app()->user->id))
            throw new CException('Missing User Id');

        if (empty($this->timestamp))
            $this->timestamp = round(microtime(true) * 1000, 0);

        $user = User::model()->findByPk(Yii::app()->user->id);

        $params = array(
//            'id' => $user->creative_user_id,
//            'uuid' => $user->uuid,
            'id' => 1,
            'uuid' => 'c828378b08ee423f',
            'd' => $this->timestamp,
        );


        if ($encode) {
            return $params;
        } else {
            unset($params['uuid']);
            return $params;
        }
    }

    private function getSig(array $params) {
        if (!isset($params['uuid']))
            throw new CException('Missing UUID');

        $str = http_build_query($params);
        $str = urldecode($str);
        echo '<br>'.$str;

        return sha1($str);
    }

    /**
     * example: 'COUNTRY_ID', 'SG,MY');
      'SG_LANGUAGE', '1');
      'MY_LANGUAGE', '1,3,7');
      'SG_AREA', '06007');
      'MY_AREA', '07002');
      'MY_CITY', '07002030,07002040,07002050');
      'GENDER', '1,2');
      'MARRIAGE', '1,2,3');
      'AGE_FROM', '30');
      'AGE_TO', '40');
      'SIZE', '100');
      'CID', CREATIVE_PROJECT_ID);
     *
     * @param integer $project_id
     */
    public function launch($creative_project_id) {

        $project = CreativeProject::model()->findByPk($creative_project_id);
        /* @var $project CreativeProject */

        $countryLanguageParams = $this->getProjectCountryLanguageParams($project);
        $targetingParams = $this->getTargetingParams($project);

        $params['COUNTRY_ID'] = $countryLanguageParams['COUNTRY_ID'];

        $Country_Attributes = array('LANGUAGE', 'AREA', 'CITY');

        $country = explode(',', $params['COUNTRY_ID']);

        foreach ($Country_Attributes as $attribute) {
            foreach ($country as $country_id) {
                $value = null;
                if($attribute != 'LANGUAGE'){
                if (isset($targetingParams[$country_id . '_' . $attribute])) {
                    $value = $targetingParams[$country_id . '_' . $attribute];
                }
                }
                else{
                    if (isset($countryLanguageParams[$country_id . '_' . $attribute])) {
                    $value = $countryLanguageParams[$country_id . '_' . $attribute];
                }
                }
                if(!empty($value))
                    $params[$country_id . '_' . $attribute] = $value;
            }
        }

        $attributes = array('GENDER', 'MARRIAGE', 'AGE_FROM', 'AGE_TO', 'SIZE');
        foreach ($attributes as $attribute) {
            $params[$attribute] = $targetingParams[$attribute];
        }

        $params['CID'] = $creative_project_id;

        $out = array_merge($params,$this->getUserParams());
        $encode = array_merge($params,$this->getUserParams(true));
        $out['sig'] = $this->getSig($encode);

        echo '<pre>' . print_r($encode,true) . '</pre>';
        echo '<pre>' . print_r($out,true) . '</pre>';
//        exit;
        $this->API('launch',$out);
    }

    private function API($action, $params) {
        switch($action){
            case 'launch': $url = 'http://10.0.0.22:8080/psiteapi/doValidateCreativeTargetCondition.do';
        }
        $ch = curl_init();
        $opt = array(
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_VERBOSE => true,
                // CURLOPT_STDERR=>'C:\www\curl.txt',
        );

        curl_setopt_array($ch, $opt);
        $result = curl_exec($ch);
        echo 'Result:' . $result;
        $data = json_decode($result);
        print_r($data);
        return;
    }

    private function getProjectCountryLanguageParams($creativeProjectModel) {
        $projectCountryLanguage = $creativeProjectModel->creativeProjectCountryLanguages;

        $language = $country = array();

        /* @var $model CreativeProjectCountryLanguage */
        foreach ($projectCountryLanguage as $model) {
            $country_id = $model->country_id;
            $language_id = $model->language_id;
            $country[] = $country_id;
            $language[$country_id . '_LANGUAGE'][] = $language_id;
        }

        $map = function($item) {
            return implode(',', $item);
        };

        $language = array_map($map, $language);

        $params['COUNTRY_ID'] = implode(',', $country);
        return array_merge($params, $language);
    }

    private function getTargetingParams($creativeProjectModel) {
        $targetConditions = $creativeProjectModel->creativeProjectTargetConditions;

        $targetParams = array();

        foreach ($targetConditions as $condition) {
            switch ($condition->category) {
                case 'country_prefecture_city':
                    $country_prefecture_city = json_decode($condition->value);

                    foreach ($country_prefecture_city as $country_id => $prefectures) {
                        foreach ($prefectures as $prefecture_id => $cities) {
                            $targetParams[$country_id . '_AREA'][] = $prefecture_id;
                            foreach ($cities as $city_id) {
                                $targetParams[$country_id . '_CITY'][] = $city_id;
                            }
                        }
                    }

                    foreach ($targetParams as $key => $value) {
                        if (stripos($key, '_AREA') !== false || stripos($key, '_CITY') !== false) {
                            $targetParams[$key] = implode(',', $value);
                        }
                    }

                    break;
                case 'gender': $targetParams['GENDER'] = $condition->value;
                    break;
                case 'marital_status':$targetParams['MARRIAGE'] = $condition->value;
                    break;
                case 'sample':$targetParams['SIZE'] = $condition->value;
                    break;
                case 'age_max':$targetParams['AGE_FROM'] = $condition->value;
                    break;
                case 'age_min':$targetParams['AGE_TO'] = $condition->value;
                    break;
            }
        }
        return $targetParams;
    }

}
