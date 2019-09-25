<?php

/*
 * xml和数组互转
 *
 */

namespace common\tool;

/**
 * Description of ArrayXML
 *
 * @author chengwangzhuang
 */
class ArrayXML {

    /**
     * 特殊数组转xml
     * $a = [
     * 'notice'=>[
     *  ['a'=>1,'b'=>2],
     * ['a'=>1,'b'=>2],
     * ['a'=>1,'b'=>2],
     * ]
     * ]
     * 这种以键是数值的数组 会被转成多个 notice XML节点
     * @param type $data
     * @param type $key
     * @return type
     */
    protected static function specital2xml($data, $key) {
        $str = "";
        foreach ($data as $k => $val) {
            if (is_array($val)) {
                $child = self::specital2xml($val, $k);
                $str.= "<$key>$child</$key>";
            } else {

                $str.= "<$k>$val</$k>";
            }
        }
        return $str;
    }

    /**
     * 数组转xml
     * @param type $data
     * @param type $k
     * @param type $root
     * @return string
     */
    public static function arrToXml($data, $k = false, $root = true) {
        $str = "";
        if ($root)
            $str .= "<?xml version=\"1.0\" encoding=\"UTF-8\"?><package>";
        foreach ($data as $key => $val) {
            if (is_array($val)) {
                if (isset($val[0]) && is_array($val[0])) {
                    $child = self::specital2xml($val, $key);
                    $str .= $child;
                } else {
                    $child = self::arrToXml($val, false, false);
                    $str .= "<$key>$child</$key>";
                }
            } else {
                $str.= "<$key>$val</$key>";
            }
        }
        if ($root)
            $str .= "</package>";
        return $str;
    }

    /**
     * 将xml转换为数组
     * @param string $xml:xml文件或字符串
     * @return array
     */
    public static function xmlToArray($xml) {
        //考虑到xml文档中可能会包含<![CDATA[]]>标签，第三个参数设置为LIBXML_NOCDATA
        if (file_exists($xml)) {
            libxml_disable_entity_loader(false);
            $xml_string = simplexml_load_file($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        } else {
            libxml_disable_entity_loader(true);
            $xml_string = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        }
        $result = json_decode(json_encode($xml_string), true);
        return $result;
    }

}
