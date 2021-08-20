<?php
if (!defined('puyuetian')) {
	exit('403');
}

if ($_G['GET']['SUBMIT'] == 'orderlogin') {
	$array = array();
	$regtime = time();
	$array['uid'] = $_G['USER']['ID'];
	$array['idcode'] = CreateRandomString(16);
	$array['regtime'] = $regtime;
	$_G['TABLE']['APP_HADSKYCLOUDSERVER_WEIXINLOGIN_RECORD'] -> newData($array);
	$id = $_G['TABLE']['APP_HADSKYCLOUDSERVER_WEIXINLOGIN_RECORD'] -> getId(array('uid' => $_G['USER']['ID'], 'idcode' => $array['idcode'], 'regtime' => $regtime));
	ExitJson(array('id' => $id, 'idcode' => $array['idcode']), true);
}

if ($_G['GET']['SUBMIT'] == 'chklogin') {
	$data = $_G['TABLE']['APP_HADSKYCLOUDSERVER_WEIXINLOGIN_RECORD'] -> getData(array('id' => $_GET['id'], 'idcode' => $_GET['idcode'], 'uid' => $_G['USER']['ID']));
	if ($data['regtime'] + 86400 < time()) {
		if ($_G['GET']['GOLOGIN']) {
			PkPopup('{
				content:"登录过期",
				icon:2,
				shade:1,
				hideclose:1,
				nomove:1,
				submit:function(){
					location.href = "index.php"
				}
			}');
		}
		ExitJson('登录过期');
	}
	if ($data['logtime']) {
		if ($_G['GET']['GOLOGIN']) {
			PkPopup('{
				content:"已被登录",
				icon:2,
				shade:1,
				hideclose:1,
				nomove:1,
				submit:function(){
					location.href = "index.php"
				}
			}');
		}
		ExitJson('已经被登录');
	}
	if (!$data['openid']) {
		if ($_G['GET']['GOLOGIN']) {
			PkPopup('{
				content:"openid为空",
				icon:2,
				shade:1,
				hideclose:1,
				nomove:1,
				submit:function(){
					location.href = "index.php"
				}
			}');
		}
		ExitJson('openid为空');
	}
	$_G['TABLE']['APP_HADSKYCLOUDSERVER_WEIXINLOGIN_RECORD'] -> newData(array('id' => $_GET['id'], 'logtime' => time()));
	if (!$_G['USER']['ID']) {
		//用户登录或注册
		//$userdata = UserLogin("where locate('[" . mysqlstr($data['openid'], false, '', false) . "]',`weixin_openids`)>0", false);
		$userdata = UserLogin(array('wxopenid' => $data['openid']), false);
		if ($userdata) {
			if ($_G['GET']['GOLOGIN']) {
				PkPopup('{
					content:"登录成功",
					icon:1,
					shade:1,
					hideclose:1,
					nomove:1,
					submit:function(){
						location.href = "index.php?c=center"
					}
				}');
			}
			ExitJson('登录成功', true);
		} else {
			//用户注册
			$_SESSION['APP_HADSKYCLOUDSERVER_WEIXIN_OPENID'] = $data['openid'];
			if ($_G['GET']['GOLOGIN']) {
				ExitGourl('index.php?c=reg');
			}
			ExitJson(array('gourl' => 'index.php?c=reg'), true);
		}
	} else {
		//用户绑定
		$array['id'] = $_G['USER']['ID'];
		//$array['weixin_openids'] = str_replace("[{$data['openid']}]", '', $_G['USER']['WEIXIN_OPENIDS']) . "[{$data['openid']}]";
		$array['wxopenid'] = $data['openid'];
		$_G['TABLE']['USER'] -> newData($array);
		if ($_G['GET']['GOLOGIN']) {
			PkPopup('{
				content:"恭喜，绑定/换绑微信号成功！",
				icon:1,
				shade:1,
				hideclose:1,
				nomove:1,
				submit:function(){
					location.href = "index.php?c=center"
				}
			}');
		}
		ExitJson('恭喜，绑定/换绑微信号成功！', true);
	}
}

//$sitekey2 = md5(md5($_GET['domain']) . md5($_G['SET']['APP_HADSKYCLOUDSERVER_SITEKEY']) . md5($_GET['createtime']) . md5($_GET['timeout']));
//5.2.1版本后的新安全机制
$sitekey3 = md5(md5($_G['SYSTEM']['DOMAIN']) . md5($_G['SET']['APP_HADSKYCLOUDSERVER_SITEKEY']) . md5($_GET['createtime']) . md5($_GET['timeout']) . md5($_G['GET']['S']) . md5($_GET['safernd']));
if (Cnum($_GET['createtime']) + Cnum($_GET['timeout']) > time()) {
	if ($_GET['sitekey3'] == $sitekey3) {
		$data = json_decode($_GET['data'], true);
		if (!$data['openid']) {
			$_G['HTMLCODE']['TIPJS'] = 'location.href="index.php?c=login"';
			$_G['HTMLCODE']['TIP'] = 'openid参数错误';
			$_G['HTMLCODE']['OUTPUT'] .= template('tip', true);
		} else {
			if (Cnum($_GET['_id'])) {
				$_G['TABLE']['APP_HADSKYCLOUDSERVER_WEIXINLOGIN_RECORD'] -> newData(array('id' => $_GET['_id'], 'openid' => $data['openid']));
				if ($_GET['json'] == 'yes') {
					ExitJson('登录成功', true);
				} else {
					// 不存在说明微信内部，直接登录该账号
					$ld = $_G['TABLE']['APP_HADSKYCLOUDSERVER_WEIXINLOGIN_RECORD'] -> getData($_GET['_id']);
					ExitGourl("index.php?c=app&a=hadskycloudserver:index&s=weixinlogin&submit=chklogin&id={$ld['id']}&idcode={$ld['idcode']}&gologin=1");
					// $_G['HTMLCODE']['TIPJS'] = 'window.close();WeixinJSBridge.call(\'closeWindow\');';
					// $_G['HTMLCODE']['TIP'] = '登录成功';
					// $_G['HTMLCODE']['OUTPUT'] .= template('tip', TRUE);
				}
			} else {
				if ($_GET['json'] == 'yes') {
					ExitJson('id参数错误');
				} else {
					$_G['HTMLCODE']['TIPJS'] = 'location.href="index.php?c=login"';
					$_G['HTMLCODE']['TIP'] = 'id参数错误';
					$_G['HTMLCODE']['OUTPUT'] .= template('tip', true);
				}
			}
		}
	} else {
		if ($_GET['json'] == 'yes') {
			ExitJson('sitekey参数错误');
		} else {
			$_G['HTMLCODE']['TIPJS'] = 'location.href="index.php?c=login"';
			$_G['HTMLCODE']['TIP'] = 'sitekey参数错误';
			$_G['HTMLCODE']['OUTPUT'] .= template('tip', true);
		}
	}
} else {
	if ($_GET['json'] == 'yes') {
		ExitJson('操作超时，请刷新页面重试');
	} else {
		$_G['HTMLCODE']['TIPJS'] = 'location.href="index.php?c=login"';
		$_G['HTMLCODE']['TIP'] = '操作超时，请刷新页面重试';
		$_G['HTMLCODE']['OUTPUT'] .= template('tip', true);
	}
}
