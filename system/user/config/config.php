<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['index_page'] = '';
$config['is_system_on'] = 'y';
$config['multiple_sites_enabled'] = 'n';
$config['show_ee_news'] = 'n';
$config['debug'] = "0";
// ExpressionEngine Config Items
// Find more configs and overrides at
// https://docs.expressionengine.com/latest/general/system_configuration_overrides.html

$config['app_version'] = '5.3.0';
$config['encryption_key'] = 'c3a140a7c32fdb1257381052fd01ef1d15c69b7f';
$config['session_crypt_key'] = '886709d1addc447f12dc012d4139d3579a593e1b';
$config['database'] = array(
	'expressionengine' => array(
		'hostname' => 'localhost',
		'database' => 'expression',
		'username' => 'expression',
		'password' => 'ExpressionEngine123',
		'dbprefix' => 'exp_',
		'char_set' => 'utf8mb4',
		'dbcollat' => 'utf8mb4_unicode_ci',
		'port'     => ''
	),
);

// EOF