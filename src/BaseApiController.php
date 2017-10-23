<?php
/**
 * A lightweight and powerful Restful interface framework based on Yii2
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
use Qinqw\Yii\Rest\validators\RequestValidator;
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
    public $serializer = [ 
            'class' => 'yii\rest\Serializer',
            'collectionEnvelope' => 'data' 
    ];
    public $modelClass = "";

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
        $req = Yii::$app->request;
        if ($req->isOptions) {
            //允许跨域请求处理
            header("Access-Control-Allow-Origin:*");
            header("Access-Control-Allow-Methods: POST, PUT, GET, DELETE, OPTIONS");
            header("Access-Control-Allow-Headers: token, app-key, content-type, etcp-base");
            header("Access-Control-Max-Age: 86400");
            
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
            $requestValidator = new RequestValidator();
            $returnValue = $requestValidator->load()->validate();

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
        $result = [ 
                'code' => $this->code,
                'message' => $this->message,
                'data' => $result 
        ];
        return $result;
    }
}
