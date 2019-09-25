<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2019/3/4
 * Time: 16:11
 */

namespace common\tool;


use Yii;

class SignTool
{

    /**
     * 签名算法
     * @param string $secret    签名秘钥
     * @param array $data       待签名的数组
     * @return string           签名结果
     */
    static function Sign(string $secret, array $data)
    {
        unset($data['sign']);
        ksort($data);
        $params = http_build_query($data);
        $sign = md5($params . $secret);
        return $sign;
    }
    /**
     * 数组加签
     * @param string $secret    签名秘钥
     * @param array $data       待签名的数组
     * @return array            签名结果
     */
    static function addSign(string $secret, array $data)
    {
        unset($data['sign']);
        $data['timestamp']=time();
        ksort($data);
        $params = http_build_query($data);
        $sign = md5($params . $secret);
        $data['sign']=$sign;
        return $data;
    }

    /**
     * 验证签名
     * @param string $secret    签名秘钥
     * @param array $data       待检验的数组
     * @param string $err_msg   错误信息
     * @return bool             验证结果
     */
    static function verifySign(string $secret, array $data, &$err_msg="") {
        if(!Yii::$app->params['is_verify_sign']){
            return true;
        }

        // 验证参数中是否有签名
        if (!isset($data['sign']) || !$data['sign']) {
            $err_msg= '发送的数据,签名不存在';
            return false;
        }
        if (!isset($data['timestamp']) || !$data['timestamp']) {
            $err_msg=  '发送的数据,参数不合法';
            return false;
        }
        // 验证请求， 10分钟失效
        if (time() - $data['timestamp'] > 600) {
            $err_msg=  '验证失效,请重新发送请求';
            return false;
        }
        $sign = $data['sign'];
        unset($data['sign']);
        ksort($data);
        $params = http_build_query($data);
        // $secret是通过key在api的数据库中查询得到
        $sign2 = md5($params . $secret);
        if ($sign == $sign2) {
            $err_msg="";
            return true;
        } else {
            $err_msg=  '签名失败';
            return false;
        }
    }
}