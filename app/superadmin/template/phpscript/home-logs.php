<?php
if (!defined('puyuetian')) {
	exit('403');
}

$_G['TEMP']['LOGHTML'] = '';
$logfiles = scandir("{$_G['SYSTEM']['PATH']}app/superadmin/logs/", true);
foreach ($logfiles as $value) {
	if (end(explode('.', $value)) == 'txt') {
		$_G['TEMP']['LOGHTML'] .= '<option value="' . md5($value) . '">' . substr($value, 0, 10) . "</option>";
	}
}
