<?php

/**
 * Created by PhpStorm.
 * User: G-emall
 * Date: 2017/6/1
 * Time: 18:19
 */
class DB
{
    private static $_instance = null;
    private $dsn;
    private $dbCharset;
    private $dbusername;
    private $dbpassword;
    protected $tableName; //表名


    public function __construct()
    {
        //获取配置文件
        $Yaf_config = Yaf_Registry::get("config")->toArray();
        $this->dsn  = $Yaf_config['db']['default']['dsn'];
        $this->dbCharset = isset($Yaf_config['db']['default']['charset']) ? $Yaf_config['db']['default']['charset'] : 'utf8';
        $this->dbusername = $Yaf_config['db']['default']['username'];
        $this->dbpassword = $Yaf_config['db']['default']['password'];

        //链接数据库
        $this->_getInstance();

    }

    /*
     * 防止克隆
     */
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /*
     *链接数据库
     */
    private function _getInstance(){
        if(self::$_instance === null){
            try{
                self::$_instance = new PDO($this->dsn,$this->dbusername,$this->dbpassword);
                self::$_instance->exec('SET character_set_connection='.$this->dbCharset.', character_set_results='.$this->dbCharset.', character_set_client=binary');
            }catch (PDOException $e){
                 var_export($e->getMessage(),true);
                 return false;
            }
        }else
            return self::$_instance;
    }

    /*
     * 条件组合
     * @params array|string $condition
     * @params array $params
     *
     *
     */
    public function where($condition,$params = []){
        if(!$condition) return '';
        if(is_string($condition) && empty($params)){
            return $condition;
        }elseif(is_string($condition) && !empty($params)){

        }elseif (is_array($condition)){
            $operator = strtoupper($condition[0]);
            if($operator === 'OR' || $operator === 'AND'){ //operator is OR or AND
                if(!isset($condition[1]) && !is_array($condition[1]))
                    return '';
                $paramsArr = $condition[1];
                if(count($paramsArr) == 1){
                    $valueStr = is_string($paramsArr[key($paramsArr)]) ?  self::$_instance->quote($paramsArr[key($paramsArr)]) : $paramsArr[key($paramsArr)];
                    return ' '.key($paramsArr).'='.$valueStr;
                }
                $parts = array();
                foreach ($condition[1] as $key=>$val){
                    $valueStr = is_string($val) ? self::$_instance->quote($val) : $val;
                    $parts[]  = $valueStr;
                }

                return $parts === array() ? ' ' : implode(' '.$operator.' ',$parts);
            }

            if(!isset($condition[1],$condition[2]))
                return '';

            $values = $condition[2];
            if(!is_array($values))
                $values = array($values);

            if($operator === 'IN' || $operator === 'NOT IN'){//operator is IN or NOT IN

                if($values === array())
                    return '';

                foreach ($values as $k=>$v){
                    $values[$k] = is_string($v) ? self::$_instance->quote($v) : (string)$v;
                }

                return trim($condition[1]) .' '.$operator.' ('.implode(',',$values).') ';

            }
            //operator is LIKE or NOT LIKE or OR LIKE or OR NOT LIKE
            if($operator === 'LIKE' || $operator === 'NOT LIKE' || $operator === 'OR LIKE' || $operator === 'OR NOT LIKE'){
                if($values === array())
                    return '';
                if($operator==='LIKE' || $operator==='NOT LIKE')
                    $andor=' AND ';
                else
                {
                    $andor=' OR ';
                    $operator=$operator==='OR LIKE' ? 'LIKE' : 'NOT LIKE';
                }
                $expressions=array();
                foreach($values as $value)
                    $expressions[]=$condition[2].' '.$operator.' '.self::$_instance->quote($value);
                return implode(' '.$andor.' ',$expressions);
            }

            return '';
        }else
            return '';
    }
    /*
     * 查询数据
     */
    public function query(){

    }

    /*
     *
     */




}