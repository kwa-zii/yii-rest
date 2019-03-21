<?php
/**
 * A lightweight and powerful Restful interface framework based on Yii2
 * PHP version 5.6 or newer
 * 
 * @category Restful_Api_Security
 * @package  Qinqw\Yii\Rest\security
 * @author   Kevin <qinqiwei@hotmail.com>
 * @license  Apache 2.0
 * @link     https://github.com/qinqw/yii-rest
 */
namespace Qinqw\Yii\Rest\security;

/**
 * 令牌工具类，支持生成及验证令牌的相关操作
 * 
 * @category Restful_Api_Security
 * @package  Qinqw\Yii\Rest\security
 * @author   Kevin <qinqiwei@hotmail.com>
 * @date     2017-06-06
 * @license  Apache 2.0
 * @link     https://github.com/qinqw/yii-rest
 */
class Authorization
{
    const SALT = "KUHJINJIK99089";
    private $_token_ttl = 3600;
    private static $_instance;

    /**
     * GetInstance
     *
     * @return mixed 
     */
    public static function getInstance()
    {
        if (!self::$_instance instanceof self) {

            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * CreateLoginToken
     * 
     * @param mixed $userId 
     * @param mixed $timeStamp 
     * @param mixed $token_ttl 3600 
     * @param mixed $data      缓存数据
     * 
     * @return mixed 
     */
    public function createLoginToken($userId, $timeStamp, $token_ttl=3600, $data=[])
    {
        $token = md5($userId . self::SALT . $timeStamp);
        $value=json_encode(array("uid"=>$userId,"data"=>$data));
        $redis = \Yii::$app->redis;
        $this->_ttl = $token_ttl;
        $result = $redis->setex($token, $this->_ttl, $value);
        return $token;
    }

    /**
     * Remove LoginToken
     *
     * @param mixed $token Token
     *
     * @return mixed 
     */
    public function removeLoginToken($token)
    {
        $redis = \Yii::$app->redis;
        $result = $redis->del($token);
        return $result;
    }

    /**
     * Validate Token
     * 
     * @param mixed $token     Token
     * @param mixed $token_ttl token_ttl
     *
     * @return mixed 
     */
    public function validateToken($token, $token_ttl=3600)
    {
        $redis = \Yii::$app->redis;
        $value = $redis->get($token);
        if (!empty($value)) {
            $this->_ttl = $token_ttl;
            $result = $redis->setex($token, $this->_ttl, $value);
            return true;
        }
        return false;
    }

    /**
     * GetData 获取登录缓存信息
     * 
     * @param mixed $token 
     * 
     * @return mixed 
     */
    public function getData($token)
    {
        $result = [];
        $redis = \Yii::$app->redis;
        $value = $redis->get($token);
        if (!empty($value)) {
            $this->_ttl = $token_ttl;
            $result = json_decode($value, true);
        }
        return $result;
    }
}