<?php

namespace common\tool;

//设置时区
ini_set('date.timezone', 'Asia/Shanghai');

//定义日志根目录(根据需要修改)
defined('LOG_ROOT') || define('LOG_ROOT', dirname(dirname(dirname(__FILE__))) . '/data');

class Logging {

    public static function getRoot() {
        return LOG_ROOT;
    }

    /**
     * 记录日志
     * @param type $message
     * @param type $filename
     * @param type $isadd
     */
    public static function log($message, $format = 'json', $isadd = 1) {

        $txt = array();
        $txt['write_log_timestamp'] = time();
        $txt['write_log_data'] = $message;
        $filename = 'Log/' . date("Y") . '/' . date("m") . '/' . date('d') . '/' . date('H') . '.log.txt';
        //格式化message
        $content = '[' . date('Y-m-d H:i:s') . ']' . PHP_EOL;
        if ($format == 'array') {

            $content .= self::recursion($txt);
        } else if ($format == 'json') {
            $json = json_encode($txt, JSON_UNESCAPED_UNICODE);
            $content .= self::_format_json($json);
        } else {
            $content .= $message;
        }
        unset($txt);
        $content .= PHP_EOL;
        self::write($content, $filename, $isadd);
    }

    /**
     * 把字符串,写入文本
     * @param type $msg
     * @param type $filename
     * @param type $isadd
     */
    public static function writeString($txt, $filename, $isadd = 0) {
        self::write($txt, $filename, $isadd);
    }

    /**
     * 把数组或json美化成json字符串,写入文本
     * @param type $msg
     * @param type $filename
     * @param type $isadd
     */
    public static function writePrettyJson($arr_or_json, $filename, $isadd = 0) {
        if (is_array($arr_or_json)) {
            $json = json_encode($arr_or_json, JSON_UNESCAPED_UNICODE);
        } else {
            $json = $arr_or_json;
        }
        $content = '';
        $content .=self::_format_json($json);
        $content .= PHP_EOL;
        self::write($content, $filename, $isadd);
    }

    /**
     * 把数组美化成字符串,写入文本
     * @param type $msg
     * @param type $filename
     * @param type $isadd
     */
    public static function writePrettyArr($arr, $filename, $isadd = 0) {
        if (!is_array($arr))
            exit('no array');
        $content = '';
        $content .= self::recursion($arr);
        $content .= PHP_EOL;
        self::write($content, $filename, $isadd);
    }

    /**
     * 把内容写入到文件
     * @param type $msg
     * @param type $filename
     * @param type $isadd
     */
    private static function write($msg, $filename, $isadd = 0) {
        $allName = self::createFloderByFilename($filename);
        $file_ex=file_exists($allName);
        if ($isadd) {


            @file_put_contents($allName, $msg, FILE_APPEND);
        } else{
            @file_put_contents($allName, $msg);
        }
        if(!$file_ex){
            if(file_exists($allName)){
                @chmod($allName, 0777);
            }
        }
    }


    /**
     * file_exists() 函数检查文件或目录是否存在。如果指定的文件或目录存在则返回 true，否则返回 false。
     * @param $filename
     * @return bool
     */
    public static function FileExists($filename){
        $allName = self::createFloderByFilename($filename);
        $file_ex=file_exists($allName);
        return $file_ex;
    }
    /*
     * 根据一个带路径的文件名,创建他所在的目录
     */

    private static function createFloderByFilename($filename) {
        //组合带路径的文件名称
        $allName = LOG_ROOT . '/' . $filename;
        $allName = str_replace("\\", "/", $allName);
        $allName = str_replace("//", "/", $allName);
        //判断文件所在目录是否存在,不存在就递归创建目录
        $floder = dirname($allName);
        if (!is_dir($floder)) {
            self::createTree($floder);
        }

        return $allName;
    }

    /**
     * 递归创建目录
     * @param type $dir 一个正常的目录路径
     * @return type
     */
    private static function createTree($dir) {
//判断$dir是不是目录
        if (is_dir($dir))
            return;
//如果$dir的上一级目录不存在,就创建
        if (!is_dir(dirname($dir))) {
            self::createTree(dirname($dir));
        }
        @mkdir($dir, 0777);
        @chmod($dir, 0777);
    }

    /**
     * 递归把数组拼接成可视化字符串
     * @param type $array
     * @param type $r
     * @return string
     */
    private static function recursion($array, $r = 1) {
        $content = '';
        $nbsp = $r * 4;
        $kongge = $kongge2 = chr(32);
        $k = chr(32);
        for ($i = 0; $i < $nbsp; $i++) {
            $kongge .= $k;
            if ($i < $nbsp - 4) {
                $kongge2 .= $k;
            }
        }
        if (is_array($array)) {
            $content .= "[" . PHP_EOL . "";
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $content = $content . $kongge . '"' . $key . '"=>';
                    $content .= self::recursion($value, $r + 1);
                } else {
                    $content .= $kongge . sprintf(" '%s' => '%s' ," . PHP_EOL . "", $key, $value);
                }
            }
            $content .= $kongge2 . "]," . PHP_EOL . "";
        } else {
            return $array;
        }


        return $content;
    }

    /**
     * json 文本美化
     */
    private static function _format_json($json, $html = false) {
        $tabcount = 0;
        $result = '';
        $inquote = false;
        $ignorenext = false;
        if ($html) {
            $tab = "   ";
            $newline = "<br/>";
        } else {
            $tab = "\t";
            $newline = PHP_EOL;
        }
        for ($i = 0; $i < strlen($json); $i++) {
            $char = $json[$i];
            if ($ignorenext) {
                $result .= $char;
                $ignorenext = false;
            } else {
                switch ($char) {
                    case '{':
                        $tabcount++;
                        $result .= $char . $newline . str_repeat($tab, $tabcount);
                        break;
                    case '}':
                        $tabcount--;
                        $result = trim($result) . $newline . str_repeat($tab, $tabcount) . $char;
                        break;
                    case ',':
                        $result .= $char . $newline . str_repeat($tab, $tabcount);
                        break;
                    case '"':
                        $inquote = !$inquote;
                        $result .= $char;
                        break;
                    case '\\':
                        if ($inquote)
                            $ignorenext = true;
                        $result .= $char;
                        break;
                    default:
                        $result .= $char;
                }
            }
        }
        return $result;
    }

}
