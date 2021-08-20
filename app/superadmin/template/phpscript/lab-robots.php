<?php
if (!defined('puyuetian')) {
	exit('403');
}
	
if ($_G['GET']['SUBMIT']) {
	if (file_put_contents($_G['SYSTEM']['PATH'] . 'robots.txt', $_POST['content']) === false) {
		ExitJson('文件写入失败');
	}
	ExitJson('保存成功', true);
}

$_G['TEMP']['ROBOTS_CONTENT'] = file_get_contents($_G['SYSTEM']['PATH'] . 'robots.txt');
if (!$_G['TEMP']['ROBOTS_CONTENT']) {
	$_G['TEMP']['ROBOTS_CONTENT'] = '';
}
$_G['TEMP']['ROBOTS_CONTENT'] = htmlspecialchars($_G['TEMP']['ROBOTS_CONTENT'], ENT_QUOTES);
