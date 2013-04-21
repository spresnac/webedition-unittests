<?php
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../'));

// Define application environment
define('APPLICATION_ENV', 'testing');

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH ),
    get_include_path(),
)));

// for simulating a remote host for webedition-config
$_SERVER["HTTP_HOST"] = 'http://dev.webedition.de';

require_once ('webEdition/lib/we/core/autoload.php');
