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
class SignValidator
{
    private $_is_valid_sign = false;
    public static $globalParams = null;
    public $attributes = [
        'app-key',
        'os-type',
        'timestamp'
    ];

    /**
     * Validate
     *
     * @return mixed 
     */
    public function validate()
    {
        // 此处实现请求的合法性校验，true代表请求合法
        // 校验参数
        if ($this->_is_valid_sign == false) {
            return true;
        }

        /**
         * 安全参数只能通过header传入 注释调从post消息体内获取安全参数的代码
         */
        $request = Yii::$app->request;
        if ($request->isGet) {
            $requestParams = $request->get();
        } else {
            $requestParams = $request->bodyParams;
        }
        
        foreach ($requestParams as $key => $postValue) {
            if (is_array($postValue)) {
                foreach ($postValue as $subKey => $subValue) {
                    if (is_array($subValue)) {
                        foreach ($subValue as $indexKey => $detailValue) {
                            $requestParams [$key . '[' . $subKey . ']' . '[' . $indexKey . ']'] = $detailValue;
                        }
                    } else {
                        $requestParams [$key . '[' . $subKey . ']'] = $subValue;
                    }
                }
                unset($requestParams[$key]);
            }
        }

        $validateParams = array_merge($requestParams, self::$globalParams);
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
        // 只有在Yii参数定义中显式的声明 enable_sign 为 true，启用签名验证。
        if (isset(Yii::$app->params['enable_sign'])) {
            if (Yii::$app->params['enable_sign'] == true) {
                $this->_is_valid_sign = true;
            }
        }

        if ($this->_is_valid_sign == true) {
            $headParams = Yii::$app->request->getHeaders()->toArray();
            if (!isset($headParams['sign-base'])) {
                //throw new HttpException (400,"Request is not legal!, 'etcp-base' can not be found ",400);
                throw new HttpException(400, "Request is not legal!, Missing 'sign-base' in the header", 400);
            }

            $appHeadParams = json_decode(array_shift($headParams['sign-base']), true);
            //var_dump($appHeadParams);die;
            foreach ($this->attributes as $attribute) {
                if (isset($appHeadParams[$attribute])) {
                    self::$globalParams [$attribute] = $appHeadParams [$attribute];
                } else {
                    self::$globalParams [$attribute] = '';
                }
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
