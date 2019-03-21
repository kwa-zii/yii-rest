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
}
