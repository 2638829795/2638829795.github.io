<?php
if (!defined('puyuetian'))
	exit('403');

global $readdata, $readuserdata;
$data = $_G['TABLE']['USERGROUP'] -> getData($readuserdata['groupid']);
if ($data) {
	$readdata['usergroupname'] = $data['usergroupname'];
} else {
	$readdata['usergroupname'] = '自由达人';
}

$_G['TEMP']['JHZD'] = '';
if ($readdata['high']) {
	$_G['TEMP']['JHZD'] .= '<span class="layui-badge layui-bg-red">精帖</span>';
}
if ($readdata['top']) {
	$_G['TEMP']['JHZD'] .= '<span class="layui-badge layui-bg-black">置顶</span>';
}

$_G['TEMP']['READADMINLINK'] = '';
if ($_G['USER']['ID'] == 1 || InArray(getUserQX(), 'admin') || $_G['TEMP']['BKADMIN'] || ($_G['USER']['ID'] == $readuserdata['id'] && $_G['USER']['ID'])) {
	if (InArray(getUserQX(), 'superman')) {
		if ($readdata['top']) {
			$_G['TEMP']['READADMINLINK'] .= '<a href="javascript:" class="layui-btn layui-btn-xs jie-admin" onclick="layer.confirm(&quot;确认取消该文章置顶？&quot;,{title:&quot;提示&quot;,icon:3},function(index){$.get(\'index.php?c=admincmd&table=read&field=top&value=0&id=' . $readdata['id'] . '&chkcsrfval=' . $_G['CHKCSRFVAL'] . '\',function(){layer.msg(\'取消成功\',{icon:1});location.reload()});layer.close(index)})">取消置顶</a>';
		} else {
			$_G['TEMP']['READADMINLINK'] .= '<a href="javascript:" class="layui-btn layui-btn-xs jie-admin" onclick="layer.confirm(&quot;确认设置该文章置顶？&quot;,{title:&quot;提示&quot;,icon:3},function(index){$.get(\'index.php?c=admincmd&table=read&field=top&value=1&id=' . $readdata['id'] . '&chkcsrfval=' . $_G['CHKCSRFVAL'] . '\',function(){layer.msg(\'设置成功\',{icon:1});location.reload()});layer.close(index)})">设为置顶</a>';
		}
		if ($readdata['high']) {
			$_G['TEMP']['READADMINLINK'] .= '<a href="javascript:" class="layui-btn layui-btn-xs jie-admin" onclick="layer.confirm(&quot;确认取消该文章精华？&quot;,{title:&quot;提示&quot;,icon:3},function(index){$.get(\'index.php?c=admincmd&table=read&field=high&value=0&id=' . $readdata['id'] . '&chkcsrfval=' . $_G['CHKCSRFVAL'] . '\',function(){layer.msg(\'取消成功\',{icon:1});location.reload()});layer.close(index)})">取消精华</a>';
		} else {
			$_G['TEMP']['READADMINLINK'] .= '<a href="javascript:" class="layui-btn layui-btn-xs jie-admin" onclick="layer.confirm(&quot;确认设置该文章精华？&quot;,{title:&quot;提示&quot;,icon:3},function(index){$.get(\'index.php?c=admincmd&table=read&field=high&value=1&id=' . $readdata['id'] . '&chkcsrfval=' . $_G['CHKCSRFVAL'] . '\',function(){layer.msg(\'设置成功\',{icon:1});location.reload()});layer.close(index)})">设为精华</a>';
		}
	}
	if (InArray(getUserQX(), 'editread')) {
		$_G['TEMP']['READADMINLINK'] .= '<a class="layui-btn layui-btn-xs jie-admin" href="index.php?c=edit&type=read&id=' . $_G['GET']['ID'] . '">编辑</a>';
	}
	if (InArray(getUserQX(), 'delread')) {
		$_G['TEMP']['READADMINLINK'] .= '&nbsp;<a href="javascript:" class="layui-btn layui-btn-xs jie-admin" onclick="layer.confirm(\'确认删除该文章？\',{title:\'提示\',icon:3},function(index){delread(\'' . $readdata['id'] . '\',\'read\',function(){location.href=\'index.php\';layer.msg(\'删除成功\',{icon:1})});layer.close(index)})">删除</a>';
	}
}
