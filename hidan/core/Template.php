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

require 'hidan/libs/Smarty/Smarty.class.php';

class _Hidan_Template extends Smarty
{
  protected $window	= 'full';
	public $jsscript	= array();
	public $script		= array();

	function __construct()
	{
		parent::__construct();
		$this->smartySettings();
	}

	private function smartySettings()
	{
		$this->php_handling = Smarty::PHP_REMOVE;

		$this->setForceCompile(false);
		$this->setMergeCompiledIncludes(true);
		$this->setCompileCheck(false);
		$this->setCacheLifetime(604800);
		$this->setCaching(Smarty::CACHING_LIFETIME_CURRENT);
		$this->setCompileDir(is_writable(CACHE_PATH) ? CACHE_PATH : $this->getTempPath());
		$this->setCacheDir($this->getCompileDir().'templates');
		$this->setTemplateDir(TEMPLATE_PATH);
	}

	private function getTempPath()
	{
		$this->setForceCompile(true);
		$this->setCaching(Smarty::CACHING_OFF);

		require_once 'hidan/libs/wcf/BasicFileUtilClass.php';
		return BasicFileUtil::getTempFolder();
	}

	public function assign_vars($var, $nocache = true)
	{
		parent::assign($var, NULL, $nocache);
	}

	public function loadscript($script)
	{
		$this->jsscript[]			= substr($script, 0, -3);
	}

	public function execscript($script)
	{
		$this->script[]				= $script;
	}

	public function show($file)
	{
    global $LNG;

		$tplDir	= $this->getTemplateDir();

		$this->assign_vars(array(
			'scripts'		=> $this->jsscript,
			'execscript'	=> implode("\n", $this->script),
		));

		$this->assign_vars(array(
			'LNG'			=> $LNG,
		), false);

		$this->compile_id	= $LNG->getLanguage();

		parent::display($file);
	}

	public function display($file = NULL, $cache_id = NULL, $compile_id = NULL, $parent = NULL)
	{
		global $LNG;
		$this->compile_id	= $LNG->getLanguage();
		parent::display($file);
	}

	public function gotoside($dest, $time = 3)
	{
		$this->assign_vars(array(
			'gotoinsec'	=> $time,
			'goto'		=> $dest,
		));
	}

	public function message($mes, $dest = false, $time = 3, $Fatal = false)
	{
		global $LNG;

		$this->assign_vars(array(
			'mes'		=> $mes,
			'fcm_info'	=> $LNG['fcm_info'],
			'Fatal'		=> $Fatal,
		));

		$this->gotoside($dest, $time);
		$this->show('errors/error_message_body.tpl');
	}

	public static function printMessage($Message, $fullSide = true, $redirect = NULL)
  {
		$template	= new self;
		if(!isset($redirect)) {
			$redirect	= array(false, 0);
		}

		$template->message($Message, $redirect[0], $redirect[1], !$fullSide);
		exit;
	}


  public function __get($name)
  {
      $allowed = array(
		'template_dir' => 'getTemplateDir',
		'config_dir' => 'getConfigDir',
		'plugins_dir' => 'getPluginsDir',
		'compile_dir' => 'getCompileDir',
		'cache_dir' => 'getCacheDir',
      );

      if (isset($allowed[$name])) {
          return $this->{$allowed[$name]}();
      } else {
          return $this->{$name};
      }
  }

  public function __set($name, $value)
  {
      $allowed = array(
		'template_dir' => 'setTemplateDir',
		'config_dir' => 'setConfigDir',
		'plugins_dir' => 'setPluginsDir',
		'compile_dir' => 'setCompileDir',
		'cache_dir' => 'setCacheDir',
      );

      if (isset($allowed[$name])) {
          $this->{$allowed[$name]}($value);
      } else {
          $this->{$name} = $value;
      }
  }
}

?>
