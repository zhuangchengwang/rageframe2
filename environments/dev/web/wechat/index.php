<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/../../common/config/bootstrap.php';
require __DIR__ . '/../../wechat/config/bootstrap.php';

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../common/config/main.php',
    require __DIR__ . '/../../common/config/main-local.php',
    require __DIR__ . '/../../wechat/config/main.php',
    require __DIR__ . '/../../wechat/config/main-local.php'
);

/**
 * 打印
 *
 * @param $array
 */
function p(...$array)
{
    echo "<pre>";

    if (count($array) == 1) {
        print_r($array[0]);
    } else {
        print_r($array);
    }

    echo '</pre>';
}
function t( $message,$category='app',$params = [], $language = null)
{
    return Yii::t($category, $message, $params = [], $language = null);
}
$application = new yii\web\Application($config);
$application->language = isset(Yii::$app->session['language']) ? Yii::$app->session['language'] : 'zh-CN';
$application->run();
