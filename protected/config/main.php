<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');
// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.

$xml_config = json_decode(json_encode(simplexml_load_file(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.xml')));

Yii::setPathOfAlias('webroot', dirname(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..'));
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',    
    'name' => 'AIP Creative Surveys',
    // preloading 'log' component
    'preload' => array('log'),
    // autoloading model and component classes
    'import' => array(
        'application.models.*',
        'application.components.*',
    ),
    'modules' => array(
        // uncomment the following to enable the Gii tool

        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => 'a',
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            'ipFilters' => array('127.0.0.1', '::1'),
        ),
    ),
    // application components
    'components' => array(
        'user' => array(            
            // enable cookie-based authentication
            'allowAutoLogin' => true,
        ),
        'assetManager' => array(
            'linkAssets' => true,
        ),
        'clientScript' => array(
            'scriptMap' => array(
                'jquery-ui.css'=>'css/jquery-ui-1.10.3.custom.min.css',
                'jquery-ui.min.js' => 'js/jquery-ui-1.10.3.custom.min.js',
                'jquery.js' => 'js/jquery-1.10.2.js',
                'jquery.min.js' => 'js/jquery-1.10.2.min.js',
                'knockout.js' => 'js/knockout-3.0.0.js',
                'filesize.js'=>'js/filesize.min.js', // required by application.widgets.image_uploader.ImageUploader
            ),
        ),
        'email' => array(
            'class' => 'ext.yii-phpmailer.YiiMailer',
            'pathViews' => 'application.views.email',
            'pathLayouts' => 'application.views.layouts.email',
        ),
        'widgetFactory' => array(
            'widgets' => array(
                'CJuiCJuiDatePicker' => array(
//          'scriptUrl'=>'//ajax.googleapis.com/ajax/libs/jqueryui/1/',
//                    'scriptUrl' => '//ajax.googleapis.com/ajax/libs/jqueryui/1/',
//          'theme'=>JUI-THEME,
//          'themeUrl'=>'//ajax.googleapis.com/ajax/libs/jqueryui/1/themes/',
                    'cssFile' => false,
                ),
            ),
        ),
        'messages' => array(
            'class' => 'SurveyMessageSource',
            'sourceMessageTable' => 'source_message',
            'translatedMessageTable' => 'message',
            'cachingDuration' => 3600,
        ),
        'cache' => array('class' => 'CDbCache', 'connectionID' => 'cachedb'),
        // uncomment the following to enable URLs in path-format
        /*
          'urlManager'=>array(
          'urlFormat'=>'path',
          'rules'=>array(
          '<controller:\w+>/<id:\d+>'=>'<controller>/view',
          '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
          '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
          ),
          ),
         */
        /*
          'db' => array(
          'connectionString' => 'sqlite:' . dirname(__FILE__) . '/../data/testdrive.db',
          ),
         */
        'rbacdb'=>array(
            'class'=>'CDbConnection',
            'connectionString' => 'sqlite:' . dirname(__FILE__) . '/../data/rbacdb.db',
        ),
        // TODO: Dev cache to be set for PRD cache
        'cachedb' => array(
            'class' => 'CDbConnection',
            'connectionString' => 'sqlite:' . dirname(__FILE__) . '/../runtime/cachedb.db',
        ),
        // uncomment the following to use a MySQL database
        /*
          'db'=>array(
          'connectionString' => 'mysql:host=localhost;dbname=testdrive',
          'emulatePrepare' => true,
          'username' => 'root',
          'password' => '',
          'charset' => 'utf8',
          ),
         */

        // Creative DB
        'db' => array(
            'connectionString' => 'pgsql:host=localhost;dbname=creative',
//          'emulatePrepare' => true,
            'username' => 'creative',
            'password' => 'creative',
            'charset' => 'utf8',
            'schemaCachingDuration'=>3600,
            'enableParamLogging'=>true,
        ),

        // AuthManager
        'authManager'=>array(
            'class'=>  'CDbAuthManager',
            'connectionID'=>'rbacdb',
            'defaultRoles'=>array('authenticated', 'admin','guest'),
        ),

        'errorHandler' => array(
            // use 'site/error' action to display errors
            'errorAction' => 'site/error',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels'=>'error,warning',
//                    'levels' => 'error, warning,info,trace',
//                    'levels' => 'error, warning,trace',
                ),
                // uncomment the following to show log messages on web pages
                array(
                    'class' => 'CWebLogRoute',
//                    'enabled'=>YII_DEBUG,
                     'levels' => 'error, warning,trace, info',
                    'showInFireBug'=>false
                ),
            ),
        ),
    ),
    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(
        // this is used in contact page
        'adminEmail' => 'webmaster@example.com',
        // config.xml file
        'xmlconfig' => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.xml',
    ),
);
