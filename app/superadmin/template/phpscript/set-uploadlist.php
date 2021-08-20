<?php
if (!defined('puyuetian')) {
	exit('403');
}

if ($_G['GET']['DO'] == 'del') {
	$ids = explode(',', $_POST['ids']);
	foreach ($ids as $id) {
		if (!Cnum($id)) {
			continue;
		}
		$data = $_G['TABLE']['UPLOAD'] -> getData($id);
		if (!$data) {
			continue;
		}
		$filepath = "{$_G['SYSTEM']['PATH']}uploadfiles/{$data['target']}s/{$data['uid']}/" . substr($data['datetime'], 0, 8) . "/" . substr($data['datetime'], 8) . "_{$data['rand']}.{$data['suffix']}";
		$_G['TABLE']['UPLOAD'] -> delData($id);
		unlink($filepath);
	}
	ExitJson('删除成功', true);
}

$page = Cnum($_GET['page'], 1, true, 1);
$limit = 100;
$sql = 'ORDER BY `id` DESC';
$field = $_G['GET']['FIELD'];
$value = $_GET['value'];
if ($field && $value) {
	$sql = "WHERE `{$field}`=" . mysqlstr($value) . " {$sql}";
}

$datas = $_G['TABLE']['UPLOAD'] -> getDatas(($page-1)*$limit, $limit, $sql);
$html = '';
foreach ($datas as $key => $data) {
	$filepath = "uploadfiles/{$data['target']}s/{$data['uid']}/" . substr($data['datetime'], 0, 8) . "/" . substr($data['datetime'], 8) . "_{$data['rand']}.{$data['suffix']}";
	$filesize = round(filesize($_G['SYSTEM']['PATH'] . $filepath) / 1024, 2) . 'K';
	$user = $_G['TABLE']['USER'] -> getData($data['uid']);
	$look = '无预览';
	if ($data['target'] == 'image') {
		$look = "<img src='$filepath' style='max-width:100px;max-height:100px;cursor:pointer' onclick='LookImage(this)' />";
	}
	$html .= "<tr id=_uploadlist_{$data['id']}>
	<td><input type=checkbox value={$data['id']}></td>
	<td style=text-align:center>{$data['id']}</td>
	<td style=text-align:center>
	<a class='pk-text-primary pk-hover-underline' href='javascript:;' onclick='_superedituser({$user['id']})'>{$user['username']}</a>
	</td>
	<td style=text-align:center><div style=max-width:150px;word-break:break-all>".($data['name']?$data['name']:'未设置')."</div></td>
	<td style=text-align:center>{$look}</td>
	<td>
	<a target='_blank' class='pk-text-primary pk-hover-underline' href='{$filepath}'>{$filepath}</a>
	</td>
	<td>
	<a target='_blank' class='pk-text-primary pk-hover-underline' href='index.php?c=app&a=puyuetianeditor:index&s=showfile&id={$data['id']}'>index.php?c=app&a=puyuetianeditor:index&s=showfile&id={$data['id']}</a>
	</td>
	<td style=text-align:center>{$filesize}</td>
	<td style=text-align:center>" . date('<p>Y-m-d</p><p>H:i:s</p>', $data['uploadtime']) . "</td>
	<td style=text-align:center>
	<a href=javascript:; class='pk-text-danger pk-hover-underline' onclick=_delfile({$data['id']})>删除</a>
	</td>
	</tr>";
}
$_G['TEMP']['PAGE'] = $page;
$_G['TEMP']['HTML'] = $html;
$_G['TEMP']['FIELD'] = $field;
$_G['TEMP']['VALUE'] = htmlspecialchars($value, ENT_QUOTES);
