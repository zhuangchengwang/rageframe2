<?php
namespace console\controllers;

use common\tool\Logging;
use common\tool\RedisHelper;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

/**
 * 同步到数据库
 * @package console\controllers
 */
class SynController extends Controller
{
    /**
     *同步操作日志到数据库common_record_change_log
     */
    public function actionIndex()
    {
        $datatime=date('Y-m-d H:i:s');
        set_time_limit(0);
        $key="queue_user_operate_log";
        $count=0;
        $arr=[];
        while (RedisHelper::lenQueue($key)>0){
            $count++;
            if($count>500){
                break;
            }
            $data=RedisHelper::rpopQueue($key);
            $data=json_decode($data,true);
            $data['changedAttributes']=json_encode($data['changedAttributes'],JSON_UNESCAPED_UNICODE);
            $arr[]=$data;
        }
        //栏位
        $comlum = ['uuid', 'event', 'application', 'operate_id', 'user_name', 'operatename', 'short_route', 'route', 'table', 'table_record_id', 'changedAttributes', 'ip_source', 'operate_date', 'created_at'];
        $res = Yii::$app->db->createCommand()->batchInsert('common_record_change_log', $comlum, $arr)->execute();
        $this->stdout($datatime . ' --- '.$res . "执行" . PHP_EOL);
    }
    /**
     *同步操作日志到数据库api_log
     */
    public function actionApiLog()
    {
        $datatime=date('Y-m-d H:i:s');
        set_time_limit(0);
        $key="queue_user_api_log";
        $count=0;
        $arr=[];
        while (RedisHelper::lenQueue($key)>0){
            $count++;
            if($count>500){
                break;
            }
            $data=RedisHelper::rpopQueue($key);
            $data=json_decode($data,true);
            $arr[]=$data;
        }
        //栏位
        $comlum =[
            'member_id',
            'method',
            'module',
            'controller',
            'action',
            'url',
            'get_data' ,
            'post_data' ,
            'cookie_data' ,
            'ip' ,
            'req_id',
            'error_code',
            'error_msg',
            'error_data',
            'status' ,
            'created_at',
            'updated_at',
        ];
        $res = Yii::$app->db->createCommand()->batchInsert('api_log', $comlum, $arr)->execute();
        $this->stdout($datatime. ' --- '.$res . "执行" . PHP_EOL);

    }
}