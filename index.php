<?php
//调试模式TRUE，运营模式FALSE
define('DEBUG', true);

//程序版本，升级必备
define('HADSKY_VERSION', '7.7.16');
if (DEBUG) {
	ini_set('display_errors', 'On');
	error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_USER_NOTICE);
} else {
	ini_set('display_errors', 'Off');
	error_reporting(0);
}

//安装信息检测
if (!file_exists(dirname(__FILE__) . '/install/install.locked') && file_exists(dirname(__FILE__) . '/install/')) {
	header('Location:install/index.php');
	exit('<script>location.href="install/index.php"</script>');
}

//全局框架加载
require 'puyuetian.php';

//程序升级驱动，确保程序具有写入权限，不然无法升级
if (file_exists('update.php')) {
	require 'update.php';
}

//输出HTML到浏览器
finallyOutput($_G['HTML']);
