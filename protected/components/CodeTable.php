<?php

/**
 * CodeTable Component handles all code table data
 *
 * @author David
 */
class CodeTable extends CComponent {

    // User Flash Message
    const FLASH_MESSAGE_SUCCESS = 'success';
    const FLASH_MESSAGE_ERROR = 'error';
    // CodeTable Data API
    const COUNTRIES = 'codeTable.countries';
    const COUNTRY_LANGUAGE = 'codeTable.country.language';
    const COUNTRY_PREFECTURE = 'codeTable.country.prefecture';
    const GENDER = 'codeTable.gender';
    const MARRIAGE = 'codeTable.marriage';
    const LANGUAGE = 'country_language';
    // indices name of the Code Table
    const INDEX_COUNTRY = 'country';
    const INDEX_LANGUAGE = 'language';
    const INDEX_PREFECTURE = 'prefecture';
    const INDEX_GENDER = 'gender';
    const INDEX_MARRIAGE = 'marriage';
    const CACHE_DURATION = 0;
    const URL_CODE_DATA = 'http://10.0.0.22:8080/psiteapi/doCreativeMasterList.do';

    /**
     * Data Mapping where
     * array( JSON_KEY => CacheID)
     *
     * @var array
     */
    private $indexMap = array(
        self::INDEX_COUNTRY => self::COUNTRIES,
        self::INDEX_PREFECTURE => self::COUNTRY_PREFECTURE,
        self::INDEX_LANGUAGE => self::COUNTRY_LANGUAGE,
        self::INDEX_GENDER => self::GENDER,
        self::INDEX_MARRIAGE => self::MARRIAGE,
    );

    /**
     * Search for data found in cache,
     * if not found, cache it
     * else get data and cache it
     * 
     * @param string $cacheID
     * @return array if found, else null
     */
    private function setData($cacheID) {
        if ($cacheID == 'refresh') {
            return $this->fetchData();
        }

        $value = Yii::app()->cache->get($cacheID);
        if (!empty($value)) {
            return $value;
        }

        // Local Dev
        $data = json_decode(file_get_contents(Yii::app()->basePath . '/data/codeTable.json'), true);

        // Live Access
//        $data = json_decode($this->fetchData(), true);

        if (empty($data))
            throw new CException('Missing CodeTable data');

        switch ($cacheID) {
            case self::COUNTRY_LANGUAGE: $result = $this->setCountryLanguage($data);
                break;
            case self::COUNTRIES: $result = $this->setCountry($data);
                break;
            case self::COUNTRY_PREFECTURE: $result = $this->setCountryPrefectureCity($data);
                break;
            case self::GENDER:
            case self::MARRIAGE:
                $key = array_search($cacheID, $this->indexMap);

                if ($key && !is_array($key)) {
                    $result = $data[$key];
                }
                break;
        }
        if (!empty($result)) {
            $value = serialize($result);
            Yii::app()->cache->set($cacheID, serialize($result), self::CACHE_DURATION);
            return $value;
        }

        return null;
    }

    private function setCountry($data) {
        $key = $this->checkKey(self::COUNTRIES);
        $result = $data[$key];
        $map = array();
        foreach ($result as $country) {
            if (!empty($country['id']) && !empty($country['name']))
                $map[$country['id']] = $country['name'];
        }
        return $map;
    }

    private function setCountryLanguage($data) {
        $key = $this->checkKey(self::COUNTRIES);
        $result = $data[$key];
        $map = array();
        foreach ($result as $country) {
            if (!empty($country[self::INDEX_LANGUAGE])) {
                $map[$country['id']] = $country[self::INDEX_LANGUAGE];
            }
        }
        return $map;
    }

    private function setCountryPrefectureCity($data) {
        $key = $this->checkKey(self::COUNTRIES);
        $result = $data[$key];
        $map = array();
        foreach ($result as $country) {
            if (isset($country[self::INDEX_PREFECTURE]))
                $map[$country['id']] = $country[self::INDEX_PREFECTURE];
        }
        return $map;
    }

    private function checkKey($key) {
        $result = array_search($key, $this->indexMap);
        if ($result && !is_array($result))
            return $result;
        else
            throw new CException('Unable to process key:' . $key);
    }

    private function fetchData() {
        $url = self::URL_CODE_DATA;
        $ch = curl_init();
//        $post = array('PANEL_ID' => '702001', 'PANEL_MEMBER_ID' => '702001');
        $opt = array(
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
//            CURLOPT_POSTFIELDS => $post,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_VERBOSE => true,
                // CURLOPT_STDERR=>'C:\www\curl.txt',
        );

        curl_setopt_array($ch, $opt);
        $result = curl_exec($ch);
        return $result;
    }

    /**
     * Get Country Array Info
     * @param string  $countryName
     * @return array of $countries if $countryName is null
     * <br> array of Prefectures and Languages of $countryName
     * <br> null if $countryName is unfound
     */
    public function country($countryid = null) {
        $countries = unserialize($this->setData(self::COUNTRIES));
        if (empty($countryid))
            return $countries;
        else
            return isset($countries[$countryid]) ? $countries[$countryid] : null;
    }

    /**
     * Get Data
     * 
     * @param string $cacheID constant as defined in this class
     * @param mixed $id either as country name
     * @return array of data
     */
    public function data($cacheID, $id = null) {
        switch ($cacheID) {
            case self::COUNTRIES:
                $List = unserialize($this->setData($cacheID));
                break;

            case self::COUNTRY_LANGUAGE:
                if (empty($id)) {
                    return unserialize($this->setData($cacheID));
                } elseif (!empty($id)) {
                    $data = Yii::app()->cache->get($cacheID . '.' . $id);
                    if (!empty($data))
                        return unserialize($data);
                }


                $list = unserialize($this->setData($cacheID));

                // with $id but not in cache
                if (!empty($id)) {
                    if (!empty($list[$id]) && is_array($list[$id])) {
                        Yii::app()->cache->set($cacheID . '.' . $id, serialize($list[$id]), self::CACHE_DURATION);
                        return $list[$id];
                    } else
                        throw new CException('Unknown id:' . $id);
                }



                print_r($list);


                exit;
                $data = array();
                if (is_null($id)) {
                    // Get all country languages
                    foreach ($list as $country => $item) {
                        $countryName = $this->country($country);
                        $map = function ($item) use ($countryName) {
                            return ucwords($countryName . '-' . $item);
                        };

                        $languages = array_map($map, $item[self::INDEX_LANGUAGE]);

                        $key = array_map(function($i) use ($country) {
                            $i = str_replace(' ', '_', $i);
                            return strtolower($country . '-' . $i);
                        }
                                , $languages);

                        $data[$country] = array_combine($key, $languages);
                    }
                } else {
                    // Get Country $id Languages
                    $countryid = str_replace(' ', '', strtolower($id));
                    $country = $this->country($countryid);
                    $languages = isset($list[$countryid][self::INDEX_LANGUAGE]) ? $list[$countryid][self::INDEX_LANGUAGE] : array();
                    if (!empty($languages)) {
                        $map = function ($item) use ($country) {
                            return ucwords($country . '-' . $item);
                        };
                        $key = array_map(function($i) use ($countryid) {
                            $i = str_replace(' ', '_', $i);
                            return strtolower($countryid . '-' . $i);
                        }, $languages);
                        $languages = array_map($map, $languages);
                        $data = array_combine($key, $languages);
                    }
                }
                return $data;
            case self::COUNTRY_PREFECTURE:
                if (!empty($id)) {
                    $data = Yii::app()->cache->get($cacheID . '.' . $id);
                    if (!empty($data))
                        return $data;
                }

                $list = unserialize($this->setData($cacheID));
                // with $id but not in cache
                if (!empty($id)) {
                    if (!empty($list[$id]) && is_array($list[$id])) {
                        Yii::app()->cache->set($cacheID . '.' . $id, $list[$id], self::CACHE_DURATION);
                        return $list[$id];
                    } else
                        throw new CException('Unknown id:' . $id);
                }

                // all countries
                $List = $list;
                break;

            case self::MARRIAGE:
            case self::GENDER:
                $data = unserialize($this->setData($cacheID));
                foreach ($data as $item) {
                    foreach ($item as $id => $value) {
                        $List[$id] = $value;
                    }
                }
                break;
        }
       
        return $List;
    }

    public function getPrefectureId($countryId, $cityId) {
        $prefectures = array();
        if (is_array($countryId)) {
            foreach ($countryId as $country) {
                $data = $this->data(self::COUNTRY_PREFECTURE, $country);
                $prefectures = array_merge($prefectures, array_values($data));
            }
        } else
            $prefectures = $this->data(self::COUNTRY_PREFECTURE, $countryId);

        foreach ($prefectures as $prefecture) {
            foreach ($prefecture['city'] as $city) {
                if (isset($city[$cityId])) {
                    return $prefecture['id'];
                }
            }
        }
        return null;
    }

    public function getLanguageDescription($languageid){
        $country_id = substr($languageid, 0, 2);
        $langid = substr($languageid, 2);

        $languages = $this->data(self::COUNTRY_LANGUAGE, $country_id);
        
        $languageFilter = function($item) use($langid){ return $item['id']==$langid; };
        $result = array_filter($languages, $languageFilter);

        if(count($result)==1) $result = $result[key($result)];
        
        if(!empty($result)) return $result['name'];
        return null;        
    }

}
