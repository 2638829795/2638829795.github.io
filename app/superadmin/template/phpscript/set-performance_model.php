<?php
if (!defined('puyuetian')) {
	exit('403');
}

$_G['TEMP']['TOTAL_READ'] = $_G['TABLE']['READ'] -> getCount();
$_G['TEMP']['TOTAL_REPLY'] = $_G['TABLE']['REPLY'] -> getCount();
$_G['TEMP']['TOTAL_RR'] = $_G['TEMP']['TOTAL_READ'] + $_G['TEMP']['TOTAL_REPLY'];
$_G['TEMP']['TEXT'] = '不推荐开启该功能';
if ($_G['TEMP']['TOTAL_READ'] > 100000 || $_G['TEMP']['TOTAL_REPLY'] > 100000 || $_G['TEMP']['TOTAL_RR'] > 100000) {
	$_G['TEMP']['TEXT'] = '可以开启该功能';
}
if ($_G['TEMP']['TOTAL_READ'] > 150000 || $_G['TEMP']['TOTAL_REPLY'] > 150000 || $_G['TEMP']['TOTAL_RR'] > 150000) {
	$_G['TEMP']['TEXT'] = '推荐开启该功能';
}
