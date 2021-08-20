<?php
if (!defined('puyuetian')) {
	exit('Not Found puyuetian!Please contact QQ632827168');
}

$a = Cstr(strtolower($_GET['a']), false, $_G['STRING']['LOWERCASE'] . $_G['STRING']['NUMERICAL'] . '_:', 1, 255);
if ($a && strpos($a, ':') === false) {
	$a .= ':index';
}
if (!$a) {
	PkPopup('{
		content:"非法的请求参数",
		icon:2,
		shade:1,
		hideclose:1,
		nomove:1,
		submit:function(){
			window.close()
		}
	}');
}
$a = explode(':', $a);
if (count($a) != 2) {
	PkPopup('{
		content:"非法的插件请求",
		icon:2,
		shade:1,
		hideclose:1,
		nomove:1,
		submit:function(){
			window.close()
		}
	}');
}
$_G['SYSTEM']['APPDIR'] = $a[0];
$_G['SYSTEM']['APPFILE'] = $a[1];
if (InArray('load,embed', $_G['SYSTEM']['APPFILE'])) {
	PkPopup('{
		content:"加载文件禁止直接访问",
		icon:2,
		shade:1,
		hideclose:1,
		nomove:1,
		submit:function(){
			window.close()
		}
	}');
}
$_G['SYSTEM']['APPPATH'] = $_G['SYSTEM']['PATH'] . "app/{$_G['SYSTEM']['APPDIR']}/{$_G['SYSTEM']['APPFILE']}.php";
if (!file_exists($_G['SYSTEM']['APPPATH']) || !$_G['SET']['APP_' . strtoupper($_G['SYSTEM']['APPDIR']) . '_LOAD']) {
	PkPopup('{
		content:"不存在的应用或未启用",
		icon:2,
		shade:1,
		hideclose:1,
		nomove:1,
		submit:function(){
			window.close()
		}
	}');
}

$_G['SYSTEM']['APPCONFIG'] = $_G['SYSTEM']['PATH'] . "app/{$_G['SYSTEM']['APPDIR']}/config.json";
if (file_exists($_G['SYSTEM']['APPCONFIG'])) {
	$_G['SYSTEM']['APPCONFIG'] = json_decode(file_get_contents($_G['SYSTEM']['APPCONFIG']), true);
} else {
	$_G['SYSTEM']['APPCONFIG'] = array();
}

if (!InArray('superadmin', $_G['SYSTEM']['APPDIR'])) {
	$_G['SET']['WEBTITLE'] = $_G['SYSTEM']['APPCONFIG']['title'] . '-插件';
	$_G['SET']['WEBKEYWORDS'] = $_G['SET']['WEBTITLE'];
	$_G['SET']['WEBDESCRIPTION'] = $_G['SET']['WEBTITLE'];
}

require $_G['SYSTEM']['APPPATH'];
