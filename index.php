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

require 'hidan/libs/http/HTTP.class.php';

require 'hidan/core/Constants.php';
require 'hidan/core/Lang.php';

$LNG = new _Hidan_Lang();
$LNG->getUserAgentLanguage();
/** The default Lang file included is CUSTOM.PHP
* You may add other files by filling the array :
*/
$LNG->includeData(array());

require 'hidan/core/Template.php';
require 'hidan/core/Database.php';
require 'hidan/core/Session.php';
require 'hidan/core/Router.php';
require 'hidan/core/Controller.php';

// Call Router to launch action
(new _Hidan_Router)->load();

?>
