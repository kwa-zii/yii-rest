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

/**
 * Site Api controller 
 * 
 * @category Restful_Api_Framework
 * @package  Controllers
 * @author   Kevin <qinqiwei@hotmail.com>
 * @license  https://www.etcp.cn/license V2
 * @link     https://www.etcp.cn
 */
class SiteController extends ActiveController
{
    public $modelClass = "";

    /**
     * BeforeAction
     * 
     * @param mixed $action 
     * 
     * @return mixed 
     */
    public function beforeAction($action)
    {
        $returnValue = false;
        $req = Yii::$app->request;
        if (parent::beforeAction($action)) {
            $controller_name = $action->controller->id;
            $action_name = $action->id;
            $headers = Yii::$app->request->headers;
            print_r($controller_name);
            print_r($action_name);
            $returnValue = true;
        }
        return $returnValue;
    }

    /**
     * ActionError
     * 
     * @return mixed 
     */
    public function actionError()
    {
        // $req = Yii::$app->request;
        // $header = $req->headers;
        // print_r($header);
        die;
    }
}
