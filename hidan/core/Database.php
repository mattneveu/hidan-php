<?php
/**
* Hidan-PHP Framework
*
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 2.1 of the License, or (at your option) any later version.
*
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.
*
* @package hidan-php
* @link https://github.com/mattneveu/hidan-php
* @author Matthieu Neveu
* @version 1.0
*/

class _Hidan_Database
{
  protected $rowCount = false;
	protected $queryCounter = 0;
  protected $dbHandle = NULL;
  protected $dbPrefix = NULL;
  protected static $dbInstance = array();

  public function __construct($num = 0)
  {
    require 'application/config/database.php';

    $db = new PDO($config[$num]['type'].":host=".$config[$num]['host'].";port=".$config[$num]['port'].";".
                  "dbname=".$config[$num]['databasename'], $config[$num]['user'], $config[$num]['userpw']);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
		$db->query("set character set utf8");
		$db->query("set names utf8");

    $this->dbHandle = $db;
    $this->dbPrefix = $config[$num]['prefix'];
  }

  public static function _getDb($num = 0)
  {
    if(self::$dbInstance[$num] == NULL)
      self::$dbInstance[$num] = new self($num);

    return self::$dbInstance[$num];
  }

  public function getHandle()
	{
		return $this->dbHandle;
	}

  protected function _query($qry, array $params, $type)
	{
		if (in_array($type, array("insert", "select", "update", "delete", "replace")) === false)
		{
			throw new Exception("Unsupported Query Type");
		}

    $this->rowCount = false;

    $qry = preg_replace('/%%(.*)%%/i', $this->dbPrefix . '$1', $qry);

		$stmt	= $this->dbHandle->prepare($qry);

		if (isset($params[':limit']) || isset($params[':offset']))
		{
			foreach($params as $param => $value)
			{
				if($param == ':limit' || $param == ':offset')
				{
					$stmt->bindValue($param, (int) $value, PDO::PARAM_INT);
				}
				else
				{
					$stmt->bindValue($param, (int) $value, PDO::PARAM_STR);
				}
			}
		}

		try {
			$success = (count($params) !== 0 && !isset($params[':limit']) && !isset($params[':offset'])) ? $stmt->execute($params) : $stmt->execute();
		}
		catch (PDOException $e) {
			throw new Exception($e->getMessage()."<br>\r\n<br>\r\nQuery-Code:".str_replace(array_keys($params), array_values($params), $qry));
		}

		if (!$success)
			return false;

		$this->rowCount = $stmt->rowCount();

		return ($type === "select") ? $stmt : true;
	}

	protected function getQueryType($qry)
	{
		if(!preg_match('!^(\S+)!', $qry, $match))
        {
            throw new Exception("Invalid query $qry!");
        }

		if(!isset($match[1]))
        {
            throw new Exception("Invalid query $qry!");
        }

		return strtolower($match[1]);
	}

	public function delete($qry, array $params = array())
	{
		if (($type = $this->getQueryType($qry)) !== "delete")
			throw new Exception("Incorrect Delete Query");

		return $this->_query($qry, $params, $type);
	}

	public function replace($qry, array $params = array())
	{
		if (($type = $this->getQueryType($qry)) !== "replace")
			throw new Exception("Incorrect Replace Query");

		return $this->_query($qry, $params, $type);
	}

	public function update($qry, array $params = array())
	{
		if (($type = $this->getQueryType($qry)) !== "update")
			throw new Exception("Incorrect Update Query");

		return $this->_query($qry, $params, $type);
	}

	public function insert($qry, array $params = array())
	{
		if (($type = $this->getQueryType($qry)) !== "insert")
			throw new Exception("Incorrect Insert Query");

		return $this->_query($qry, $params, $type);
	}

	public function select($qry, array $params = array())
	{
		if (($type = $this->getQueryType($qry)) !== "select")
			throw new Exception("Incorrect Select Query");

		$stmt = $this->_query($qry, $params, $type);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function selectSingle($qry, array $params = array(), $field = false)
	{
		if (($type = $this->getQueryType($qry)) !== "select")
			throw new Exception("Incorrect Select Query");

		$stmt = $this->_query($qry, $params, $type);
		$res = $stmt->fetch(PDO::FETCH_ASSOC);
		return ($field === false || is_null($res)) ? $res : $res[$field];
	}

}

?>
