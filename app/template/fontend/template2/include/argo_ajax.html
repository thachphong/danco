<style type="text/css">
.ui-dialog .ui-dialog-content.ui-dialog-spinner{
    background: url("<%$smarty.const.ACW_BASE_URL%>css/images/ajax-loader.gif") no-repeat scroll 5px 15px transparent;
    padding: 30px 4px 4px 70px;
    border: 2px solid #DDDDDD;
    font-weight: 900;
    line-height: 1em;
    font-size: 1.1em;
    color: #737373;
}
.ui-icon.large {background-size: 384px 360px; width: 24px;height: 24px;margin-top: 0;}
.ui-icon-info.large { background-position: -24px -216px; }
.ui-icon-circle-close.large { background-position: -48px -288px; }
</style>
<script type="text/javascript">
// ajaxライブラリ処理
//  プロトタイプにメソッドを追加する。
//  コンストラクタを Prototype に実装する。
function Argo_ajax() {
    this.initialize.apply(this, arguments);
}
// コンストラクタ
Argo_ajax.prototype.initialize = function(data_type) {
    this.ajax_option = {
		cache: false,
		dataType: data_type,
		statusCode: {
			401 : function () {
				sesstion_err_msg();
			}
		}
	};
};

function sesstion_err_msg()
{
	$('#modal_message').dialog('close');
	Argo_ajax.message_box_btns("警告"
	, "一定時間操作されなかったため、再度、ログインが必要です。",
		{
			"ログイン画面へ戻る": function() {
				$(this).dialog("close");
				location.href = '<%$smarty.const.ACW_BASE_URL%>login';
			}
		}
	);
}

Argo_ajax.prototype.always_func = function (data){};	// デフォルト
Argo_ajax.prototype.done_func = function (data){};	// デフォルト
Argo_ajax.prototype.fail_func = function (data) {
	Argo_ajax.message_box('Lỗi hệ thống', data.responseText);
};	// デフォルト

// ajax接続 POST
Argo_ajax.prototype.connect = function (action_type, ajax_url, ajax_data) {
	var aa_this = this;
	Argo_ajax.loading(true);
	var opt =　this.ajax_option;
	opt.type =  action_type;
	opt.url = '<%$smarty.const.ACW_BASE_URL%>' + ajax_url;
	if (ajax_data) {
		opt.data = ajax_data;
	} else if (opt.data) {
		delete opt.data;
	}
	$.ajax(opt)
		.always(function (data) {
			Argo_ajax.loading(false);
			aa_this.always_func(data);
		})	// alwaysを一番先に実行
		.done(aa_this.done_func)
		.fail(aa_this.fail_func);
};

// クラスメソッド
// ローディングダイアログ
Argo_ajax.loading = function (flag) {
	if (flag) {
		$("#loading_dialog").loading();	// loading
	} else {
		$("#loading_dialog").loading("loadStop");	// loading閉じる
	}
};

// クラスメソッド
// メッセージボックス
Argo_ajax.message_box = function (title, msg, closed_func, style) { //Edit NBKD-1107 - Tin-VNIT - 2015/06/24
	$('#modal_message').empty();
	$('#modal_message').prepend('<span style="display: block; padding-top: 4px;padding-left: 30px;'+style+'">'+msg+'</span>'); //Edit NBKD-1107 - Tin-VNIT - 2015/06/24
	
	//Add Start - Trung VNIT - 2014/08/04 fix title when empty value
	if(title.trim() == ''){
		title = " ";//transparency character Alt + 0160
	}
	//Add End - Trung VNIT - 2014/08/04

	$('#modal_message').prepend('<p style="width: 5px; margin-top: 7px;"><span class="ui-icon ui-icon-info large" style="float:left; margin:0 7px 5px 0;"></span></p>');
	$('#modal_message').dialog({
		modal:true,
		draggable:false,
		title:title,
		height:'auto',
		width:400,
		closeOnEscape: false,
		buttons:{
				'OK': function() {
					$(this).dialog('close');
					if (closed_func) {
						closed_func();
					}
				}
		},
		open: function(event, ui) {
			// 右上の×ボタン非表示
			$('#modal_message').parent().children().find('.ui-dialog-titlebar-close').hide();
		},
		close: function() {//Add - Minh VNIT - 2014/08/04
			$(".ui-dialog-titlebar-close").show();
		}
	});
};

//Add Start - Trung VNIT - 2014/08/04
Argo_ajax.message_box_error = function (title, msg, closed_func, style) { //Edit NBKD-1107 - Tin-VNIT - 2015/06/24
	$('#modal_message').empty();
        $('#modal_message').prepend('<span style="display: block; padding-top: 4px;padding-left: 30px;'+style+'">'+msg+'</span>'); //Edit NBKD-1107 - Tin-VNIT - 2015/06/24

	$('#modal_message').prepend('<p style="width: 5px; margin-top: 7px;"><span class="ui-icon ui-icon-circle-close large" style="float:left; margin:0 7px 5px 0;"></span></p>');

	//Add Start - Trung VNIT - 2014/08/04 fix title when empty value
	if(title.trim() == ''){
		title = " ";//transparency character Alt + 0160
	}
	//Add End - Trung VNIT - 2014/08/04

	$('#modal_message').dialog({
		modal:true,
		draggable:false,
		title:title,
		height:'auto',
		width:400,
		closeOnEscape: false,
		buttons:{
				'OK': function() {
					$(this).dialog('close');
					if (closed_func) {
						closed_func();
					}
				}
		},
		open: function(event, ui) {
			// 右上の×ボタン非表示
			$('#modal_message').parent().children().find('.ui-dialog-titlebar-close').hide();
		},
		close: function() {//Add - Minh VNIT - 2014/08/04
			$(".ui-dialog-titlebar-close").show();
		}
	});
};
//Add End - Trung VNIT - 2014/08/04

Argo_ajax.loading_message_init = 'Loading...';
Argo_ajax.loading_message = Argo_ajax.loading_message_init;

// クラスメソッド
// 選択ボックス
Argo_ajax.set_loading_message = function (msg) {
	if (msg) {
		// 初期値に戻す
		Argo_ajax.loading_message = msg;
	} else {
		// 初期値に戻す
		Argo_ajax.loading_message = Argo_ajax.loading_message_init;
	}
};

Argo_ajax.message_box_btns = function (title, msg, btns , wth) {
	$('#modal_message').empty();
	$('#modal_message').prepend(msg);
	
	//Add Start NBKD-50 Minh Vnit 2014/12/15
	var width = 400;
	if(wth != null){
		width = wth;
	}
	//Add End NBKD-50 Minh Vnit 2014/12/15
	
	//Add Start - Trung VNIT - 2014/08/04 fix title when empty value
	if(title.trim() == ''){
		title = " ";//transparency character Alt + 0160
	}
	//Add End - Trung VNIT - 2014/08/04

	$('#modal_message').prepend('<span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>');

	$('#modal_message').dialog({
		modal:true,
		draggable:false,
		title:title,
		height:'auto',
		width:width,
		buttons: btns,
		closeOnEscape: false,
		open: function(event, ui) {
			// 右上の×ボタン非表示
			$(".ui-dialog-titlebar-close").hide();
		},
		close: function() {//Add - Minh VNIT - 2014/08/04
			$(".ui-dialog-titlebar-close").show();
		}
	});
};

// クラスメソッド
// 選択ボックス
Argo_ajax.ok_cancel_box = function (title, msg, ok_func, cancel_func) {
	$('#modal_message').empty();
	$('#modal_message').prepend(msg);

	//Add Start - Trung VNIT - 2014/08/04 fix title when empty value
	if(title.trim() == ''){
		title = " ";//transparency character Alt + 0160
	}
	//Add End - Trung VNIT - 2014/08/04

	$('#modal_message').dialog({
		modal:true,
		draggable:false,
		title:title,
		height:'auto',
		width:400,
		closeOnEscape: false,
		buttons:{
				'Đóng': function() {
					$(this).dialog('close');
					if (cancel_func) {
						cancel_func();
					}
				},
				'ＯＫ': function() {
					$(this).dialog('close');
					if (ok_func) {
						ok_func();
					}
				}
			},
		open: function() {
			// 2番目のボタンを規定値に eq(0)だと1番目
			$(this).siblings('.ui-dialog-buttonpane').find('button:eq(1)').focus();
			// 右上の×ボタン非表示
			$(".ui-dialog-titlebar-close").hide();
		},
		close: function() {//Add - Minh VNIT - 2014/08/04
			$(".ui-dialog-titlebar-close").show();
		}
	 });
};

//Add Start - NBKD-66 - TrungVNIT - 2014/12/29
Argo_ajax.get_value = function (data, key) {
    var result = '';
    $.each(data, function(i) {
        if(data[i]['name'] == key){
            if(data[i]['value'] != ''){
                result = data[i]['value'];
            }
        }
    });
    return result;
};
//Add End - NBKD-66 - TrungVNIT - 2014/12/29

//Add Start - NBKD-1133 - TrungVNIT - 2015/06/01
Argo_ajax.getParamURL = function (name) {
    var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
    return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
}
//Add End - NBKD-1133 - TrungVNIT - 2015/06/01

// _形式のIDで本当のIDを獲得
Argo_ajax.get_split_id = function (id_str, spl_r) {
	var spl = id_str.split('_');
	if (spl_r) {
		spl_r = spl_r.concat(spl);
	}
	return spl[spl.length - 1];
};

// _形式のIDを獲得
Argo_ajax.split_id = function (id_str, arr) {
	var spl = id_str.split('_');
	var result = {};
	var name;
	var i;
	for (i = 0; i < arr.length; i ++) {
		name = arr[i];
		result[name] = spl[i];
	}
	return result;
};

(function($) {
	$.widget("argo.loading", $.ui.dialog, {
		options: {
			// your options
			spinnerClassSuffix: 'spinner',
			//spinnerHtml: 'Loading',// allow for spans with callback for timeout...
			minHeight: 80,
			minWidth: 220,
			height: 'auto',
			width: 'auto',
			modal: true
		},

		_create: function() {
			$.ui.dialog.prototype._create.apply(this);
			// constructor
			$(this.uiDialog).children('*').hide();
			var self = this,
			options = self.options;
			self.uiDialogSpinner = $('.ui-dialog-content',self.uiDialog)
				.html(Argo_ajax.loading_message)
				.addClass('ui-dialog-'+options.spinnerClassSuffix);
		},
		// other methods
		loadStart: function(newHtml){
			/*if(typeof(newHtml)!='undefined'){
				this._setOption('spinnerHtml',newHtml);
			}*/
			this.open();
		},
		loadStop: function(){
			//this._setOption('spinnerHtml',this.options.spinnerHtml);
			this.close();
		}
	});
        //Add Start - NBKD-41 - TrungVNIT - 2015/02/13
        $.getParam = (function(a) {
            if (a == "") return {};
            var b = {};
            for (var i = 0; i < a.length; ++i)
            {
                var p=a[i].split('=');
                if (p.length != 2) continue;
                b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
            }
            return b;
        })(window.location.search.substr(1).split('&'));
        //Add End - NBKD-41 - TrungVNIT - 2015/02/13
})(jQuery);


</script>
<div id="loading_dialog"></div>
<div id="modal_message"></div>