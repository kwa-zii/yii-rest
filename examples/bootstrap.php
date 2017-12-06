<?php
/**
 * A lightweight and powerful Restful interface framework based on Yii2
 *
 * @category Restful_Api_Examples
 * @package  Qinqw\Yii\Rest\examples
 * @author   Kevin <qinqiwei@hotmail.com>
 * @license  Apache 2.0
 * @link     https://github.com/qinqw/yii-rest
 */

spl_autoload_register( 
    function ($class) {
        // project-specific namespace prefix
        $prefix = 'Qinqw\\Yii\\Rest';

        // base directory for the namespace prefix
        $base_dir = dirname(__DIR__) . '/src/';

        // does the class use the namespace prefix?
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            // no, move to the next registered autoloader
            return;
        }

        // get the relative class name
        $relative_class = substr($class, $len);

        // replace the namespace prefix with the base directory, replace namespace
        // separators with directory separators in the relative class name, append
        // with .php
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

        // if the file exists, require it
        if (file_exists($file)) {
            //require $file;
            include $file;
        }
    }
);
