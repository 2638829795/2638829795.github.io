<?php
if (!defined('puyuetian'))
	exit('403');

$_G['TEMP']['GROUPNAME'] = $_G['USERGROUP']['USERGROUPNAME'] ? $_G['USERGROUP']['USERGROUPNAME'] : '自由达人';

$gpshtml = '';
switch ($_G['GET']['C']) {
	case 'list' :
		if ($_G['GET']['SORTID']) {
			$gpshtml = '<li class="layui-hide-xs"><a href="' . ReWriteURL('forum', '') . '">版块列表</a><span style="display:inline-block;color:#e2e2e2;height:10px">&raquo;</span></li>';
			$_sortid = $_G['GET']['SORTID'];
			for ($i = 0; $i < 9; $i++) {
				$sortdata = $_G['TABLE']['READSORT'] -> getData($_sortid);
				if ($sortdata) {
					$_sortid = $sortdata['pid'];
					$gpshtml .= '<li class="layui-hide-xs"><a href="' . ReWriteURL('list', "sortid={$sortdata['id']}&page=1") . '">' . $sortdata['title'] . '</a></li>' . ($_sortid ? '<span style="display:inline-block;color:#e2e2e2;height:10px">&raquo;</span>' : '');
				} else {
					break;
				}
			}
		} else {
			$gpshtml = '<li class="layui-hide-xs"><a href="javascript:">最新动态</a></li>';
		}
		break;
	case 'forum' :
		if ($_G['GET']['ID']) {
			$_id = $_G['GET']['ID'];
			for ($i = 0; $i < 9; $i++) {
				$sortdata = $_G['TABLE']['READSORT'] -> getData($_id);
				if ($sortdata) {
					$_id = $sortdata['pid'];
					$gpshtml = '<li class="layui-hide-xs"><a href="' . ReWriteURL('forum', "id={$sortdata['id']}&page=1") . '">' . $sortdata['title'] . '</a>' . ($i ? '<span style="display:inline-block;color:#e2e2e2;height:10px">&raquo;</span>' : '') . '</li>' . $gpshtml;
				} else {
					break;
				}
			}
			$gpshtml = '<li class="layui-hide-xs"><a href="' . ReWriteURL('forum', '') . '">版块列表</a><span style="display:inline-block;color:#e2e2e2;height:10px">&raquo;</span></li>' . $gpshtml;
		} else {
			$gpshtml .= '<li class="layui-hide-xs"><a href="javascript:">版块列表</a></li>';
			break;
		}
		break;
	case 'read' :
		$readdata = $_G['TABLE']['READ'] -> getData(Cnum($_G['GET']['ID']));
		if ($readdata) {
			global $sortid;
			$_sortid = $sortid = $readdata['sortid'];
			for ($i = 0; $i < 99; $i++) {
				$sortdata = $_G['TABLE']['READSORT'] -> getData($_sortid);
				if ($sortdata) {
					$_sortid = $sortdata['pid'];
					$gpshtml = '<li class="layui-hide-xs"><a href="' . ReWriteURL('list', "sortid={$sortdata['id']}&page=1") . '">' . $sortdata['title'] . '</a><span style="display:inline-block;color:#e2e2e2;height:10px">&raquo;</span></li>' . $gpshtml;
				} else {
					break;
				}
			}
			$gpshtml = '<li class="layui-hide-xs"><a href="' . ReWriteURL('forum', '') . '">版块列表</a><span style="display:inline-block;color:#e2e2e2;height:10px">&raquo;</span></li>' . $gpshtml . '<li class="layui-hide-xs"><a href="javascript:">' . mb_substr(strip_tags($readdata['title']), 0, 32) . '</a></li>';
		}
		break;
	case 'user' :
		$userdata = $_G['TABLE']['USER'] -> getData(Cnum($_G['GET']['ID']));
		if ($userdata) {
			$gpshtml .= '<li class="layui-hide-xs"><a href="javascript:">' . $userdata['nickname'] . '的个人信息</a></li>';
		} else {
			$gpshtml .= '<li class="layui-hide-xs"><a href="javascript:">我的资料</a></li>';
		}
		break;
	case 'app' :
		$gpshtml .= '<li class="layui-hide-xs"><a href="javascript:">应用</a></li>';
		break;
	default :
		break;
}
$_G['TEMP']['GPSHTML'] = $gpshtml;
