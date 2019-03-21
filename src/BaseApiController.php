<?php
/**
 * A lightweight and powerful Restful interface framework based on Yii2
 * PHP version 5.6 or newer
 * 
 * @category Restful_Api_Framework
 * @package  Qinqw\Yii\Rest
 * @author   Kevin <qinqiwei@hotmail.com>
 * @license  Apache License V2
 * @link     https://github.com/qinqw/curl-http
 */
namespace Qinqw\Yii\Rest;

use Yii;
use yii\rest\ActiveController;
use Qinqw\Yii\Rest\validators\SignValidator;
use Qinqw\Yii\Rest\validators\TokenValidator;

/**
 * Base Api controller , API接口控制器基类
 * 
 * @category Restful_Api_Framework
 * @package  Qinqw\Yii\Rest
 * @author   Kevin <qinqiwei@hotmail.com>
 * @date     2017-06-06
 * @license  Apache License V2
 * @link     https://github.com/qinqw/curl-http
 */
class BaseApiController extends ActiveController
{
    // api接口的返回状态，code代表错误码，message代表错误信息
    public $is_auth = false;
    public $code = 0;
    public $message = 'Succeed!';
    public $debug_stack = [];
    public $serializer = [ 
            'class' => 'yii\rest\Serializer',
            'collectionEnvelope' => 'data' 
    ];
    public $modelClass = "";
    //Access-Control-Allow-Origin:*
    public $arr_acao = [
        '*'
    ];
    //Access-Control-Allow-Methods
    public $arr_acam = [
        'POST', 
        'PUT', 
        'GET', 
        'DELETE',
        'OPTIONS'
    ];
    //Access-Control-Allow-Headers
    public $arr_acah = [
        'token', 
        'app-key',
        'content-type',
    ];

    /**
     * __construct
     * 
     * @param mixed $id 
     * @param mixed $module 
     * 
     * @return mixed 
     */
    public function __construct($id, $module)
    {
        parent::__construct($id, $module);
        if (isset(Yii::$app->params['access_control'])) {
            $access_control = Yii::$app->params['access_control'];
            if (isset($access_control['allow_origin'])
                && is_array($access_control['allow_origin'])
            ) {
                $this->arr_acao = $access_control['allow_origin'];
            }
            if (isset($access_control['allow_methods'])
                && is_array($access_control['allow_methods'])
            ) {
                $this->arr_acam = $access_control['allow_methods'];
            }
            if (isset($access_control['allow_headers'])
                && is_array($access_control['allow_headers'])
            ) {
                $this->arr_acah = $access_control['allow_headers'];
            }
        }
    }

    /**
     * Behaviors
     * 
     * @return mixed 
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['contentNegotiator']['formats']['application/xml']);
        return $behaviors;
    }

    /**
     * Actions
     * 
     * @return mixed 
     */
    public function actions() 
    {
        return [ ];
    }

    /**
     * Verbs
     * 
     * @return mixed 
     */
    public function verbs()
    {
        return [];
    }

    /**
     * BeforeAction
     *
     * @param mixed $action Action
     *
     * @return mixed 
     */
    public function beforeAction($action)
    {
        defined('YII_DEBUG') or define('YII_DEBUG', true);
        $req = Yii::$app->request;
        header('Access-Control-Allow-Origin: '.implode(',', $this->arr_acao));
        header('Access-Control-Allow-Methods: '.implode(',', $this->arr_acam));
        header('Access-Control-Allow-Headers: '.implode(',', $this->arr_acah));
        header("Access-Control-Max-Age: 86400");

        if ($req->isOptions) {
            $code = "202";
            $message = "Accepted";
            header("HTTP/1.1 ".$code." ".$message); 
            exit();
        }
        
        /** 
         * 此部分代码用户修复 Rest控制器报 Response content must not be an array. 的错误
         *
         * \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
         * return true;
         * 或者
         * if (parent::beforeAction ( $action )) {
         *     return true;
         * }
         */
        $returnValue = false;

        if (parent::beforeAction($action)) {
            $signValidator = new SignValidator();
            $returnValue = $signValidator->load()->validate();

            if (!$this->is_auth) {
                $tokenValidator = new TokenValidator();
                $returnValue = $tokenValidator->load()->validate();
            }
        }
        return $returnValue;
    }

    /**
     * AfterAction
     *
     * @param mixed $action action
     * @param mixed $result result
     * 
     * @return mixed 
     */
    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);
        // your custom code here
        $res = [ 
            'code' => $this->code,
            'message' => $this->message,
            'data' => $result
        ];
        if (YII_DEBUG) {
            $res['debug_stack'] = $this->debug_stack;
        }
       
        return $res;
    }
}
