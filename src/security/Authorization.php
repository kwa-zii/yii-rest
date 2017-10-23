<?php
/**
 * A lightweight and powerful Restful interface framework based on Yii2
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
    const LOGIN_EXPIRE = 3600;

    const SALT = "KUHJINJIK99089";

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
     * @param mixed $appKey    AppKey
     * @param mixed $userId    UserId
     * @param mixed $timeStamp TimeStamp
     *
     * @return mixed 
     */
    public function createLoginToken($appKey, $userId, $timeStamp)
    {
        $token = md5($userId . self::SALT . $timeStamp);
        $value=json_encode(array("uid"=>$userId,"app_key"=>$appKey));
        $redis = \Yii::$app->redis;
        $result = $redis->setex($token, self::LOGIN_EXPIRE, $value);
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
     * @param mixed $token  Token
     * @param mixed $appKey AppKey
     *
     * @return mixed 
     */
    public function validateToken($token, $appKey)
    {
        $redis = \Yii::$app->redis;
        $value = $redis->get($token);
        if (!empty($value)) {
            $result = $redis->setex($token, self::LOGIN_EXPIRE, $value);
            return true;
        }
        return false;
    }
}