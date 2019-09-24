<?php
defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'prod');

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/../../common/config/bootstrap.php';
require __DIR__ . '/../../api/config/bootstrap.php';

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../common/config/main.php',
    require __DIR__ . '/../../common/config/main-local.php',
    require __DIR__ . '/../../api/config/main.php',
    require __DIR__ . '/../../api/config/main-local.php'
);

function t( $message,$category='app',$params = [], $language = null)
{
    return Yii::t($category, $message, $params = [], $language = null);
}
$application = new yii\web\Application($config);
$application->language = isset(Yii::$app->session['language']) ? Yii::$app->session['language'] : 'zh-CN';
$application->run();
