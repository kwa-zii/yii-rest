<?php
/**
 * A lightweight and powerful Restful interface framework based on Yii2
 *
 * @category Restful_Api_Validator
 * @package  Qinqw\Yii\Rest\validators
 * @author   Kevin <qinqiwei@hotmail.com>
 * @license  Apache 2.0
 * @link     https://github.com/qinqw/yii-rest
 */

namespace Qinqw\Yii\Rest\validators;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Exception;
use yii\web\HttpException;
use yii\web\Request;

/**
 * Request Validator 参数验证器
 * 
 * @category Restful_Api_Validator
 * @package  Qinqw\Yii\Rest\validators
 * @author   Kevin <qinqiwei@hotmail.com>
 * @date     2017-06-06
 * @license  Apache 2.0
 * @link     https://github.com/qinqw/yii-rest
 */
class RequestValidator
{
    public static $globalParams = null;
    public $attributes = [
        'app_key',
        'time_stamp',
        'os_type'
    ];

    /**
     * Validate
     *
     * @return mixed 
     */
    public function validate()
    {
        //return true;
        // 此处实现请求的合法性校验，true代表请求合法
        // 校验参数
        if (!Yii::$app->params ['enable_sign']) {
            return true;
        }

        /**
         * 安全参数智能通过header传入 注释调从post消息体内获取安全参数的代码
         */
        $requestPost = Yii::$app->request->post();
        foreach ($requestPost as $key => $postValue) {
            if (is_array($postValue)) {
                foreach ($postValue as $subKey => $subValue) {
                    if (is_array($subValue)) {
                        foreach ($subValue as $indexKey => $detailValue) {
                            $requestPost [$key . '[' . $subKey . ']' . '[' . $indexKey . ']'] = $detailValue;
                        }
                    } else {
                        $requestPost [$key . '[' . $subKey . ']'] = $subValue;
                    }
                }
                unset($requestPost[$key]);
            }
        }

        $validateParams = array_merge($requestPost, self::$globalParams);
        foreach ($validateParams as $key => $param) {
            if ($param == null || $param == '') {
                unset($validateParams[$key]);
            }
        }
        //var_dump($validateParams);die;
        if (!isset($validateParams['sign'])) {
            throw new HttpException(400, "sign is required", 400);
        }

        $signSubmit = $validateParams['sign'];
        unset($validateParams['sign']);
        ksort($validateParams);
        $validateParams ['secret'] = Yii::$app->params ['app_secret'];
        
        $signStringArray = [ ];
        foreach ($validateParams as $key => $validate) {
            $signStringArray [] = $key . '=' . $validate;
        }
        
        $signOfRequest = sha1(implode('&', $signStringArray));
        
        if ($signSubmit === $signOfRequest) {
            return true;
        } else {
            $stringOfSign = implode('&', $signStringArray);
            throw new HttpException(403, "sign is  incorrect, request is not legal!", 403);
            //throw new Exception ( 'sign is not correct, request is not legal!', 4034 );
        }
    }
    
    /**
     * Load
     *
     * @return mixed 
     */
    public function load()
    {
        $headParams = Yii::$app->request->getHeaders()->toArray();
        if (! isset($headParams['etcp-base'])||(!is_array($headParams['etcp-base']))||empty($headParams['etcp-base'])) {
            if (! Yii::$app->params ['enable_sign']) {
                return $this;
            } else {
                //throw new HttpException (400,"Request is not legal!, 'etcp-base' can not be found ",400);
                throw new HttpException(400, "Request is not legal!, Missing 'etcp-base' in the header", 400);
            }
        }
        $appHeadParams = json_decode(array_shift($headParams['etcp-base']), true);
        //var_dump($appHeadParams);die;
        foreach ($this->attributes as $attribute) {
            if (isset($appHeadParams[$attribute])) {
                self::$globalParams [$attribute] = $appHeadParams [$attribute];
            } else {
                self::$globalParams [$attribute] = '';
            }
        }
        return $this;
    }

    /**
     * GetParams
     *
     * @return mixed 
     */
    public function getParams()
    {
        return self::$globalParams;
    }
}
