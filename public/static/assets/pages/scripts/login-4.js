var Login = function () {
	//登录按钮
	var login_in_event=function(){
		$(".login_btn").click(function(event) {
			$("form.login_form").submit();
		});
	}

	//刷新验证码
	var refresh_verify=function(){
		$(".verifyimg_event").click(function(event) {
			$(".verifyimg_event").attr("src","/static/assets/global/img/loading-spinner-grey.gif");

			$.post('/manager/Login/refresh_verify', function(data, textStatus, xhr) {
				if(data){
					$(".verifyimg_event").attr("src",data);
				}else{
					$(".alert_modal").find('.modal-body').text('验证码获取失败');
	                			$(".alert_modal").modal('show');
				}
			});
		});
	}

	var handleLogin = function() {
		var allow_verify_toggle=false;	//验证码必填的开关
		if(typeof(allow_verify) != "undefined"){	//假如允许有验证码，就打开验证的开关
			if(allow_verify){
				allow_verify_toggle=true;
			}
		}

		$('.login-form').validate({
	            errorElement: 'span', //default input error message container
	            errorClass: 'help-block', // default input error message class
	            focusInvalid: true, // do not focus the last invalid input
	            rules: {
	                username: {
	                    required: true
	                },
	                password: {
	                    required: true
	                },
	                verify: {
	                    required: allow_verify_toggle
	                }
	            },

	            messages: {
	                username: {
	                    required: "用户名为必填项"
	                },
	                password: {
	                    required: "密码为必填项"
	                },
	                verify: {
	                    required: "验证码为必填项"
	                }
	            },

	            invalidHandler: function (event, validator) { //display error alert on form submit   
	                $('.alert-danger', $('.login-form')).show();

	                if(localStorage.nine_login==2 && window_width<=767){	//假如是9宫格形式错误提示
	                	$(".login .content").removeClass('content_nine');
	                	if(allow_verify){
	                		$(".login .content").addClass('content_nine_verify_error');
	                	}else{
	                		$(".login .content").addClass('content_nine_error');
	                	}
	                }
	            },

	            highlight: function (element) { // hightlight error inputs
	                $(element)
	                    .closest('.form-group').addClass('has-error'); // set error class to the control group

	                    if(localStorage.nine_login==2 && window_width<=767){	//假如是9宫格形式错误提示
	                	$(".login .content").removeClass('content_nine');
	                	if(allow_verify){
	                		$(".login .content").addClass('content_nine_verify_error');
	                	}else{
	                		$(".login .content").addClass('content_nine_error');
	                	}
	                }
	            },

	            success: function (label) {
	                label.closest('.form-group').removeClass('has-error');
	                label.remove();

	                if(localStorage.nine_login==2 && window_width<=767){	//假如是9宫格形式错误提示
	                	$(".login .content").removeClass('content_nine_verify_error');
	                	$(".login .content").removeClass('content_nine_error');
	                	$(".login .content").addClass('content_nine');
	                }
	            },

	            errorPlacement: function (error, element) {
	                error.insertAfter(element.closest('.input-icon'));
	            },

	            submitHandler: function (form) {
	                //form.submit();

	                if(allow_login_error){	//是否开启限制用户登录失败的次数验证
	                	//判断是否过了禁止登录的时间
	                	if(check_error_wait_time()){}else{
	                		return false;
	                	}
		    }

	                //loading
		   App.blockUI({
		       boxed: true,
		       message: '登录中...'
		    });

		   localStorage.prev_login_username=$('#username').val();	//记录登录的用户名到html5存储里面
		   
	                $.post(form.action, $(form).serialize(), function(data, textStatus, xhr) {
	                	App.unblockUI();	//解除loading

	                	if(data.status){
	                		App.blockUI({
					boxed: true,
					message: '页面跳转中...'
				});
	                		localStorage.login_error_times=0;	//重新让失败次数归零
	                		location.href=data['url'];
	                	}else{
	                		switch(data.type){
	                			case 0:    //非法登录访问
	                				$(".alert_modal").find('.modal-body').text(data.info);
	                				$(".alert_modal").modal('show');
	                				break;
	                			case 1:    //验证码输入错误
	                				$(".verifyimg_event").attr("src",data['src']);
	                				rewrite_verify_code('输入的验证码有误');
	                				break;
	                			case 2:case 3:    //用户名和密码错误，或账户被禁用
	                				if(allow_login_error){	//是否开启限制用户登录失败的次数验证
	                					//localStorage.clear();	//调试
	                					//return false;

	                					if(localStorage.login_error_times){}else{	//html5本地存储失败次数
	                						localStorage.login_error_times=0;	//初始化
	                					}

	                					if(localStorage.login_error_times >= login_error_times){	//假如登录失败的次数超过了后台设定的次数，就一段时间禁止登录
								var t=new Date();//客户端当前时间
								var t_s=t.getTime();//转化为时间戳毫秒数
								var nt=t_s+1000*error_wait_time;	//设置等待时间
								var wait_time_show=error_wait_time / 60;	//转化等待的时间（单位：分钟）
								localStorage.error_wait_time=nt;	//存储在本地

								$(".alert_modal").find('.modal-body').html('登录失败次数过多，已达到'+login_error_times+'次，请等待：<b style="color:#f00">'+wait_time_show+'</b>分钟后再尝试');
                							$(".alert_modal").modal('show');
                							
                							localStorage.login_error_times=0;	//重新让失败次数归零
		                				}else{
		                					localStorage.login_error_times=parseInt(localStorage.login_error_times)+1;	//累计失败次数
		                					var show_notice_times=login_error_times - localStorage.login_error_times;	//剩余登录次数
		                					$(".alert_modal").find('.modal-body').html(data['info']+'，登录失败，还剩：<b style="color:#f00">'+show_notice_times+'</b>次机会');
                							$(".alert_modal").modal('show');
		                				}
		                				$(".verifyimg_event").attr("src",data['src']);
                						rewrite_verify_code('请重新输入验证码');
		                				return false;
	                				}

	                				$(".alert_modal").find('.modal-body').text(data.info);
	                				$(".alert_modal").modal('show');

	                				$(".verifyimg_event").attr("src",data['src']);
	                				rewrite_verify_code('请重新输入验证码');
	                				break;
	                		}
	                	}
	                });
	            }
	        });

	        $('.login-form input').keypress(function (e) {
	            if (e.which == 13) {
	            	$('.login-form').submit();
	                // if ($('.login-form').validate().form()) {
	                //     $('.login-form').submit();
	                // }
	                return false;
	            }
	        });
	}

	//重新填写验证码的方法
	var rewrite_verify_code = function(txt){	//参数（提示文字）
		$(".verify_input").parents('.form-group:first').removeClass('has-error');
		$(".verify_input").parents('.form-group:first').addClass('has-error');
		$(".verify_input").parents('.form-group:first').find('span.help-block').remove();
		$(".verify_input").parents('.form-group:first').append('<span class="help-block">'+txt+'</span>');
		$(".verify_input").focus();
		$(".verify_input").unbind('keydown');
		$(".verify_input").keydown(function(event) {
			$(".verify_input").parents('.form-group:first').find('span.help-block').remove();
			$(".verify_input").parents('.form-group:first').removeClass('has-error');
		});
	}

	//判断是否过了禁止登录的时间
	var check_error_wait_time=function(){
		if(localStorage.error_wait_time){	//判断是否过了禁止登录的时间
			var t=new Date();//客户端当前时间
			var t_s=t.getTime();//转化为时间戳毫秒数
			if(localStorage.error_wait_time > t_s){	//假如还没过禁止时间，就提示用户
				var wait_time=localStorage.error_wait_time - t_s;	//算出等待的时间差
				var wait_time_show=parseInt(wait_time / 1000 / 60);	//转化等待的时间（单位：分钟）
				$(".alert_modal").find('.modal-body').html('登录失败次数过多，请等待：<b style="color:#f00">'+wait_time_show+'</b>分钟后再尝试');
				$(".alert_modal").modal('show');
				return false;
			}else{
				return true;
			}
		}else{
			return true;
		}
	}

	//自动填入上次登录的用户名
	var auto_login_username=function(){
		if(record_login_username && localStorage.prev_login_username){	//假如允许记录上次登录的用户，且有记录值，就执行填入
			$("#username").val(localStorage.prev_login_username);
		}
	}

	//绑定更改为9宫格方式登录
	var turn_nine_login=function(){
		if(nine_login){	//假如开启九宫格登录，就执行
			if(localStorage.nine_login==2 && window_width<=767){	//假如本地记录为九宫格登录，且为手机设备的时候执行
				nine_login_style();	//变更登录方式(9宫格)
			}else{
				default_login_style();	//变更登录方式(传统)
			}

			$(".nine_btn").click(function(event) {
				if(localStorage.nine_login==1){	//假如是默认登录方式，就变更登录方式(9宫格)
					nine_login_style();	//变更登录方式(9宫格)
				}else{	//假如是9宫格登录方式，就变更登录方式(传统)
					default_login_style();	//变更登录方式(传统)
				}
			});
		}
	}
	//变更登录方式(9宫格)
	var nine_login_style=function(){
		$(".login_btn").hide();	//隐藏登录按钮
		$(".password_group").hide();	//隐藏密码输入框
		$("#password").val('nine');	//随意赋予个值，跳过必填验证
		localStorage.nine_login=2;	//标识9宫格登录
		$(".nine_btn").text('使用传统方式登录');
		$(".content_event").addClass('content_nine');	//调整登录框背景样式
		$('.login-form input').unbind('keypress');	//消除绑定回车提交表单事件
		$(".login_top_divide_event").addClass('login_top_divide_nine');	//更改顶部间距
		$("#nine_box").addClass('visible-xs');	//手机设备识别样式
		$("#nine_box").show();	//显示9宫格
	}
	//变更登录方式(传统)
	var default_login_style=function(){
		$(".login_btn").show();	//显示登录按钮
		$(".password_group").show();	//显示密码输入框
		$("#password").val('');	//清空值
		$(".nine_password").val('');    //清空值
		localStorage.nine_login=1;	//标识默认登录
		$(".nine_btn").text('使用九宫格方式登录');
		$(".content_event").removeClass('content_nine');	//调整登录框背景样式
		$('.login-form input').keypress(function(e){		//绑定回车提交表单事件
			if (e.which == 13){
				$('.login-form').submit();
				return false;
			}
		});
		$(".login_top_divide_event").removeClass('login_top_divide_nine');	//更改顶部间距
		$("#nine_box").removeClass('visible-xs');	//去除手机设备识别样式
		$("#nine_box").hide();		//隐藏9宫格
		if(localStorage.nine_login==1 && window_width<=767){	//假如是9宫格形式错误提示
			$(".login .content").removeClass('content_nine_verify_error');
	                	$(".login .content").removeClass('content_nine_error');
	                	$(".login .content").removeClass('content_nine');
		}
	}

	// var handleForgetPassword = function () {
	// 	$('.forget-form').validate({
	//             errorElement: 'span', //default input error message container
	//             errorClass: 'help-block', // default input error message class
	//             focusInvalid: false, // do not focus the last invalid input
	//             ignore: "",
	//             rules: {
	//                 email: {
	//                     required: true,
	//                     email: true
	//                 }
	//             },

	//             messages: {
	//                 email: {
	//                     required: "Email is required."
	//                 }
	//             },

	//             invalidHandler: function (event, validator) { //display error alert on form submit   

	//             },

	//             highlight: function (element) { // hightlight error inputs
	//                 $(element)
	//                     .closest('.form-group').addClass('has-error'); // set error class to the control group
	//             },

	//             success: function (label) {
	//                 label.closest('.form-group').removeClass('has-error');
	//                 label.remove();
	//             },

	//             errorPlacement: function (error, element) {
	//                 error.insertAfter(element.closest('.input-icon'));
	//             },

	//             submitHandler: function (form) {
	//                 form.submit();
	//             }
	//         });

	//         $('.forget-form input').keypress(function (e) {
	//             if (e.which == 13) {
	//                 if ($('.forget-form').validate().form()) {
	//                     $('.forget-form').submit();
	//                 }
	//                 return false;
	//             }
	//         });

	//         jQuery('#forget-password').click(function () {
	//             jQuery('.login-form').hide();
	//             jQuery('.forget-form').show();
	//         });

	//         jQuery('#back-btn').click(function () {
	//             jQuery('.login-form').show();
	//             jQuery('.forget-form').hide();
	//         });

	// }

	// var handleRegister = function () {

	// 	        function format(state) {
 //            if (!state.id) { return state.text; }
 //            var $state = $(
 //             '<span><img src="../assets/global/img/flags/' + state.element.value.toLowerCase() + '.png" class="img-flag" /> ' + state.text + '</span>'
 //            );
            
 //            return $state;
 //        }

 //        if (jQuery().select2 && $('#country_list').size() > 0) {
 //            $("#country_list").select2({
	//             placeholder: '<i class="fa fa-map-marker"></i>&nbsp;Select a Country',
	//             templateResult: format,
 //                templateSelection: format,
 //                width: 'auto', 
	//             escapeMarkup: function(m) {
	//                 return m;
	//             }
	//         });


	//         $('#country_list').change(function() {
	//             $('.register-form').validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
	//         });
 //    	}


 //         $('.register-form').validate({
	//             errorElement: 'span', //default input error message container
	//             errorClass: 'help-block', // default input error message class
	//             focusInvalid: false, // do not focus the last invalid input
	//             ignore: "",
	//             rules: {
	                
	//                 fullname: {
	//                     required: true
	//                 },
	//                 email: {
	//                     required: true,
	//                     email: true
	//                 },
	//                 address: {
	//                     required: true
	//                 },
	//                 city: {
	//                     required: true
	//                 },
	//                 country: {
	//                     required: true
	//                 },

	//                 username: {
	//                     required: true
	//                 },
	//                 password: {
	//                     required: true
	//                 },
	//                 rpassword: {
	//                     equalTo: "#register_password"
	//                 },

	//                 tnc: {
	//                     required: true
	//                 }
	//             },

	//             messages: { // custom messages for radio buttons and checkboxes
	//                 tnc: {
	//                     required: "Please accept TNC first."
	//                 }
	//             },

	//             invalidHandler: function (event, validator) { //display error alert on form submit   

	//             },

	//             highlight: function (element) { // hightlight error inputs
	//                 $(element)
	//                     .closest('.form-group').addClass('has-error'); // set error class to the control group
	//             },

	//             success: function (label) {
	//                 label.closest('.form-group').removeClass('has-error');
	//                 label.remove();
	//             },

	//             errorPlacement: function (error, element) {
	//                 if (element.attr("name") == "tnc") { // insert checkbox errors after the container                  
	//                     error.insertAfter($('#register_tnc_error'));
	//                 } else if (element.closest('.input-icon').size() === 1) {
	//                     error.insertAfter(element.closest('.input-icon'));
	//                 } else {
	//                 	error.insertAfter(element);
	//                 }
	//             },

	//             submitHandler: function (form) {
	//                 form.submit();
	//             }
	//         });

	// 		$('.register-form input').keypress(function (e) {
	//             if (e.which == 13) {
	//                 if ($('.register-form').validate().form()) {
	//                     $('.register-form').submit();
	//                 }
	//                 return false;
	//             }
	//         });

	//         jQuery('#register-btn').click(function () {
	//             jQuery('.login-form').hide();
	//             jQuery('.register-form').show();
	//         });

	//         jQuery('#register-back-btn').click(function () {
	//             jQuery('.login-form').show();
	//             jQuery('.register-form').hide();
	//         });
	// }
    
    return {
        //main function to initiate the module
        init: function () {
            handleLogin();
            login_in_event();	//登录按钮
            refresh_verify();	//绑定刷新验证码
            auto_login_username();	//自动填入上次登录的用户名
            turn_nine_login();	//绑定更改为9宫格方式登录
            //handleForgetPassword();
            // /handleRegister();
        }
    };
}();

jQuery(document).ready(function() {
    Login.init();
});