<?php
if (!defined('puyuetian')) {
	exit('Not Found puyuetian!Please contact QQ632827168');
}
/*
 * puyuetianPHP轻框架 核心函数
 * 作者：蒲乐天（puyuetian）
 * QQ：632827168
 * 官网：http://www.puyuetian.com
 *
 * 作者允许您转载和使用，但必须注明来自puyuetianPHP轻框架。
 */
/********************************************
 数据库操作类 - 获取、修改、添加、删除表信息，必须为id为唯一索引
 *******************************************/
class Data
{
	public $pdo;
	public $sqltype;
	public $id;
	public $array;
	public $query;
	public $link;
	public $mysql_prefix;
	public $table;
	public $index;
	public $cache_open = false;
	public $cache_cycle = 0;
	private $array_keys;
	private $array_values;
	public function __construct($table, $prefix = true)
	{
		global $_G;
		if ($prefix) {
			//获取数据库表前缀
			$this -> mysql_prefix = $_G['SQL']['PREFIX'];
		} else {
			$this -> mysql_prefix = '';
		}
		$this -> sqltype = $_G['SQL']['TYPE'];
		$this -> table = $table;
		$this -> pdo = $_G['PDO'];
		if (Cnum($_G['SET']['MYSQLCACHECYCLE'], false, true, 1) && InArray($_G['SET']['MYSQLCACHETABLES'], $table) && $table != "cache") {
			//开启了数据库缓存功能
			$this -> cache_open = true;
			$this -> cache_cycle = intval($_G['SET']['MYSQLCACHECYCLE']);
		}
	}

	public function getCache($sql)
	{
		//出json数据
		if (!$this -> cache_open || !$sql) {
			return false;
		}
		$path = $_G['SYSTEM']['PATH'] . 'cache/sql/' . md5($sql . $_G['SET']['KEY']) . '.json';
		if(!file_exists($path) || (filemtime($path) + $this -> cache_cycle < time())){
			return false;
		}
		return file_get_contents($path);
		// $query = $this -> pdo -> query("select `content` from `{$this->mysql_prefix}cache` where `query`=" . mysqlstr($sql) . " and `time`<" . time() . " limit 0,1");
		// if ($query) {
		// 	$array = $query -> fetch(PDO::FETCH_ASSOC);
		// 	return $array[0];
		// }
		// return false;
	}

	public function newCache($sql, $content = '')
	{
		//入json数据
		if (!$this -> cache_open || !$sql) {
			return false;
		}
		$path = $_G['SYSTEM']['PATH'] . 'cache/sql/' . md5($sql . $_G['SET']['KEY']) . '.json';
		if (file_put_contents($path, $content) === false) {
			return false;
		}
		return true;
		// $query = 'insert into `' . $this -> mysql_prefix . 'cache` (`query`,`content`,`time`) values (' . mysqlstr($sql, TRUE, '', TRUE) . ',' . mysqlstr($content, TRUE, '', TRUE) . ',' . (time() + $this -> cache_cycle) . ')';
		// $mysql_r = $this -> pdo -> query($query);
		// return $mysql_r;
	}

	public function getSql($field, $str = null)
	{
		$sql = "";
		if (is_array($field) && is_array($str)) {
			foreach ($field as $key => $value) {
				if (Cstr($field[$key], false, true, 1, 255)) {
					$sql .= "`{$field[$key]}`=" . mysqlstr($str[$key]) . " and ";
				}
			}
			$sql = substr($sql, 0, strlen($sql) - 5);
		} elseif (is_array($field) && (!$str && $str !== "")) {
			foreach ($field as $key => $value) {
				if (Cstr($key, false, true, 1, 255)) {
					$sql .= "`{$key}`=" . mysqlstr($value) . " and ";
				}
			}
			$sql = substr($sql, 0, strlen($sql) - 5);
		} elseif ($field && (!$str && $str !== "")) {
			$sql = $field;
			$w = true;
		} else {
			$str = mysqlstr($str);
			if (Cstr($field, false, true, 1, 255)) {
				$sql = "`{$field}`={$str}";
			}
		}
		if (!$w) {
			$sql = "where {$sql}";
		}
		return $sql;
	}

	public function getId($field = 'order by `id` desc', $str = null)
	{
		$sql = $this -> getSql($field, $str);
		$sql = "select `id` from `{$this->mysql_prefix}{$this->table}` {$sql} limit 0,1";
		//读取数据
		$query = $this -> pdo -> query($sql);
		if ($query) {
			$array = $query -> fetch(PDO::FETCH_ASSOC);
			//输出数据
			return $array['id'];
		} else {
			return false;
		}
	}

	public function getData($field = null, $str = null, $fields = '*')
	{
		if ($field === null) {
			return false;
		}
		if (Cnum($field, false)) {
			$sql = "where `id`='{$field}'";
		} else {
			$sql = $this -> getSql($field, $str);
		}
		$sql = "select {$fields} from `{$this->mysql_prefix}{$this->table}` {$sql} limit 0,1";
		$query = $this -> pdo -> query($sql);
		if ($query) {
			$array = $query -> fetch(PDO::FETCH_ASSOC);
			return $array;
		} else {
			return false;
		}
	}

	//pos开始指针位置，rnum读取记录条数
	public function getDatas($pos = 0, $rnum = 10, $field = null, $str = null, $fields = '*')
	{
		$limit = " limit {$pos},{$rnum}";
		if ($field) {
			$sql = ' ' . $this -> getSql($field, $str);
		}
		if (!$rnum) {
			$limit = '';
		}
		$sql = "select {$fields} from `{$this -> mysql_prefix}{$this -> table}`{$sql}{$limit}";
		//============================================
		//是否有缓存
		$cache = $this -> getCache($sql);
		if ($cache) {
			return json_decode($cache, true);
		}
		//============================================
		//读取数据
		$query = $this -> pdo -> query($sql);
		if ($query) {
			$array = array();
			$index = 0;
			while ($querya = $query -> fetch(PDO::FETCH_ASSOC)) {
				$array[$index] = $querya;
				$index++;
			}
			//============================================
			//存储缓存
			$this -> newCache($sql, json_encode($array));
			//============================================
			//输出数据
			return $array;
		} else {
			return false;
		}
	}

	public function getColumns($field = null, $str = null)
	{
		if (Cstr($field, false, true, 1, 255) && $str == null) {
			$sql = " where `Field`='{$field}'";
		} elseif ($field) {
			$sql = ' ' . $this -> getSql($field, $str);
		} else {
			$sql = '';
		}
		switch ($this->sqltype) {
			case 'sqlite':
				$fn = 'name';
				$sql = "PRAGMA table_info('{$this -> mysql_prefix}{$this -> table}')";
				break;

			default:
				$fn = 'Field';
				$sql = "show full columns from `{$this -> mysql_prefix}{$this -> table}`{$sql}";
				break;
		}
		$query = $this -> pdo -> query($sql);
		if ($query) {
			$array = array();
			while ($querya = $query -> fetch(PDO::FETCH_ASSOC)) {
				switch ($this->sqltype) {
					case 'sqlite':
						$querya['Field'] = $querya['name'];
						break;
					default:
						break;
				}
				$array[$querya[$fn]] = $querya;
			}
			return $array;
		} else {
			return false;
		}
	}

	public function getCount($field = null, $str = null)
	{
		if ($field) {
			$sql = ' ' . $this -> getSql($field, $str);
		}
		$sql = "select count(*) from `{$this->mysql_prefix}{$this->table}`{$sql}";
		//============================================
		//是否有缓存
		$cache = $this -> getCache($sql);
		if ($cache) {
			$cache = json_decode($cache, true);
			return intval(Cnum($cache[0], 0, true, 0));
		}
		//============================================
		$query = $this -> pdo -> query($sql);
		if (!$query) {
			return 0;
		}
		$res = $query -> fetch(PDO::FETCH_NUM);
		//============================================
		//存储缓存
		$this -> newCache($sql, json_encode($res));
		//============================================
		return intval(Cnum($res[0], 0, true, 0));
	}

	public function newData($array)
	{
		if (!is_array($array)) {
			return false;
		}
		//获取表字段名
		$array_keys = array_keys($array);
		//print_r($array_keys);
		//获取对应字段的值
		$array_values = array_values($array);
		//print_r($array_values);
		if (array_key_exists('id', $array)) {
			$id = Cnum($array['id']);
		} else {
			$id = 0;
		}
		if ($id) {
			//表信息更新
			$query = "update `{$this->mysql_prefix}{$this->table}` set ";
			for ($i = 0; $i < count($array_keys); $i++) {
				$query .= '`' . $array_keys[$i] . '`=' . mysqlstr($array_values[$i], true, '', true) . ',';
			}
			$query = substr($query, 0, strlen($query) - 1);
			$query .= " where `id`='{$id}'";
		} else {
			//表添加新信息
			$idkey = null;
			$query = "insert into `{$this->mysql_prefix}{$this->table}` (";
			foreach ($array_keys as $key => $value) {
				if ($value == 'id') {
					$idkey = $key;
				} else {
					$query .= "`$value`,";
				}
			}
			$query = substr($query, 0, strlen($query) - 1);
			$query .= ') values (';
			foreach ($array_values as $key => $value) {
				if ($key !== $idkey) {
					$query .= mysqlstr($value, true, '', true) . ',';
				}
			}
			$query = substr($query, 0, strlen($query) - 1);
			$query .= ')';
		}
		$mysql_r = $this -> pdo -> query($query);
		return $mysql_r;
	}

	public function delData($field, $str = null)
	{
		if (Cnum($field)) {
			$sql = "where `id`='{$field}'";
		} else {
			$sql = $this -> getSql($field, $str);
		}
		$query = "delete from `{$this->mysql_prefix}{$this->table}` {$sql}";
		$mysql_r = $this -> pdo -> query($query);
		return $mysql_r;
	}
}
