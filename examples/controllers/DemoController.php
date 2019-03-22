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
namespace controllers;

use Yii;
use yii\rest\ActiveController;
use Qinqw\Yii\Rest\BaseApiController;
use Qinqw\Yii\Rest\validators\ParamsValidator;

/**
 * Demo Api controller
 * PHP version 5.6 or newer
 * 
 * @category Restful_Api_Framework
 * @package  Backend\controllers
 * @author   Kevin <qinqiwei@hotmail.com>
 * @date     2017-10-23
 * @license  https://www.etcp.cn/license V2
 * @link     https://www.etcp.cn
 */
class DemoController extends BaseApiController
{
    // public $modelClass = "";
    public $is_auth = true;

    /**
     * IndexAction
     * 
     * @return mixed 
     */
    public function actionError()
    {
        echo "ccc";die;
    }

    /**
     * IndexAction
     * 
     * @return mixed 
     */
    public function actionTest()
    {
        $req = Yii::$app->request;

        $this->code = 0;
        $this->message = 'OK';

        $data = $req->post();

        return $data;
    }

    /**
     * ActionDemo
     * 
     * @return mixed 
     */
    public function actionDemo()
    {
        $method_allowed = false;
        $req = Yii::$app->request;
        if ($req->isGet) {
            $params = $req->get();
            $method_allowed = true;
        } elseif ($req->isPost) {
            //$params = $req->post();
            $params = $req->bodyParams;
            $method_allowed = true;
        } else {
            $this->code = 405;
            $this->message = "Method Not Allowed";
            $data = "Method GET/POST is required";
        }

        if ($method_allowed == true) {
            $this->code = 0;
            $this->message = "OK";

            $rule = [
                'username'  =>'require',
                'password'  =>'require'
            ];
            $msg = [
                //'username.require'  =>'用户名[username]不能为空'
            ];
            $validate = new ParamsValidator($rule, $msg);
            $valid_result = $validate->check($params);

            if (!$valid_result) {
                $data  = ["notification"=>$validate->getError()];
                $this->code    = "400";
                $this->message = "Bad Request";
            } else {

                $this->code = 200;
                $this->message = 'OK';
                $this->debug_stack = [];
                $data = $params;
            }
        }
        return $data;
    }
}
