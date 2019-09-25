<?php

namespace common\tool;

use Yii;

class RedisHelper {

    /**
     * 包装key,避免多个平台使用同一台redis,同一个库,导致数据冲突
     * @param type $key
     * @return type
     */
    static function getKey($key) {
        $ZZB_PUBCODE = Yii::$app->config->info("ZZB_PUBCODE");
        if (!$ZZB_PUBCODE) {
            $ZZB_PUBCODE="Have_No_ZZB_PUBCODE";
        }
        return $ZZB_PUBCODE . "_" . $key;
    }

    /**
     * 设置缓存并读取
     * @param $key
     * @param $callable
     * @param null $duration
     * @param null $dependency
     * @return mixed
     */

    static function getOrSet( $key, $callable, $duration = null, $dependency = null ) {
        $cache = Yii::$app->cache;
        if (is_array($key) || is_object($key)) {
            $key = serialize($key);
        }
        $key = self::getKey($key);
        $data = $cache->getOrSet( $key, $callable, $duration, $dependency);
        return $data;
    }

    /**
     * 入队
     * @param $key
     * @param $data
     * @return
     */
    static function lpushQueue($key,$data){
        $key=self::getKey($key);
        $redis = Yii::$app->redis;
        return $redis->LPUSH($key,$data);
    }

    /**
     * 出队(非阻塞)
     * @param $key
     * @return
     */
    static function rpopQueue($key){
        $key=self::getKey($key);
        $redis = Yii::$app->redis;
        return $redis->RPOP($key);
    }

    /**
     * 获取队列长度
     * @param $key
     * @return mixed
     */
    static function lenQueue($key){
        $key=self::getKey($key);
        $redis = Yii::$app->redis;
        return $redis->LLEN($key);
    }

    /**
     * 接口频率控制
     * @param $seconds
     * @param string $biaoshi
     * @param string $controllerID
     * @param string $actionID
     * @return bool
     * 隐患:特殊情况下(概率不大),EXPIRE 没有执行,会导致,接口只能访问一次,不能访问多次
     */
    static function pinglvContro_biaoshi($seconds, $biaoshi = '', $controllerID = '', $actionID='') {
        $controllerID = $controllerID?$controllerID:Yii::$app->controller->id;
        $actionID = $actionID?$actionID:Yii::$app->controller->action->id;
        $redis = Yii::$app->redis;
        $str_key = $controllerID . '_' . $actionID . '_is_call_before_' . $seconds . 's';
        if ($biaoshi) {
            $str_key.='_' . $biaoshi;
        }
        $res = $redis->incr($str_key);
        if ($res > 1) {
            return false;
        }
        $redis->EXPIRE($str_key, $seconds);
        return true;
    }

}
