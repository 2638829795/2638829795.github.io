<?php
if (!defined('puyuetian'))
	exit('403');

//热帖
$_G['TEMP']['HOTREADHTML'] = '';
if (Cnum($_G['SET']['TEMPLATE_PUYUETIAN_FLY_HOTREADCOUNT'], FALSE, TRUE, 1)) {
	$sql = '';
	if ($_G['SET']['TEMPLATE_PUYUETIAN_FLY_HOTREADSORTIDS']) {
		$sql = ' and (`sortid`=\'' . str_replace(array(',', '，'), '\' or `sortid`=\'', $_G['SET']['TEMPLATE_PUYUETIAN_FLY_HOTREADSORTIDS']) . '\')';
	}
	if (Cnum($_G['SET']['TEMPLATE_PUYUETIAN_FLY_HOTREADDAYS'], FALSE, TRUE, 1)) {
		$sql .= ' and `posttime`>' . (time() - $_G['SET']['TEMPLATE_PUYUETIAN_FLY_HOTREADDAYS'] * 86400);
	}
	$datas = $_G['TABLE']['READ'] -> getDatas(0, $_G['SET']['TEMPLATE_PUYUETIAN_FLY_HOTREADCOUNT'], 'where `del`=0' . $sql . ' order by `looknum` desc', FALSE, 'id,title,looknum');
	$_G['TEMP']['HOTREADHTML'] .= '<dl class="fly-panel fly-list-one"><dt class="fly-panel-title">本站热帖</dt>';
	$xh = 1;
	foreach ($datas as $value) {
		$_G['TEMP']['HOTREADHTML'] .= '<dd><a href="' . ReWriteURL('read', "id={$value['id']}&page=1") . '"><span class="layui-badge-rim" style="margin-top:5px">' . ($xh < 10 ? '0' . $xh : $xh) . '</span>&nbsp;' . $value['title'] . '</a><span><i class="iconfont">&#xe60b;</i> ' . $value['looknum'] . '</span></dd>';
		$xh++;
	}
	$_G['TEMP']['HOTREADHTML'] .= '</dl>';
}

//最新用户
$_G['TEMP']['NEWUSERS'] = '';
$datas = $_G['TABLE']['USER'] -> getDatas(0, 12, 'order by `id` desc', FALSE, 'id,username,nickname');
foreach ($datas as $value) {
	$_G['TEMP']['NEWUSERS'] .= '<dd><a target="_blank" href="' . ReWriteURL('user', "id={$value['id']}&page=1") . '"><img src="userhead/' . $value['id'] . '.png" onerror="this.src=\'userhead/0.png\'" alt="' . $value['username'] . '"><cite>' . $value['nickname'] . '</cite><i>' . $value['username'] . '</i></a></dd>';
}
