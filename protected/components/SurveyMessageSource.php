<?php

/**
 * Description of SurveyMessageSource
 *
 * <br>Usage
 * <br>Set language to desired translation.
 * <br>Yii::app()->language = 'zh';
 * <br>echo Yii::t('item_id','default English message');
 *
 * @author David
 */
class SurveyMessageSource extends CDbMessageSource {

    /**
     * Override to use customized item column field, instead of default column field name "category"
     * 
     * @param string $item
     * @param string $language
     * @return mixed array or string
     */
    protected function loadMessagesFromDb($item, $language) {
        $sql = <<<EOD
SELECT t1.message AS message, t2.translation AS translation
FROM {$this->sourceMessageTable} t1, {$this->translatedMessageTable} t2
WHERE t1.id=t2.id AND t1.item=:item AND t2.language=:language
EOD;
        $command = $this->getDbConnection()->createCommand($sql);
        $command->bindValue(':item', $item);
        $command->bindValue(':language', $language);
        $messages = array();
        foreach ($command->queryAll() as $row)
            $messages[$row['message']] = $row['translation'];

        return $messages;
    }
}
