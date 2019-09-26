<?php

namespace common\components;


use common\tool\Logging;
use common\tool\RedisHelper;
use Faker\Provider\Uuid;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class UserOperateLog
{
    //操作说明
    private static $operate_arr=[];
    //获取操作名称
    protected static function getOperateName($route){
        if(!self::$operate_arr){
            $key=RedisHelper::getKey("getOperateNameForUserLog");
            self::$operate_arr =  RedisHelper::getOrSet($key, function (){
                $res = Yii::$app->db->createCommand("select name,title from common_auth_item")->queryAll();
                $res=ArrayHelper::map($res,'name','title');
                return $res;
            },300);

        }
        return self::$operate_arr[$route]?self::$operate_arr[$route]:"未知";
    }

    /**
     * 插入新记录
     * @param $event
     * @return bool
     */
    public static function writei($event){
        return true;
    }

    /**
     * 更新事件
     * @param $event
     * @return bool
     */
    public static function writeu($event){
        $canbeUpdate = ['sys_manager',"sys_config",'sys_config_cate','sys_menu'];
        $canbeUpdate = Yii::$app->params['operateLog']['canbeUpdate']?array_merge(Yii::$app->params['operateLog']['canbeUpdate'],$canbeUpdate):$canbeUpdate;
        if(in_array($event->sender->tableName(),$canbeUpdate)){
            if(!empty($event->sender->Attributes)) {
                global $REQUESTUUID;
                $short_route = Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
                $short_route =str_replace("app-backend/",'',$short_route);
                $short_route =str_replace("app-api/",'',$short_route);
                $changedAttributes=[];
                if($event->changedAttributes&&$event->sender->Attributes){
                    foreach ($event->changedAttributes as $k=>$v){
                        $changedAttributes[$k]=[
                            'old'=>$v,
                            'new'=>$event->sender->Attributes[$k]
                        ];
                    }
                }
                //表的主键
                $key  = $event->sender->primaryKey()[0];
                $data = [
                    'uuid'=>$REQUESTUUID?$REQUESTUUID:"",
                    //触发事件
                    'event'=>$event->name,
                    //有backend,api,console
                    'application'=>Yii::$app->id,
                    //获取操作用户兼容三种应用//有backend,api,console
                    'operate_id' => @self::getOperateId(),
                    'user_name' => @self::getUserName(),
                    //暂不兼容api的说明
                    'operatename' => @self::getOperateName($short_route),
                    'short_route'=>$short_route,
                    'route' => @self::getAllRoute(),
                    'table' => $event->sender->tableName(),
                    'table_record_id'=>$event->sender->$key,
                    'changedAttributes'=>$changedAttributes,
                    'ip_source' => @self::getIpResource(),
                    'operate_date'=>date("Y-m-d"),
                    'created_at' => date("Y-m-d H:i:s"),
                ];
                self::writeData($data);
            }
        }
        return true;
    }



    /**
     * 哪些表删除可以被监听
     * @var array
     */
    protected static $canbeDel=['xh_order','xh_borrow','xh_xuzu','xh_huankuan_record','sys_manager',"sys_config",'sys_config_cate','fy_bankcard','sys_menu'];
    /**
     * 删除事件
     */
    public static function writed($event)
    {
        $canbeDel = ['sys_manager',"sys_config",'sys_config_cate','sys_menu'];
        $canbeDel = Yii::$app->params['operateLog']['canbeDel']?array_merge(Yii::$app->params['operateLog']['canbeDel'],$canbeDel):$canbeDel;
        if(in_array($event->sender->tableName(),$canbeDel)){
            if(!empty($event->sender->Attributes)) {
                global $REQUESTUUID;
                $short_route = Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
                $short_route =str_replace("app-backend/",'',$short_route);
                $short_route =str_replace("app-api/",'',$short_route);
                $changedAttributes=$event->sender->Attributes;
                //表的主键
                $key  = $event->sender->primaryKey()[0];
                $data = [
                    'uuid'=>$REQUESTUUID?$REQUESTUUID:"",
                    //触发事件
                    'event'=>$event->name,
                    //有backend,api,console
                    'application'=>Yii::$app->id,
                    //获取操作用户兼容三种应用//有backend,api,console
                    'operate_id' => @self::getOperateId(),
                    'user_name' => @self::getUserName(),
                    //暂不兼容api的说明
                    'operatename' => @self::getOperateName($short_route),
                    'short_route'=>$short_route,
                    'route' => @self::getAllRoute(),
                    'table' => $event->sender->tableName(),
                    'table_record_id'=>$event->sender->$key,
                    'changedAttributes'=>$changedAttributes,
                    'ip_source' => @self::getIpResource(),
                    'operate_date'=>date("Y-m-d"),
                    'created_at' => date("Y-m-d H:i:s"),
                ];

                self::writeData($data);
            }
        }
        return true;
    }

    /**
     * 写入数据
     * @param $data
     */
    protected static function writeData($data){
        $string=@json_encode($data,JSON_UNESCAPED_UNICODE);
        @RedisHelper::lpushQueue("queue_user_operate_log",$string);
       // $res2 =  @Yii::$app->db->createCommand()->insert("xh_record_change_log", $data)->execute();
        //Logging::writePrettyJson($data,'/UserOperateLog/'.$data['operate_date'].'.log',1);
    }
    /**
     * 获取操作员id
     * @return string
     */
    protected static function getOperateId(){
        if(Yii::$app->id=="app-backend"){
            return isset(Yii::$app->user->identity->id)?Yii::$app->user->identity->id:"";
        }
        if(Yii::$app->id=="app-api"){
            return isset(Yii::$app->user->identity->user_id)?Yii::$app->user->identity->user_id:"";
        }
        if(Yii::$app->id=="app-console"){
            return "console";
        }
    }

    /**
     * 获取操作员姓名
     * @return string
     */
    protected static function getUserName(){
        if(Yii::$app->id=="app-backend"){
            return isset(Yii::$app->user->identity->username)?Yii::$app->user->identity->username:"";
        }
        if(Yii::$app->id=="app-api"){
            //无法使用Yii::$app->user->identity 去获取,这个字段也不是很重要就是了
            return "";
        }
        if(Yii::$app->id=="app-console"){
            return "console";
        }
    }
    /**
     * 获取操作详细地址
     * @return string
     */
    protected static function getAllRoute(){
        if(Yii::$app->id=="app-backend"){
            return urldecode(Url::to());
        }
        if(Yii::$app->id=="app-api"){
            return urldecode(Url::to());
        }
        if(Yii::$app->id=="app-console"){
            return Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
        }
    }
    /**
     * 获取操作IP
     * @return string
     */
    protected static function getIpResource(){
        if(Yii::$app->id=="app-backend"){
            return isset(Yii::$app->request->userIP)?Yii::$app->request->userIP:"";
        }
        if(Yii::$app->id=="app-api"){
            return isset(Yii::$app->request->userIP)?Yii::$app->request->userIP:"";
        }
        if(Yii::$app->id=="app-console"){
            return "127.0.0.1";
        }
    }

}