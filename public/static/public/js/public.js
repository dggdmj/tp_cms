var Public = function () {

    // 私有的函数和变量
    

    
    //示例方法的定义
    var myFunc = function(text) {
        alert(text);
    }

    //icheck全选操作
    var select_all_fun=function(){
        if($(".select_all")[0]){
            $('.select_all').on('ifChecked', function(event){
                $('.check_li').iCheck('uncheck');
            });
            $('.select_all').on('ifUnchecked', function(event){
                $('.check_li').iCheck('check');
            });
        }
    }
    //icheck全选操作2（相反）
    var select_all_fun2=function(){
        if($(".select_all2")[0]){
            $('.select_all2').on('ifChecked', function(event){
                $('.check_li').iCheck('check');
            });
            $('.select_all2').on('ifUnchecked', function(event){
                $('.check_li').iCheck('uncheck');
            });
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //公共方法 开始 ↓
    //改变类名，鼠标经过效果
    var change_class=function(jel){ //参数：jquery选择器对象
        $(jel).each(function(index, el) {
            $(this).unbind('mouseenter');
            $(this).mouseenter(function(event) {
                $(this).addClass("active");
            });
            $(this).unbind('mouseleave');
            $(this).mouseleave(function(event) {
                $(this).removeClass('active');
            });
        });
    }

    //loading的等待界面......
    var loading = function(txt){    //txt加载时显示的文字
        if(txt){
            App.blockUI({
                boxed: true,
                message: txt
            });
        }else{
            App.blockUI({
                boxed: true,
                message: '请稍等......'
            });
        }
    }

    //解除loading的等待界面......
    var unloading = function(){
        App.unblockUI();
    }

    //显示模态框提示
    var modal_alert = function(txt){
        $(".alert_modal").find('.modal-body').html(txt);
        $(".alert_modal").modal('show');    //弹模态框显示
        $('.btn_tixian_even').removeAttr("disabled");
    }

    //显示模态框提示，并带确认按钮
    var confirm_modal = function(txt){
        $(".confirm_modal").find('.modal-body').html(txt);
        $(".confirm_modal").modal('show');    //弹模态框显示
    }

    //标记输入框为错误
    var input_error = function(dom){   //参数：dom或者可以直接被jquery当做选择器的字符
        if($(dom)[0]){
            $(dom).parents(".form-group:first").addClass('has-error');     //显示为红框
            $(dom).next('span.form-control-feedback').show();  //显示叉叉小图标
            $(dom).next('span.glyphicon-ok').hide();  //隐藏小勾
            $(dom).parents(".form-group:first").find("span.help-block").show();   //显示错误提示
        }
    }
    //标记输入框错误取消
    var input_error_off = function(dom){   //参数：dom或者可以直接被jquery当做选择器的字符
        if($(dom)[0]){
            $(dom).parents(".form-group:first").removeClass('has-error');
            $(dom).next('span.form-control-feedback').hide();
            $(dom).parents(".form-group:first").find("span.help-block").hide();
        }
    }

    //气泡提示框
    var alert_slide = function(jel) {
        if($(jel)[0]){
            $(jel).popover({html : true });

            $(jel).each(function(index, el) {
                $(this).click(function(event) {
                    $(jel).not($(this)).popover('hide');
                });
            });
        }
    }

    //消失的提示框
    var fade_alert = function(txt,style) {
        var style=style || 0;
        $(".alert_bar"+style).find("p").html(txt);

        for (var i = 0; i < 10; i++) {
            $(".alert_bar"+i).stop();
            $(".alert_bar"+i).css("display","none");
        };
        $(".alert_bar"+style).show(function(){
            setTimeout(function(){$(".alert_bar"+style).hide(1000)},3000);
        });
    }

    //背景音乐播放器控制
    var bg_music = function() {
        if($("#audio_btn")[0]){
            var music_switch=true;  //控制音乐的开关
            $("#audio_btn").click(function(event) {
                if(music_switch){
                    music_switch=false;
                    $(this).removeClass('play_yinfu');
                    $(this).addClass('off');
                    $("#yinfu").removeClass('rotate');
                    $("#media")[0].pause();
                }else{
                    music_switch=true;
                    $(this).addClass('play_yinfu');
                    $(this).removeClass('off');
                    $("#yinfu").addClass('rotate');
                    $("#media")[0].play();
                }
            });
        }
    }

    //图片位移效果
    var img_move=function(jel){ //参数（jquery父容器选择器）
        $(jel).find("a").each(function(index, el) {
            $(this).mouseenter(function(event) {
                $(this).find("img").stop();
                $(this).find("img").animate({
                        "margin-left": "-10%"
                    },
                    300, function() {
                    /* stuff to do after animation is complete */
                });
            });

            $(this).mouseleave(function(event) {
                $(this).find("img").animate({
                        "margin-left": 0
                    },
                    300, function() {
                    /* stuff to do after animation is complete */
                });
            });
        });
    }

    //容器固定不动
    var fixed_box=function(jel){    //参数（jquery选择器）
        //alert($(jel)[0].offsetTop);
    }

    //跳转锚点
    var jump_mao=function(jel){ //参数（点击触发对象）
        $(jel).each(function(index, el) {
            $(this).unbind('click');
            $(this).click(function(event) {
                jump($(this).attr("data-id"));
            });
        });
    }
    //平滑的跳到锚点处
    var jump=function(id,custom_height){  //参数（锚点ID，自定义跳转高度）
        if(custom_height){
            $("html,body").animate({scrollTop: $("#"+id).offset().top+custom_height}, 300);
        }else{
            $("html,body").animate({scrollTop: $("#"+id).offset().top}, 300);
        }
    }

    //从右滑出的提示框（成功提示）
    var right_slide_notice=function(txt,style){   //参数（提示的信息，颜色风格）
        var txt=txt || '提交成功！';
        var color_style='teal';
        if(style){
            switch(style){
                case 1:
                    color_style='teal';
                    break;
                case 2:
                    color_style='ruby';
                    break;
                case 3:
                    color_style='tangerine';
                    break;
                case 4:
                    color_style='lime';
                    break;
            }
        }
        var settings = {
            life: 3000,
            theme: color_style,
            sticky: false,
            verticalEdge: 'right',
            horizontalEdge: 'top',
            zindex: 1100,
            heading: txt
        };
        $.notific8('zindex', 11500);
        $.notific8("", settings);
    }
    //从右滑出的提示框（失败的提示）
    var right_slide_notice_false=function(txt){   //参数（提示的信息）
        var txt=txt || '提交失败！';
        var settings = {
            life: 5000,
            theme: 'ruby',
            sticky: false,
            verticalEdge: 'right',
            horizontalEdge: 'top',
            zindex: 1100,
            heading: txt
        };
        $.notific8('zindex', 11500);
        $.notific8("详情请看顶部信息", settings);
    }
    //从右下侧滑出的提示框（提示）
    var right_bottom_slide_notice=function(txt){   //参数（提示的信息）
        var txt=txt || '提示！';
        var settings = {
            life: 10000,
            theme: 'tangerine',
            sticky: false,
            verticalEdge: 'right',
            horizontalEdge: 'bottom',
            zindex: 1100,
            heading: txt
        };
        $.notific8('zindex', 11500);
        $.notific8("", settings);
    }
    //从右上侧滑出的提示框（提示，不会自动消失）
    var right_top_slide_notice=function(txt,title){   //参数（提示的信息，提示信息的标题）
        var txt=txt || '提示！';
        var title=title || '提示！';
        var settings = {
            life: 10000,
            theme: 'lime',
            sticky: true,
            verticalEdge: 'right',
            horizontalEdge: 'top',
            zindex: 1100,
            heading: title
        };
        $.notific8('zindex', 11500);
        $.notific8(txt, settings);
    }

    //显示顶部提示信息
    var show_note_top=function(txt,style,clear){    //参数（需要显示的文字信息，提示框风格，是否清空之前的信息）
        var style_color="alert-danger";
        $(".note_top_event").show();
        if(clear){}else{
            $(".note_top_event").html('');
        }

        if(style){
            switch(style){
                case 1:
                    style_color="alert-danger";
                    break;
                case 2:
                    style_color="alert-success";
                    break;
                case 3:
                    style_color="alert-info";
                    break;
                case 4:
                    style_color="alert-warning";
                    break;
            }
        }        

        var note_html='<div class="alert alert-block '+style_color+' fade in"><button type="button" class="close" data-dismiss="alert"></button><h4 class="alert-heading">提示</h4><p> '+txt+' </p></div>';
        $(".note_top_event").append(note_html);
    }

    var isArray=function(o){    //参数（判断是否为数组）
        return Object.prototype.toString.call(o)=='[object Array]';
    }
    //公共方法 结束 ↑
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    // 对外函数
    return {

        //主函数
        init: function () {
            //初始化所有函数
            select_all_fun();   //icheck全选操作
            select_all_fun2();   //icheck全选操作2（相反）
        },

        //改变类名，鼠标经过效果
        Change_class:function(jel){
            change_class(jel);
        },

        //loading的等待遮罩
        Loading: function (txt) {
            loading(txt);
        },
        //解除loading状态
        Unloading: function () {
            unloading();
        },

        //模态框提示
        Modal_alert: function (txt) {
            modal_alert(txt);
        },
        
        //带按钮的模态框
        Confirm_modal: function (txt) {
            confirm_modal(txt);
        },

        //消失的提示框
        Fade_alert: function (txt,style) {
            fade_alert(txt,style);
        },

        //背景音乐播放器控制
        Bg_music: function () {
            bg_music();
        },

        //图片位移效果
        Img_move:function(jel){
            img_move(jel);
        },

        //容器固定不动
        Fixed_box:function(jel){
            fixed_box(jel);
        },

        //标记输入框错误
        Input_error:function(jel){
            input_error(jel);
        },
        //解除标记输入框错误
        Input_error_off:function(jel){
            input_error_off(jel);
        },

        //跳转到指定锚点
        JumpMao: function (jel) {   //参数（触发跳转的对象）
            jump_mao(jel);
        },
        //跳转到指定锚点
        JumpM: function (id,custom_height) {   //参数（触发跳转的对象，自定义跳转的高度）
            jump(id,custom_height);
        },

        //从右滑出的提示框（成功的提示）
        RightNotice: function(txt,style){ //参数（提示的信息，颜色风格）
            right_slide_notice(txt,style);
        },
        //从右滑出的提示框（失败的提示）
        RightNoticeFalse: function(txt){   //参数（提示的信息）
            right_slide_notice_false(txt);
        },
        //从右下侧滑出的提示框（提示）
        RightBottomNotice: function(txt){ //参数（提示的信息）
            right_bottom_slide_notice(txt);
        },
        //从右上侧滑出的提示框（提示）
        RightTopNotice: function(txt,title){ //参数（提示的信息，提示信息的标题）
            right_top_slide_notice(txt,title);
        },

        //显示顶部提示信息
        ShowNoteTop: function(txt,style,clear){    //参数（需要显示的文字信息，提示框风格，是否清空之前的信息）
            show_note_top(txt,style,clear);
        },

        //一些帮助的方法
        doSomeStuff: function () {
            myFunc();
        }

    };

}();

jQuery(document).ready(function() {    
   Public.init(); 
});

/***
使用方法
***/
//Public.init();
//Public.doSomeStuff();