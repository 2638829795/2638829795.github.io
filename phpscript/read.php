<?php
if (!defined('puyuetian')) {
	exit('403');
}

$id = Cnum($_G['GET']['ID'], 0, true, 1);
$page = Cnum($_G['GET']['PAGE'], 1, true, 1);
$desc = Cnum($_G['GET']['DESC'], Cnum($_G['SET']['REPLYORDER'], 1, true, 0, 1), true, 0, 1);
$prenum = Cnum($_G['SET']['REPLYLISTNUM'], 10, true, 1);
$spos = ($page - 1) * $prenum;
$template = template('read-2', true, false, false);
$orderfield = $_G['GET']['ORDERFIELD'];
if ($orderfield == 'zannum') {
	$orderfield = 'zannum desc,id';
} else {
	$orderfield = 'id';
}

$readdata = $_G['TABLE']['READ'] -> getData(array('id' => $id, 'del' => 0));
if ($_G['SET']['HIGHSPEED']) {
	$pagecount = $_G['TABLE']['REPLY'] -> getCount(array('rid' => $id));
} else {
	$pagecount = $_G['TABLE']['REPLY'] -> getCount(array('rid' => $id, 'del' => 0));
}
$pagecount = Cnum(ceil($pagecount / $prenum), 1, true, 1);
if ($page > $pagecount) {
	$page = $pagecount;
}
if (!$readdata) {
	if ($_G['SET']['DELETEDREADSHOWTYPE'] == 404) {
		header('HTTP/1.1 404 Not Found');
		header('status: 404 Not Found');
		$_G['HTMLCODE']['MAIN'] = template('error', true);
		template($_G['TEMPLATE']['MAIN']);
		exit;
	}
	PkPopup('{
		content:"该主题已被删除或正在审核",
		icon:2,
		hideclose:1,
		shade:1
	}');
}

//各种检测
if ((!InArray($_G['USERGROUP']['QUANXIAN'], 'lookread') && $_G['USERGROUP']['ID']) || (!InArray($_G['USER']['QUANXIAN'], 'lookread') && !$_G['USERGROUP']['ID'])) {
	PkPopup('{
		content:"您无权阅读文章",
		icon:2,
		hideclose:1,
		shade:1
	}');
}

//用户阅读权限的检测
if (!chkReadSortQx($readdata['sortid'], 'looklevel') || (($_G['USERGROUP']['ID'] && (Cnum($readdata['readlevel']) > Cnum($_G['USERGROUP']['READLEVEL']))) || (!$_G['USERGROUP']['ID'] && (Cnum($readdata['readlevel']) > Cnum($_G['USER']['READLEVEL']))))) {
	PkPopup('{
		content:"您的阅读权限太低或您的用户组不被允许",
		icon:2,
		hideclose:1,
		shade:1
	}');
}

if (!isset($_SESSION['HS_LOOKREAD_' . $id]) || $_G['SET']['READLOOKNUMADDTYPE']) {
	$_SESSION['HS_LOOKREAD_' . $id] = $readdata['looknum'];
	$_G['TABLE']['READ'] -> newData(array('id' => $id, 'looknum' => ($readdata['looknum'] + 1)));
}

//整片文章回复后查看的检测
if ($readdata['replyafterlook'] && $_G['USER']['ID'] != $readdata['uid'] && $_G['USER']['ID'] != 1) {
	if ($_G['USER']['ID']) {
		if (!$_G['TABLE']['REPLY'] -> getId(array('rid' => $readdata['id'], 'uid' => $_G['USER']['ID'], 'del' => 0))) {
			$readdata['content'] = '<p class="pk-width-all pk-padding-15 pk-text-center pk-text-default" style="border:dashed 1px orangered">该文章设置了回复查看，请回复后查看内容</p>';
		}
	} else {
		$readdata['content'] = '<p class="pk-width-all pk-padding-15 pk-text-center pk-text-default" style="border:dashed 1px orangered">您需要登录并回复后才可以查看该文章内容</p>';
	}
}
//部分内容回复后可见
if (strpos($readdata['content'], '<p class="PytReplylook">') !== false && $_G['USER']['ID'] != $readdata['uid'] && $_G['USER']['ID'] != 1) {
	$_ls = '';
	if ($_G['USER']['ID']) {
		if (!$_G['TABLE']['REPLY'] -> getId(array('rid' => $readdata['id'], 'uid' => $_G['USER']['ID'], 'del' => 0))) {
			$_ls = '<p class="PytReplylook">该内容设置了回复查看，请回复后查看隐藏内容</p>';
		}
	} else {
		$_ls = '<p class="PytReplylook">您需要登录并回复后才可以查看隐藏的内容</p>';
	}
	if ($_ls) {
		$readdata['content'] = preg_replace('/\<p class="PytReplylook"\>[\s\S]+?\<\/p\>/', $_ls, $readdata['content']);
	}
}
//文章所属分类
$sortid = $readdata['sortid'];
if (!$_G['GET']['SORTID']) {
	$_G['GET']['SORTID'] = $sortid;
}
$bkdata = $_G['TABLE']['READSORT'] -> getData($sortid);
$_G['TEMP']['BKADMIN'] = (InArray($bkdata['adminuids'], $_G['USER']['ID']) && $_G['USER']['ID']) ? true : false;
if ($readdata['uid']) {
	$readuserdata = $_G['TABLE']['USER'] -> getData($readdata['uid']);
} else {
	$readuserdata = JsonData($_G['SET']['GUESTDATA']);
}
// 是否显示最后编辑时间
if ($_G['SET']['SHOWREADLASTEDITTIME'] && $readdata['lastedituid']) {
	if ($readdata['lastedituid'] == $readdata['uid']) {
		$lasteditud = $readuserdata;
	} else {
		$lasteditud = $_G['TABLE']['USER'] -> getData($readdata['lastedituid']);
	}
	$readdata['content'].='<div class="readedittime" style="text-align:center;font-size:12px;margin:15px 0;padding:5px 0;letter-spacing:1px;color:#aaa">本文章最后由 <a target="_blank" class="pk-hover-underline" href="' . ReWriteURL('center', 'uid=' . $lasteditud['id']) . '">' . $lasteditud['username'] . '</a> 于 <span>' . date('Y-m-d H:i', $readdata['lastedittime']) . '</span> 编辑</div>';
}
if ($spos == 0) {
	//==============================置顶回复处理===========================
	$sql = 'where `rid`=' . Cnum($readdata['id']);
	if (!$_G['SET']['HIGHSPEED']) {
		$sql .= ' and `del`=0';
	}
	$sql .= ' and `top`=1 order by ' . $orderfield;
	if ($desc) {
		$sql .= ' desc';
	}
	$replydatas = $_G['TABLE']['REPLY'] -> getDatas(0, 0, $sql);
	if ($replydatas) {
		foreach ($replydatas as $replydata) {
			if ($replydata['uid']) {
				$replyuserdata = $_G['TABLE']['USER'] -> getData($replydata['uid']);
			} else {
				$replyuserdata = JsonData($_G['SET']['GUESTDATA']);
			}
			$replyhtmlcode .= template('read-2', true, $template);
		}
	}
}
//==============================普通回复处理===========================
$sql = 'where `rid`=' . Cnum($readdata['id']);
if (!$_G['SET']['HIGHSPEED']) {
	$sql .= ' and `del`=0';
}
$sql .= ' and `top`=0 order by ' . $orderfield;
if ($desc) {
	$sql .= ' desc';
}
$replydatas = $_G['TABLE']['REPLY'] -> getDatas($spos, $prenum, $sql);
if ($replydatas) {
	foreach ($replydatas as $replydata) {
		if ($replydata['uid']) {
			$replyuserdata = $_G['TABLE']['USER'] -> getData($replydata['uid']);
		} else {
			$replyuserdata = JsonData($_G['SET']['GUESTDATA']);
		}
		$replyhtmlcode .= template('read-2', true, $template);
	}
}
//seo重塑
$title = htmlspecialchars(strip_tags($readdata['title']), ENT_QUOTES);
$keywords = htmlspecialchars($readdata['label'], ENT_QUOTES);
$description = strip_tags($readdata['content']);
$description = str_replace("\t", ' ', $description);
$description = preg_replace('/[ ]+/', ' ', $description);
$description = str_replace(array("\r","\n"), '', $description);
$description = mb_substr(htmlspecialchars($description, ENT_QUOTES), 0, 100);
$_G['SET']['WEBKEYWORDS'] = $keywords ? $keywords : $title;
$_G['SET']['WEBDESCRIPTION'] = $description ? $description : $title;
if ($_G['SET']['READSEOADDWORDS']) {
	$_G['SET']['WEBTITLE'] = $title . (Cnum($_G['GET']['PAGE'], 1, true, 1) != 1 ? '-第' . Cnum($_G['GET']['PAGE'], 1, true, 1) . '页' : '') . "-{$bkdata['title']}-{$_G['SET']['WEBADDEDWORDS']}";
} else {
	$_G['SET']['WEBTITLE'] = $title . (Cnum($_G['GET']['PAGE'], 1, true, 1) != 1 ? '-第' . Cnum($_G['GET']['PAGE'], 1, true, 1) . '页' : '');
}
$_G['HTMLCODE']['OUTPUT'] .= template('read-1', true);
$_G['HTMLCODE']['OUTPUT'] .= $replyhtmlcode;
$_G['HTMLCODE']['OUTPUT'] .= template('read-3', true);
