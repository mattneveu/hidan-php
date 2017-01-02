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

class _Hidan_Controller extends _Hidan_Template
{
  private $params = array();

  public function __construct()
  {
    if(count($_POST) > 0)
    {
      $this->params = $_POST;
    }
    parent::__construct();
  }

  public function _addParams($pa = array())
  {
    $this->params = array_merge($this->params, $pa);
  }

  protected function _getParam($name, $default, $multibyte = false, $highnum = false)
	{
		if(!isset($this->params[$name]))
		{
			return $default;
		}

		if(is_float($default) || $highnum)
		{
			return (float) $this->params[$name];
		}

		if(is_int($default))
		{
			return (int) $this->params[$name];
		}

		if(is_string($default))
		{
			return self::_quote($this->params[$name], $multibyte);
		}

		if(is_array($default) && is_array($this->params[$name]))
		{
			return self::_quoteArray($this->params[$name], $multibyte, !empty($default) && $default[0] === 0);
		}

		return $default;
	}

  private static function _quoteArray($var, $multibyte, $onlyNumbers = false)
	{
		$data	= array();
		foreach($var as $key => $value)
		{
			if(is_array($value))
			{
				$data[$key]	= self::_quoteArray($value, $multibyte);
			}
			elseif($onlyNumbers)
			{
				$data[$key]	= (int) $value;
			}
			else
			{
				$data[$key]	= self::_quote($value, $multibyte);
			}
		}

		return $data;
	}

	private static function _quote($var, $multibyte)
	{
		$var	= str_replace(array("\r\n", "\r", "\0"), array("\n", "\n", ''), $var);
		$var	= htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
		$var	= trim($var);

		if ($multibyte) {
			if (!preg_match('/^./u', $var))
			{
				$var = '';
			}
		}
		else
		{
			$var = preg_replace('/[\x80-\xFF]/', '?', $var); // no multibyte, allow only ASCII (0-127)
		}

		return $var;
	}

}

?>
