<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2019/5/7
 * Time: 20:20
 */

namespace common\tool;


class VirtualTable
{
    private $_from=[];
    public static function from(array $arr){
        if(!is_array($arr)){
            throw new \Exception("not a array");
        }
        return new VirtualTable($arr);
    }
    private function __construct(array $arr){
        $this->_from = $arr;
    }

    /**
     * 选择某些列的数值
     * @param $select
     * @param bool $whenOneColOneDimensional  当只选择一列时,是否只返回一维数组
     * @return $this
     */
    public  function selectColumn($select,$iskeepkey=false,$whenOneColOneDimensional=false){
        $data = [];
        $type=self::jugeType($select);
        if($type=="array"){
            $count = count($select);
        }
        foreach ($this->_from as $key=>$v){
            if($type=="array"){
               if($count>1){
                   $d = [];
                   foreach ($select as &$v2){
                       $d[$v2]=$v[$v2];
                   }
                   if($iskeepkey){
                       $data[$key]=$d;
                   }else{
                       $data[]=$d;
                   }
               }else{
                   if($whenOneColOneDimensional){
                       if($iskeepkey){
                           $data[$key]=$v[$select[0]];
                       }else{
                           $data[]=$v[$select[0]];
                       }

                   }else{
                       if($iskeepkey){
                           $data[$key]=[$select[0]=>$v[$select[0]]];
                       }else{
                           $data[]=[$select[0]=>$v[$select[0]]];
                       }

                   }
               }
            }elseif ($type=="string"){
                if($whenOneColOneDimensional){
                    if($iskeepkey){
                        $data[$key]=$v[$select];
                    }else{
                        $data[]=$v[$select];
                    }

                }else{
                    if($iskeepkey){
                        $data[$key]=[$select=>$v[$select]];
                    }else{
                        $data[]=[$select=>$v[$select]];
                    }
                }
            }elseif($type=="callable"){
                $data[$key]=$select($v);
            }
        }
        $this->_from = $data;
        unset($data);
        return $this;
    }

    /**
     * 新增一列数值
     * @param $columnName
     * @param $value
     * @return $this
     */
    public function addColumn($columnName,$value){
        $type=self::jugeType($value);
        foreach ($this->_from as &$v){
            if($type=="callable"){
                $v[$columnName]=$value($v);
            }else{
                $v[$columnName]=$value;
            }
        }
        return $this;
    }

    /**
     * 移除某些列
     * @param $columnNames
     * @return $this
     */
    public function removeColumn($columnNames){
        $type=self::jugeType($columnNames);
        foreach ($this->_from as $key=>$v){
           if($type=='array'){
               foreach ($columnNames as $v2){
                   unset($this->_from[$key][$v2]);
               }
           }elseif ($type=='string'){
               unset($this->_from[$key][$columnNames]);
           }
        }
        return $this;
    }

    /**
     * 匿名函数返回true则保留那一行
     * @param callable $callable $v是数组的每个元素
     * @return $this
     */
    public function where(callable $callable){
        foreach ($this->_from as $key=>$v){
             if(!$callable($v)){
                 unset($this->_from[$key]);
             }
        }
        return $this;
    }

    /**
     * 根据一个匿名函数修改数组中记录的值
     * @param callable $callable($v) $v是数组的每个元素
     * @return $this
     */
    public function changeItemValue(callable $callable){
        foreach ($this->_from as $key=>$v){
            $this->_from[$key]=$callable($v);
        }
        return $this;
    }

    /**
     * 以某一列的值为二维数组重建索引
     * 注意:如果键相同,则会出现后面元素覆盖前面的情况
     * @param $columnName
     * @return $this
     */
    public function IndexByColumn($columnName){
        $tmp = [];
        foreach ($this->_from as $key=>$v){
            $tmp[$v[$columnName]]=$v;
        }
        $this->_from = $tmp;
        unset($tmp);
        return $this;
    }

    /**
     * 列映射
     * @param $from
     * @param $to
     * @return $this
     */
    public function map($from,$to){
        $tmp = [];
        foreach ($this->_from as $key=>$v){
            $tmp[$v[$from]]=$v[$to];
        }
        $this->_from = $tmp;
        unset($tmp);
        return $this;
    }

    /**
     * 获取最后结果
     * @return array
     */
    public function getVar(){
        return $this->_from;
    }
    /**
     * 获取当前的二维数组
     * @return array
     */
    public function getVtable(&$arr){
        $arr = $this->_from;
        return $this;
    }

    /**
     * 获取当前二维数组的个数
     * @return array
     */
    public function getCount(&$count){
        $count = count($this->_from);
        return $this;
    }
    /**
     * 求$key列之和
     * @return array
     */
    public function getSum($key,&$sum){
        if(is_array($key)){
            $sum = [];
            foreach ($this->_from as $v){
                foreach ($key as $k){
                    $sum[$k]+=$v[$k];
                }
            }
        }else{
            $sum = 0;
            foreach ($this->_from as $v){
                $sum+=$v[$key];
            }
        }
        return $this;
    }

    /**
     * 求$key列之和
     * @return array
     */
    public function getValue(&$arr,$key){
        if(is_array($key)){
            foreach ($this->_from as $v){
                foreach ($key as $k){

                }
            }
        }else{
            $sum = 0;
            foreach ($this->_from as $v){
                $sum+=$v[$key];
            }
        }
        return $this;
    }

    /**
     * 求$key列之和
     * @return array
     */
    public function limit($limit){
        if($limit > 0){
            $arr = [];
            $i = 0;
             foreach ($this->_from as $v){
                 $i++;
                 if($i <= $limit){
                     $arr[] = $v;
                 }
             }
            $this->_from = $arr;
        }
        return $this;
    }

    /**
     * 打印数组
     * @return $this
     */
    public function printVar(){
        $this->p($this->_from);
        return $this;
    }
    public  function p($data,$title=""){
        static $count=0;
        $count++;
        if($count==1){
            echo '<pre>';
        }
        if($title){
            echo date('Y-m-d H:i:s')."-----------------".$title.'<br>';
        }
        print_r($data);
        echo "<br><br>";
    }
    /**
     * 根据某一列对二维数组进行排序(多字段排序)
     * orderBy('id',SORT_ASC,'name',SORT_ASC,'age',SORT_DESC);
     * @param $column
     * @param string $sort
     * @param bool $isKeepkey
     * @return $this
     */
    public function orderBy() {
        $args = func_get_args();
        if(empty($args)){
            return null;
        }
        $arr = $this->_from;
        foreach($args as $key => $field){
            if(is_string($field)){
                $temp = array();
                foreach($arr as $index=> $val){
                    $temp[$index] = $val[$field];
                }
                $args[$key] = $temp;
            }
        }
        $args[] = &$arr;//引用值
        call_user_func_array('array_multisort',$args);
        $this->_from = array_pop($args);
        unset($arr);
        return $this;
    }

    /**
     * @param string $prop
     * @param callable|null $having  function($v){return $v['a']>0;}  类似sql 的having条件
     * @return $this
     */
   public function groupByHaving( string $prop, callable $having=null){
        $a = $this->_from;
        if($having==null){
            $this->_from=array_reduce($a, function ($acc, $obj) use ($prop) {
                $key = $obj[$prop];
                if (!array_key_exists($key, $acc)) {
                    $acc[$key] = [];
                }
                array_push($acc[$key], $obj);
                return $acc;
            }, []);
            return $this;
        }
        $this->_from=array_reduce($a, function ($acc, $obj) use ($prop, $having) {
            if (call_user_func($having, $obj)) {
                $key = $obj[$prop];
                if (!array_key_exists($key, $acc)) {
                    $acc[$key] = [];
                }
                array_push($acc[$key], $obj);
            }
            return $acc;
        }, []);

        return $this;
    }

    /**
     * 给列取别名
     * @param array $columns
     * @return $this
     */
    public function alias(array $columns){
        $data = [];
        foreach ($this->_from as $key=>$v){
            foreach($columns as $key2=>$v2){
                $this->_from[$key][$v2]=$this->_from[$key][$key2];
                unset($this->_from[$key][$key2]);
            }

        }
        return $this;
    }
    /**
     * 判断数据类型
     * @param $value
     * @return string
     */
    private static function jugeType($value){
        $type="";
        if(is_array($value)){
            $type = "array";
        }elseif (is_string($value)){
            $type = "string";
        }elseif(is_callable($value)){
            $type = "callable";
        }
        return $type;
    }
    //聚集函数
    //limit
    //join left inner right

}