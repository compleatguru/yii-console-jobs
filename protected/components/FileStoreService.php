<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ImageStoreService
 *
 * @author David
 */
class FileStoreService extends CComponent {

    /**
     * Save Image
     * @param CUploadedFile $uploadedFile
     * @return mixed string of destination or false if unsuccessful
     */
    public function saveFile($uploadedFile) {
        if(get_class($uploadedFile) != 'CUploadedFile') return false;
        $config = json_decode(json_encode(simplexml_load_file(Yii::app()->params['xmlconfig'])));
        $webRoot = YiiBase::getPathOfAlias('webroot');
        $mediaPath = $webRoot . '/' . $config->Media->path;

        $filename = str_replace(array(' ','#'), '', $uploadedFile->name);
        $filename = $ts = round(microtime(true)*1000,0) . $filename;
        $dest = $mediaPath . '/' . $filename;

        if ($uploadedFile->saveAs($dest)) {
            Yii::log('Saving File to Destination: ' . $dest);
            return $config->Media->path . '/' . $filename;
        } else {
            Yii::log('File upload error:' . $uploadedFile->error);
            return false;
        }
    }

    public function deleteFile($file){
        $config = json_decode(json_encode(simplexml_load_file(Yii::app()->params['xmlconfig'])));
        $webRoot = YiiBase::getPathOfAlias('webroot');
        $destFile = $webRoot . '/' . $file;
        $destFile = str_replace('/', DIRECTORY_SEPARATOR, $destFile);
        Yii::log('Delete file: '.$destFile);
        $currentErrorReporting = error_reporting();
        error_reporting(0);
        $status = unlink($destFile);
        error_reporting($currentErrorReporting);
    }

}
