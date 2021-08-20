<?php
if (!defined('puyuetian'))
	exit('403');

global $forumdata;

if ($forumdata['url']) {
	$_G['TEMP']['TARGET'] = '_blank';
	$_G['TEMP']['FORUMURL'] = $forumdata['url'];
} else {
	$_G['TEMP']['TARGET'] = '';
	$_G['TEMP']['FORUMURL'] = ReWriteURL('list', "sortid={$forumdata['id']}&page=1");
}

if ($_G['TABLE']['READSORT'] -> getId(array('pid' => $forumdata['id'], 'show' => 1))) {
	$_G['TEMP']['ZBKHTML'] = '<a class="layui-btn layui-btn-primary" href="' . ReWriteURL('forum', "id={$forumdata['id']}") . '">子版块</a>';
} else {
	$_G['TEMP']['ZBKHTML'] = '';
}
