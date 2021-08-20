<?php
if (!defined('puyuetian'))
	exit('403');

global $page, $pagecount;
$_G['GET']['SORTID'] = Cnum($_G['GET']['SORTID']);
if (!$pagecount) {
	$pagecount = 1;
}
if ($pagecount < 6) {
	$i = 1;
	$c = $pagecount + 1;
} else {
	$page = Cnum($page, $pagecount, TRUE, 1, $pagecount);
	$i = $page - 2;
	if ($page < 4) {
		$i = 1;
	}
	if ($page > $pagecount - 3) {
		$i = $pagecount - 4;
	}
	$c = $i + 5;
}

$_G['GET']['SORTID'] = Cnum($_G['GET']['SORTID'], 0, TRUE, 0);
$q = '';
if ($_GET['label']) {
	$q .= "&label={$_GET['label']}";
}
if ($_G['GET']['TYPE']) {
	$q .= "&type={$_G['GET']['TYPE']}";
}
if ($_G['GET']['ORDER']) {
	$q .= "&order={$_G['GET']['ORDER']}";
}
$q && $q = substr($q, 1);
$_G['TEMP']['HTML'] = $page > 1 ? '<a class="laypage-prev" href="' . ReWriteURL('list', "sortid={$_G['GET']['SORTID']}&page=" . ($page - 1), $q) . '" title="上一页">上一页</a><a href="' . ReWriteURL('list', "sortid={$_G['GET']['SORTID']}&page=1", $q) . '" title="首页，第1页">首页</a>' : '';
for ($i2 = $i; $i2 < $c; $i2++) {
	$_G['TEMP']['HTML'] .= '<a href="' . ($page == $i2 ? 'javascript:" class="laypage-curr' : ReWriteURL('list', "sortid={$_G['GET']['SORTID']}&page=" . ($i2), $q)) . '" title="第' . $i2 . '页">' . $i2 . '</a>';
}
$_G['TEMP']['HTML'] .= ($page < $pagecount) ? '<a href="' . ReWriteURL('list', "sortid={$_G['GET']['SORTID']}&page=" . $pagecount, $q) . '" title="尾页，第' . $pagecount . '页">尾页</a><a class="laypage-next" href="' . ReWriteURL('list', "sortid={$_G['GET']['SORTID']}&page=" . ($page + 1), $q) . '" title="下一页">下一页</a>' : '';
