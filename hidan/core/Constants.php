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

define('HTTP_BASE', str_replace(array('\\', '//'), '/', dirname($_SERVER['SCRIPT_NAME']).'/'));
define('HTTP_ROOT', str_replace(basename($_SERVER['SCRIPT_FILENAME']), '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
define('CACHE_PATH', 'cache');
define('TEMPLATE_PATH', 'application/views/');
define('SESSION_LIFETIME', 43200);

?>
