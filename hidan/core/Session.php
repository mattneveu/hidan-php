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

class _Hidan_Session
{

  static private $obj = NULL;
	static private $iniSet	= false;
	private $data = NULL;

	/**
	 * Set PHP session settings
	 *
	 * @return bool
	 */

	static public function init()
	{
		if(self::$iniSet === true)
		{
			return false;
		}
		self::$iniSet = true;

		ini_set('session.use_cookies', '1');
		ini_set('session.use_only_cookies', '1');
		ini_set('session.use_trans_sid', 0);
		ini_set('session.auto_start', '0');
		ini_set('session.serialize_handler', 'php');
		ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
		ini_set('session.gc_probability', '1');
		ini_set('session.gc_divisor', '1000');
		ini_set('session.bug_compat_warn', '0');
		ini_set('session.bug_compat_42', '0');
		ini_set('session.cookie_httponly', true);
		ini_set('session.save_path', CACHE_PATH.'sessions');
		ini_set('upload_tmp_dir', CACHE_PATH.'sessions');

		session_set_cookie_params(SESSION_LIFETIME, HTTP_ROOT, NULL, HTTPS, true);
		session_cache_limiter('nocache');
		session_name('hidan-php');

		return true;
	}

	static private function getTempPath()
	{
		require_once 'hidan/libs/wcf/BasicFileUtilClass.php';
		return BasicFileUtil::getTempFolder();
	}


	/**
	 * Create an empty session
	 *
	 * @return String
	 */

	static public function getClientIp()
    {
		if(!empty($_SERVER['HTTP_CLIENT_IP']))
        {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        }
		elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
			$ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        elseif(!empty($_SERVER['HTTP_X_FORWARDED']))
        {
			$ipAddress = $_SERVER['HTTP_X_FORWARDED'];
        }
        elseif(!empty($_SERVER['HTTP_FORWARDED_FOR']))
        {
			$ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
        }
        elseif(!empty($_SERVER['HTTP_FORWARDED']))
        {
			$ipAddress = $_SERVER['HTTP_FORWARDED'];
        }
        elseif(!empty($_SERVER['REMOTE_ADDR']))
        {
			$ipAddress = $_SERVER['REMOTE_ADDR'];
        }
        else
        {
			$ipAddress = 'UNKNOWN';
        }
        return $ipAddress;
	}

	/**
	 * Create an empty session
	 *
	 * @return Session
	 */

	static public function create()
	{
		if(!self::existsActiveSession())
		{
			self::$obj	= new self;
			register_shutdown_function(array(self::$obj, 'save'));

			@session_start();
		}

		return self::$obj;
	}

	/**
	 * Wake an active session
	 *
	 * @return Session
	 */

	static public function load()
	{
		if(!self::existsActiveSession())
		{
			self::init();
			session_start();
			if(isset($_SESSION['obj']))
			{
				self::$obj	= unserialize($_SESSION['obj']);
				register_shutdown_function(array(self::$obj, 'save'));
			}
			else
			{
				self::create();
			}
		}

		return self::$obj;
	}

	/**
	 * Check if an active session exists
	 *
	 * @return bool
	 */

	static public function existsActiveSession()
	{
		return isset(self::$obj);
	}

	public function __construct()
	{
		self::init();
	}

	public function __sleep()
	{
		return array('data');
	}

	public function __wakeup()
	{

	}

	public function __set($name, $value)
	{
		$this->data[$name]	= $value;
	}

	public function __get($name)
	{
		if(isset($this->data[$name]))
		{
			return $this->data[$name];
		}
		else
		{
			return NULL;
		}
	}

	public function __isset($name)
	{
		return isset($this->data[$name]);
	}

	public function save()
	{
    // do not save an empty session
    $sessionId = session_id();
    if(empty($sessionId)) {
        return;
    }

    // sessions require an valid user.
    if(empty($this->data['userId'])) {
        $this->delete();
    }

    $userIpAddress = self::getClientIp();

		$this->data['lastActivity']  = TIMESTAMP;
		$this->data['sessionId']	 = session_id();
		$this->data['userIpAddress'] = $userIpAddress;
		$this->data['requestPath']	 = $this->getRequestPath();

		$_SESSION['obj']	= serialize($this);

		@session_write_close();
	}

	public function delete()
	{
		@session_destroy();
	}

	public function isValidSession()
	{
		if($this->compareIpAddress($this->data['userIpAddress'], self::getClientIp(), COMPARE_IP_BLOCKS) === false)
		{
			return false;
		}

		if($this->data['lastActivity'] < TIMESTAMP - SESSION_LIFETIME)
		{
			return false;
		}

		return true;
	}


	private function getRequestPath()
	{
		return HTTP_ROOT.(!empty($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : '');
	}

	private function compareIpAddress($ip1, $ip2, $blockCount)
	{
		if (strpos($ip2, ':') !== false && strpos($ip1, ':') !== false)
		{
			$s_ip = $this->short_ipv6($ip1, $blockCount);
			$u_ip = $this->short_ipv6($ip2, $blockCount);
		}
		else
		{
			$s_ip = implode('.', array_slice(explode('.', $ip1), 0, $blockCount));
			$u_ip = implode('.', array_slice(explode('.', $ip2), 0, $blockCount));
		}

		return ($s_ip == $u_ip);
	}
}

?>
