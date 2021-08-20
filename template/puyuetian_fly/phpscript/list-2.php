<?php
if (!defined('puyuetian'))
	exit('403');

global $readdata, $readuserdata;
$data = $_G['TABLE']['USERGROUP'] -> getData($readuserdata['groupid']);
if ($data) {
	$readuserdata['usergroupname'] = $data['usergroupname'];
} else {
	$readuserdata['usergroupname'] = '自由达人';
}

//精华，置顶
$_G['TEMP']['JHZD'] = '';
if ($readdata['high']) {
	$_G['TEMP']['JHZD'] .= '<span class="layui-badge layui-bg-red">精帖</span>';
}
if ($readdata['top']) {
	$_G['TEMP']['JHZD'] .= '<span class="layui-badge layui-bg-black">置顶</span>';
}

//发表时间人性化
$readlistorder = Cstr($_G['SET']['READLISTORDER'], 'activetime', TRUE, 1, 255);
if ($readlistorder != 'posttime') {
	$readlistorder = 'activetime';
}
$_G['TEMP']['POSTTIME'] = time() - Cnum($readdata[$readlistorder]);
if ($_G['TEMP']['POSTTIME'] < 60) {
	$_G['TEMP']['POSTTIME'] = '刚刚';
} elseif ($_G['TEMP']['POSTTIME'] < 3600) {
	$_G['TEMP']['POSTTIME'] = (int)($_G['TEMP']['POSTTIME'] / 60) . '分钟前';
} elseif ($_G['TEMP']['POSTTIME'] < 86400) {
	$_G['TEMP']['POSTTIME'] = (int)($_G['TEMP']['POSTTIME'] / 3600) . '小时前';
} else {
	$_G['TEMP']['POSTTIME'] = (int)($_G['TEMP']['POSTTIME'] / 86400) . '天前';
}

$_G['TEMP']['READADMINLINK'] = '';
//版主检测
$bkdata = $_G['TABLE']['READSORT'] -> getData($readdata['sortid']);
if (InArray(getUserQX(), 'superman')) {
	if ($readdata['top']) {
		$_G['TEMP']['READADMINLINK'] .= '<a href="javascript:" onclick="layer.confirm(&quot;确认取消该文章置顶？&quot;,{title:&quot;提示&quot;,icon:3},function(index){$.get(\'index.php?c=admincmd&table=read&field=top&value=0&id=' . $readdata['id'] . '&chkcsrfval=' . $_G['CHKCSRFVAL'] . '\',function(){layer.msg(\'取消成功\',{icon:1});location.reload()});layer.close(index)})">取消置顶</a>';
	} else {
		$_G['TEMP']['READADMINLINK'] .= '<a href="javascript:" onclick="layer.confirm(&quot;确认设置该文章置顶？&quot;,{title:&quot;提示&quot;,icon:3},function(index){$.get(\'index.php?c=admincmd&table=read&field=top&value=1&id=' . $readdata['id'] . '&chkcsrfval=' . $_G['CHKCSRFVAL'] . '\',function(){layer.msg(\'设置成功\',{icon:1});location.reload()});layer.close(index)})">设为置顶</a>';
	}
	if ($readdata['high']) {
		$_G['TEMP']['READADMINLINK'] .= '<a href="javascript:" onclick="layer.confirm(&quot;确认取消该文章精华？&quot;,{title:&quot;提示&quot;,icon:3},function(index){$.get(\'index.php?c=admincmd&table=read&field=high&value=0&id=' . $readdata['id'] . '&chkcsrfval=' . $_G['CHKCSRFVAL'] . '\',function(){layer.msg(\'取消成功\',{icon:1});location.reload()});layer.close(index)})">取消精华</a>';
	} else {
		$_G['TEMP']['READADMINLINK'] .= '<a href="javascript:" onclick="layer.confirm(&quot;确认设置该文章精华？&quot;,{title:&quot;提示&quot;,icon:3},function(index){$.get(\'index.php?c=admincmd&table=read&field=high&value=1&id=' . $readdata['id'] . '&chkcsrfval=' . $_G['CHKCSRFVAL'] . '\',function(){layer.msg(\'设置成功\',{icon:1});location.reload()});layer.close(index)})">设为精华</a>';
	}
}
if (InArray(getUserQX(), 'admin') || (InArray($bkdata['adminuids'], $_G['USER']['ID']) && $_G['USER']['ID'])) {
	$_G['TEMP']['READADMINLINK'] .= '<a href="index.php?c=edit&type=read&id=' . $readdata['id'] . '">编辑</a>&nbsp;<a href="javascript:" onclick="layer.confirm(\'确认删除该文章？\',{title:\'提示\',icon:3},function(index){delread(\'' . $readdata['id'] . '\',\'read\',function(){$(\'#listdivbox-' . $readdata['id'] . '\').remove();layer.msg(\'删除成功\',{icon:1})});layer.close(index)})">删除</a>';
}

$_G['TEMP']['SHOW_TS'] = 0;
$_G['TEMP']['IMGS'] = '';
// 视频显示，当显示视频的时候，图片默认不显示
$showvideo = false;
if ($_G['SET']['TEMPLATE_PUYUETIAN_FLY_LISTSHOWVIDEO']) {
	$videos = getHtmlVideos($readdata['content'], 1);
	if (count($videos)) {
		$_G['TEMP']['IMGS'] .= '<div class="pk-row _video" style="width:100%">
		<div class="pk-w-sm-12" style="padding:7px 0">
			<video src="' . $videos[0]['src'] . '" controls="controls" style="width:100%;height:372px;background-color:#000"></video>
		</div>
		</div>';
		$showvideo = true;
		$_G['TEMP']['SHOW_TS'] = 1;
	}
}

//读取图片
$tpxs = Cnum($_G['SET']['TEMPLATE_PUYUETIAN_FLY_READIMGNUM'], false, true, 1);
if ($tpxs && !$showvideo) {
	$imgs = getHtmlImages($readdata['content'], $tpxs);
	$count = count($imgs);
	if ($count) {
		$_G['TEMP']['IMGS'] .= '<div class="pk-row _imgs">';
		for ($i = 0; $i < $count; $i++) {
			switch ($count) {
				case 1:
					$_i = 12;
					break;
				case 2:
					$_i = 6;
					break;
				default:
					$_i = 4;
					break;
			}
			$_G['TEMP']['SHOW_TS'] = 2;
			$_G['TEMP']['IMGS'] .= '<div class="pk-w-sm-' . $_i . '" style="height:188px;padding:7px 5px 10px 5px"><img class="pk-cursor-pointer" src="' . $imgs[$i]['src'] . '" alt="" onclick="LookImage(this)" style="width:100%;height:100%;object-fit:cover;border-radius:2px"></div>';
		}
		$_G['TEMP']['IMGS'] .= '</div>';
	}
}