layui.define(['layer', 'laytpl', 'form', 'element', 'upload', 'util'], function(exports) {

	var $ = layui.jquery,
		layer = layui.layer,
		laytpl = layui.laytpl,
		form = layui.form,
		element = layui.element,
		upload = layui.upload,
		util = layui.util,
		device = layui.device()

		,
		DISABLED = 'layui-btn-disabled';

	//阻止IE7以下访问
	if(device.ie && device.ie < 8) {
		layer.alert('如果您非得使用IE浏览器访问本站，那么请使用IE8+');
	}

	//搜索
	$('.fly-search').on('click', function() {
		layer.open({
			type: 1,
			title: false,
			closeBtn: false
				//,shade: [0.1, '#fff']
				,
			shadeClose: true,
			maxWidth: 10000,
			skin: 'fly-layer-search',
			content: ['<form action="index.php">', '<input type="hidden" name="c" value="app"><input type="hidden" name="a" value="puyuetian_search:index"><input autocomplete="off" placeholder="搜索只需回车一下~" type="text" name="w">', '</form>'].join(''),
			success: function(layero) {
				var input = layero.find('input');
				input.focus();

				layero.find('form').submit(function() {
					var val = input.val();
					if(val.replace(/\s/g, '') === '') {
						return false;
					}
				});
			}
		})
	});

	//手机设备的简单适配
	var treeMobile = $('.site-tree-mobile'),
		shadeMobile = $('.site-mobile-shade')

	treeMobile.on('click', function() {
		$('body').addClass('site-mobile');
	});

	shadeMobile.on('click', function() {
		$('body').removeClass('site-mobile');
	});

	var fly = {};
	exports('fly', fly);
});