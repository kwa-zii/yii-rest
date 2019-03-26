<?php
/**
 * A lightweight and powerful Restful interface framework based on Yii2
 * PHP version 5.6 or newer
 * 
 * @category Restful_Api_Examples
 * @package  Qinqw\Yii\Rest\examples
 * @author   Kevin <qinqiwei@hotmail.com>
 * @license  Apache 2.0
 * @link     https://github.com/qinqw/yii-rest
 */

require_once 'bootstrap.php';

defined('YII_ENV') or define('YII_ENV', 'dev');
defined('YII_DEBUG') or define('YII_DEBUG', true);

$config = [
    'id' => 'ETCP_cloud ',
    'controllerNamespace' => 'controllers',
    'vendorPath' => dirname(__DIR__) . '/vendor',
    'basePath' => __DIR__,
    'bootstrap' => ['log'],
    'timeZone' => 'Asia/Shanghai',//时区设置
    'modules' => [
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'dmpisthebest',
            'parsers' => [
               'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 10 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning','info',],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/index',
        ],
        'user' => [
            'identityClass' => '',
            'enableAutoLogin' => false,
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '127.0.0.1',
            'port' => 6379,
            //'password' => '',
            'database' => 15,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            //'enableStrictParsing' => true,
            'rules' => [
                //''=>'';
            ]
        ],
    ],
    'params' => [
        'adminEmail' => 'qinwiei@etcp.cn',
        'enable_sign' => true,                     //sign validation switch
        'enable_token' => true,                    //Token validation switch
        'access_control' => [                       //access_control_allow
            'allow_origin'=>['*','*.qinqiwei.com'],
            'allow_methods'=>['get','post','delete'],
            'allow_headers'=>['token'],
        ]
    ],
];

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';
Yii::setAlias('@controllers', __DIR__ .'/controllers');

$application = new yii\web\Application($config);
$application->run();




