<?php
if (!defined('puyuetian'))
	exit('403');

global $replydata, $replyuserdata;
$data = $_G['TABLE']['USERGROUP'] -> getData($replyuserdata['groupid']);
if ($data) {
	$replydata['usergroupname'] = $data['usergroupname'];
} else {
	$replydata['usergroupname'] = '自由达人';
}

$_G['TEMP']['TOPSHOW'] = 'layui-hide';
if ($replydata['top']) {
	$_G['TEMP']['TOPSHOW'] = '';
}

$lgtime = time() - Cnum($replydata['posttime']);
if ($lgtime < 60) {
	$lgtime = '刚刚';
} elseif ($lgtime < 3600) {
	$lgtime = (int)($lgtime / 60) . '分钟前';
} elseif ($lgtime < 86400) {
	$lgtime = (int)($lgtime / 3600) . '小时前';
} elseif ($lgtime < 2592000) {
	$lgtime = (int)($lgtime / 86400) . '天前';
} else {
	$lgtime = date('Y-m-d H:i:s', $replydata['posttime']);
}
$_G['TEMP']['LGTIME'] = $lgtime;

$_G['TEMP']['READADMINLINK'] = '';
if ($_G['USER']['ID'] == 1 || InArray(getUserQX(), 'admin') || $_G['TEMP']['BKADMIN'] || ($_G['USER']['ID'] == $replyuserdata['id'] && $_G['USER']['ID'])) {
	if (InArray($_G['USER']['QUANXIAN'], 'admin') || $_G['USER']['ID'] == $readuserdata['id']) {
		if (!$replydata['top']) {
			$_G['TEMP']['READADMINLINK'] .= '<a href="javascript:" onclick="layer.confirm(&quot;确认设置该回复置顶？&quot;,{title:&quot;提示&quot;,icon:3},function(index){$.get(\'index.php?c=admincmd&table=reply&field=top&value=1&id=' . $replydata['id'] . '&chkcsrfval=' . $_G['CHKCSRFVAL'] . '\',function(){layer.msg(\'置顶成功\',{icon:1});location.reload()});layer.close(index)})">置顶回复</a>';
		} else {
			$_G['TEMP']['READADMINLINK'] .= '<a href="javascript:" onclick="layer.confirm(&quot;确认取消该回复置顶？&quot;,{title:&quot;提示&quot;,icon:3},function(index){$.get(\'index.php?c=admincmd&table=reply&field=top&value=0&id=' . $replydata['id'] . '&chkcsrfval=' . $_G['CHKCSRFVAL'] . '\',function(){layer.msg(\'取消成功\',{icon:1});location.reload()});layer.close(index)})">取消置顶</a>';
		}
	}
	if (InArray(getUserQX(), 'editreply')) {
		$_G['TEMP']['READADMINLINK'] .= '&nbsp;<a href="index.php?c=edit&type=reply&id=' . $replydata['id'] . '">编辑</a>';
	}
	if (InArray(getUserQX(), 'delreply')) {
		$_G['TEMP']['READADMINLINK'] .= '&nbsp;<a href="javascript:" onclick="layer.confirm(\'确认删除该文章？\',{title:\'提示\',icon:3},function(index){delread(\'' . $replydata['id'] . '\',\'reply\',function(){$(\'#replydivbox-' . $replydata['id'] . '\').remove();layer.msg(\'删除成功\',{icon:1})});layer.close(index)})">删除</a>';
	}
}
