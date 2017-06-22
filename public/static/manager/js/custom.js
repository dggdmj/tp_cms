/**
Custom module for you to write your own javascript functions
**/
var Custom = function () {

    // private functions & variables

    var myFunc = function(text) {
        alert(text);
    }

    //顶部按钮样式快速设定
    var quick_topMenu=function(){
        $('.topMenu').each(function(index, el) {
            $(this).find('a').addClass(btnStyle_fun(index));
            $(this).find('i').addClass(fa_fun(index));
        });
    }
    //左边菜单栏样式快速设定
    var quick_leftMenu=function(){
        $(".left_menu_icon").each(function(index, el) {
            $(this).addClass(icon_fun(index));
        });
    }

    //根据当前页的url的path，设置左边菜单栏的激活效果
    var set_left_menu_active=function(){
        $('[data-left-menu-url]').each(function(index, el) {
            var menu_url=$(this).attr('data-left-menu-url').toLowerCase();
            var request_path_str=request_path.toLowerCase();
            if(request_path_str.indexOf(menu_url)>-1){  //假如当前url有包含菜单的url，就显示激活效果
                set_leftMenu_active(this);
            }
        });
    }
    //设置左边菜单栏的激活效果
    var set_leftMenu_active=function(jel){  //参数（当前菜单的dom）
        $(".left_nav_item").removeClass('active');
        $(".left_nav_item").removeClass('open');
        $(".left_menu_arrow").removeClass('open');
        $(jel).addClass('active');
        $(jel).addClass('open');
        $(jel).find('a:first').find('.left_menu_arrow').addClass('open');
        $(jel).parents('.left_nav_item').each(function(index, el) {
            $(this).addClass('active');
            $(this).addClass('open');
            $(this).find('a:first').find('.left_menu_arrow').addClass('open');
        });
    }

    //公用后台表单异步提交方法（按钮触发形式）
    var form_ajax_submit=function(){
        $(".submit_btn").each(function(index, el) {
            $(this).click(function(event) {
                var jel_form=$(this).parents('form:first')[0];    //对应的表单对象
                var page_jump=$(jel_form).attr('data-jump');    //获取是否跳转
                var page_modal=$(jel_form).attr('data-modal');   //获取是否隐藏模态框
                var page_refresh=$(jel_form).attr('data-refresh'); //获取是否刷新当前页
                public_ajax_submit(jel_form,page_jump,page_modal,page_refresh);   //异步提交
            });
        });

        //带表名
        $(".submit_btn2").each(function(index, el) {
            $(this).click(function(event) {
                var jel_form=$(this).parents('form:first')[0];    //对应的表单对象
                public_ajax_submit2(jel_form);   //异步提交2
            });
        });
    }

    //公用后台模态框中表单异步提交方法
    var modal_ajax_submit=function(){
        $(".modal_submit_btn").each(function(index, el) {
            $(this).click(function(event) {
                var modal_obj=$(this).parents('.modal:first')[0];   //模态框dom对象
                var jel_form=$(modal_obj).find('form')[0];    //对应的表单对象
                var page_jump=$(jel_form).attr('data-jump');    //获取是否跳转
                var page_modal=$(jel_form).attr('data-modal');   //获取是否隐藏模态框
                var page_refresh=$(jel_form).attr('data-refresh'); //获取是否刷新当前页
                public_ajax_submit(jel_form,page_jump,page_modal,page_refresh);   //异步提交表单，参数（form对象，是否跳转，是否隐藏模态框，是否刷新当前页面）
            });
        });
    }

    //绑定开关事件的按钮
    var bind_toggle_btn=function(){
        $(".toggle_btn").each(function(index, el) {
            var toggle_btn_input=$(this).parents('.toggle_btn_event:first').find('.toggle_btn_input')[0];   //获取开关按钮控制的input对象
            var toggle_on_text=$(toggle_btn_input).attr("data-on-text");    //获取开关的开启文字
            var toggle_off_text=$(toggle_btn_input).attr("data-off-text");  //获取开关的关闭文字
            var toggle_val=$(toggle_btn_input).val();   //获取开关input的状态
            if(toggle_val!=0){ //假如开关当前为开启状态，就执行
                $(this).removeClass('blue');
                $(this).addClass('grey-mint');
                $(this).text(toggle_off_text);
            }else{      //否则为关闭状态
                $(this).removeClass('grey-mint');
                $(this).addClass('blue');
                $(this).text(toggle_on_text);
            }
            $(this).click(function(event) {
                var toggle_btn_input=$(this).parents('.toggle_btn_event:first').find('.toggle_btn_input')[0];   //获取开关按钮控制的input对象
                var toggle_on_text=$(toggle_btn_input).attr("data-on-text");    //获取开关的开启文字
                var toggle_off_text=$(toggle_btn_input).attr("data-off-text");  //获取开关的关闭文字
                var toggle_on_val=$(toggle_btn_input).attr("data-on-val");  //获取开关的开启value值
                var toggle_off_val=$(toggle_btn_input).attr("data-off-val");    //获取开关的关闭value值
                var toggle_val=$(toggle_btn_input).val();   //获取开关input的状态
                if(toggle_val!=0){ //假如开关当前为开启状态，就执行
                    $(this).removeClass('grey-mint');
                    $(this).addClass('blue');
                    $(this).text(toggle_on_text);
                    $(toggle_btn_input).val(toggle_off_val);
                    Public.RightNotice('已切换为：'+toggle_off_text,3);   //修改状态的显示
                }else{      //否则为关闭状态
                    $(this).removeClass('blue');
                    $(this).addClass('grey-mint');
                    $(this).text(toggle_off_text);
                    $(toggle_btn_input).val(toggle_on_val);
                    Public.RightNotice('已切换为：'+toggle_on_text,4);   //修改状态的显示
                }
            });
        });
    }

    //初始化数据表格
    var init_table = function () {
        if($(".data_table")[0]){
            $(".data_table").each(function(index, el) {
                var table = $(this);

                var oTable = table.dataTable({
                    // 初始化，更多信息请参考： http://datatables.net/manual/i18n
                    language: {
                        processing:     "处理中...",
                        search:         "搜索&nbsp;:",
                        lengthMenu:    "显示 _MENU_ 条记录",
                        info:           "显示第 _START_ 条到 _END_ 条   总共 _TOTAL_ 条记录",
                        infoEmpty:      "没有任何数据",
                        infoFiltered:   "(从 _MAX_ 条记录中搜索得出)",
                        infoPostFix:    "",
                        loadingRecords: "loading...",
                        zeroRecords:    "没有找到匹配的记录",
                        emptyTable:     "没有任何数据",
                        paginate: {
                            first:      "首页",
                            previous:   "上一页",
                            next:       "下一页",
                            last:       "最后一页"
                        },
                        aria: {
                            sortAscending:  ": 按升序排列",
                            sortDescending: ": 按降序排列"
                        }
                    },

                    // 或者你可以使用远程翻译文件
                    //"language": {
                    //   url: '//cdn.datatables.net/plug-ins/3cfcc339e89/i18n/Portuguese.json'
                    //},

                    // 设置按钮参考: http://datatables.net/extensions/buttons/
                    buttons: [
                        { extend: 'print', className: 'btn default' },
                        { extend: 'pdf', className: 'btn default' },
                        { extend: 'csv', className: 'btn default' }
                    ],

                    // 设置响应式参考: http://datatables.net/extensions/responsive/
                    responsive: {
                        details: {
                           
                        }
                    },

                    "bStateSave": false, // 保存数据表状态(分页、排序等)进cookie

                    "order": [
                        [1, 'desc'] //默认第一列倒序排序
                    ],
                    
                    "lengthMenu": [
                        [5, 10, 15, 20, -1],
                        [5, 10, 15, 20, "全部"] // 更改每页显示的值
                    ],
                    // 设置初始值
                    "pageLength": 15,
                    "pagingType": "bootstrap_full_number",
                    // "columnDefs": [{  // 设置默认列设置
                    //     'orderable': false, //禁用第一列排序
                    //     'targets': [0]
                    // }, {
                    //     "searchable": false,    //禁用第一列参与搜索
                    //     "targets": [0]
                    // }],
                    // 第一列设置为默认的排序，升序排序

                    "dom": "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>"// 水平方向滚动数据表

                    //取消注释下面的注释（“dom”参数）以修复数据单元格中的下拉溢出问题。 默认数据类型布局设置使用scrollable div（table-scrollable）和overflow：auto来启用垂直滚动（参见：assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js）。
                    //所以当下拉列表使用scrollable div应该删除。
                    //"dom": "<'row' <'col-md-12'T>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
                });
            });
        }
    }

    //修改用户数据的提交按钮
    var edit_user_btn_submit=function(){
        $(".edit_user_btn").click(function(event) {
            $('.edit_user_form').submit();
        });
    }
    //修改用户数据时的校验
    var check_edit_user = function() {
        if($('.edit_user_form')[0]){
            $('.edit_user_form').validate({
                rules: {
                    username: {
                        required: true
                    },
                    password: {
                        required: true
                    },
                    repassword: {
                        equalTo: ".modal_password"
                    }
                },

                messages: {
                    username: {
                        required: "用户名为必填项"
                    },
                    password: {
                        required: "密码为必填项"
                    },
                    repassword: {
                        equalTo: "两次输入的密码必须相同"
                    }
                },

                submitHandler: function (form) {
                    Public.Loading();   //loading
                    $.post(form.action, $(form).serialize(), function(data, textStatus, xhr) {
                        Public.Unloading();   //移除loading
                        if(data.status){
                            Public.RightNotice();   //提交成功的侧滑提示
                            $(".edit_modal").modal('hide'); //隐藏模态框

                            //更新表格中的数据
                            if($(".data_table").find('td[data-userId-list="'+data['info']['id']+'"]').text() == $(".username-hide-on-mobile").text()){    //假如修改的用户名等于右上角的名称
                                $(".username-hide-on-mobile").text(data['info']['username']);      //先更新右上角
                            }
                            $(".data_table").find('td[data-userId-list="'+data['info']['id']+'"]').text(data['info']['username']);  //更新用户名
                        }else{
                            Public.RightNoticeFalse();  //提交失败的侧滑提示
                            note_error_data(data);      //顶部显示出来出错的信息
                        }
                    });
                }
            });
        }
    }

    //修改用户组
    var edit_user_group=function(){
        var user_id=0;  //当前控制获取的用户id

        //触发修改用户组的模态框
        $(".edit_usergroup_modal").each(function(index, el) {
            $(this).click(function(event) {
                user_id=$(this).parents('tr:first').find('td:first').text();    //获取当前点击的用户id
                select_option_selected($(this).attr("data-groupId"));   //让下拉框中显示当前用户组选中状态
                $(".edit_group_modal").modal('show');
            });
        });
        //下拉框中显示当前用户组选中状态
        function select_option_selected(gid){
            $(".modal_user_group option").each(function(index, el) {    //首先删除所有下拉框中的选中状态
                $(this).removeAttr('selected');
            });
            $(".modal_user_group option[value='"+gid+"']").attr("selected","selected"); //再显示出当前点选的用户组
        }

        //修改用户组的提交按钮
        $(".edit_usergroup_btn").click(function(event) {
            Public.Loading();
            $.post('/manager/Index/edit_usergroup', {'user_id':user_id,'user_group_id':$(".modal_user_group").val()}, function(data, textStatus, xhr) {
                Public.Unloading();
                //$(".edit_group_modal").modal('hide');   //隐藏模态框
                if(data.status){
                    $("a.edit_usergroup_modal[data-userId='"+user_id+"']").attr("data-groupId",$(".modal_user_group").val());   //更新当前dom组id为修改后的id
                    $("a.edit_usergroup_modal[data-userId='"+user_id+"']").text($(".modal_user_group option[value='"+$(".modal_user_group").val()+"']").text()); //更新当前dom组的名称
                    Public.RightNotice('用户组修改成功');
                }else{
                    Public.RightNoticeFalse('用户组修改失败');
                    note_error_data(data);      //顶部显示出来出错的信息
                }
            });
        });
    }

    //初始化bootstrap的select样式
    // var init_bootstrap_select=function(){
    //     if($('.bs-select')[0]){
    //         $('.bs-select').selectpicker({
    //             iconBase: 'fa',
    //             tickIcon: 'fa-check'
    //         });
    //     }
    // }

    //编辑用户资料
    var edit_user_info=function(){
        $(".edit_userinfo_btn").each(function(index, el) {
            $(this).click(function(event) {
                $(".user_id_input").val($(this).parents('tr:first').find('td:first').text());   //当前控制获取的用户id赋给隐藏输入框中
                $(".modal_username").val($(this).parents("tr:first").find('td:eq(1)').text());  //获取表格中的用户名，填入到模态框中
                $(".modal_password").val('');
                $(".modal_repassword").val('');
                $(".nine_password").val('');
            });
        });
    }

    //新增用户的验证
    var add_user_check=function(){
        if($('.add_user_form')[0]){
            $('.add_user_form').validate({
                rules: {
                    username: {
                        required: true
                    },
                    password: {
                        required: true
                    },
                    repassword: {
                        equalTo: ".add_user_password"
                    }
                },

                messages: {
                    username: {
                        required: "用户名为必填项"
                    },
                    password: {
                        required: "密码为必填项"
                    },
                    repassword: {
                        equalTo: "两次输入的密码必须相同"
                    }
                },

                submitHandler: function (form) {
                    public_ajax_submit(form,'back',0,0);
                }
            });
        }
    }

    //绑定数据清理
    var bind_data_trash=function(){
        //清理缓存
        $(".clear_cache_btn").click(function(event) {
            data_trash_ajax($(this)[0]);
        });
        //清理session
        $(".clear_session_btn").click(function(event) {
            data_trash_ajax($(this)[0]);
        });
    }

    //数据清理异步方法
    var data_trash_ajax=function(jel){  //参数（对应触发的按钮）
        Public.Loading();
        var form=$(jel).parents('form:first')[0];  //对应触发区块的form
        $.post('/manager/Trash/trash_run', {data_type: $(form).attr('data-type'),datas: $(form).serializeArray()}, function(data, textStatus, xhr) {
            Public.Unloading();
            if(data.status){
                Public.RightNotice('清理数据成功');
                if(data['info'] && data['info_show']){
                    if(isArray(data['info'])){  //假如有多条出错信息，就逐条显示
                        for (var i = 0; i < data['info'].length; i++) {
                            Public.ShowNoteTop(data['info'][i],1,true);   //顶部显示出来出错的信息
                        };
                    }else{  //单条出错信息
                        Public.ShowNoteTop(data['info'],1,true);   //顶部显示出来出错的信息
                    }
                }else{
                    //Public.ShowNoteTop(data,1,true);   //顶部显示出来出错的调试信息
                }
            }else{
                Public.RightNoticeFalse('清理失败');
                if(data['info']){
                    if(isArray(data['info'])){  //假如有多条出错信息，就逐条显示
                        for (var i = 0; i < data['info'].length; i++) {
                            Public.ShowNoteTop(data['info'][i],1,true);   //顶部显示出来出错的信息
                        };
                    }else{  //单条出错信息
                        Public.ShowNoteTop(data['info'],1,true);   //顶部显示出来出错的信息
                    }
                }else{
                    Public.ShowNoteTop(data,1,true);   //顶部显示出来出错的调试信息
                }
            }
        });
    }

    //左边栏菜单的当前页面激活效果显示
    // var now_page_menu=function(nav_li,li_one,li_two){  //参数（栏目菜单对象，一级子菜单对象，二级子菜单对象）
    //     //先删除初始化显示的菜单样式
    //     // $(".first_left_menu").removeClass('start');
    //     // $(".first_left_menu").removeClass('active');
    //     // $(".first_left_menu").removeClass('open');
    //     // $(".first_left_menu").find('a:first').find('.arrow').removeClass('open');
    //     // $(".first_left_menu").find('ul li').removeClass('start');
    //     // $(".first_left_menu").find('ul li').removeClass('active');
    //     // $(".first_left_menu").find('ul li').removeClass('open');
        
    //     //显示当前页面的菜单激活样式
    //     // $(nav_li).addClass('start');
    //     // $(nav_li).addClass('active');
    //     // $(nav_li).addClass('open');
    //     // $(nav_li).find('a:first').find('.arrow').addClass('open');
    //     // $(nav_li).find('ul li').addClass('start');
    //     $(li_one).addClass('active');
    //     $(li_one).addClass('open');
    // }

    //公用：异步开关插件
    var change_switch_fun=function(){
        $(".switch_change").each(function(index, el) {
            $(this).on('switchChange.bootstrapSwitch', function(event, state) {
                var rid=$(this).parents('tr:first').attr('data-row-id');    //获取行id
                if(!rid){   //如果没有获取到行id，就获取属性形式的id
                    rid=$(this).attr('data-sid');
                }
                var action_url=$(this).attr('data-action-url');     //提交的url
                if(!action_url){   //如果没有获取到提交的url，就获取form形式的url
                    action_url=$(this).parents('form:first').attr('action');
                }
                var table_name=$(this).attr('data-table');  //表名
                var field_name=$(this).attr('data-field');  //字段名
                var s_on=$(this).attr('data-on');   //开启的标识值
                var s_off=$(this).attr('data-off');   //关闭的标识值
                var pre_table=$(this).attr('data-pre-table');   //带表前缀的表名

                var obj=this;   //当前点击对象
                var jump=$(obj).attr('data-jump');     //页面是否跳转
                var modal_obj=$(obj).attr('data-modal');   //页面模态框是否关闭
                var refresh=$(obj).attr('data-refresh');   //页面是否重载

                Public.Loading();
                $.post(action_url, {'id':rid,'table_name':table_name,'field_name':field_name,'status':state,'on':s_on,'off':s_off,'pre_table':pre_table}, function(data, textStatus, xhr) {   //参数（id，表名，字段名，状态，自定义状态开，自定义状态关，省去表前缀的表名(优先执行)）
                    Public.Unloading();
                    if(data.status){
                        Public.RightNotice('修改成功');

                        if(data['info']){
                            Public.ShowNoteTop(data['info'],2);   //顶部显示出来信息
                        }
                        if(jump=='back'){   //假如为返回上一页
                            Public.Loading('页面返回中');
                            window.history.back();
                        }else if(jump!=0 && jump!='undefined'){   //是否跳转页面的判断
                            Public.Loading('页面跳转中');
                            location.href=data['url'];  //提交成功后的跳转
                        }
                        if(modal_obj!=0 && modal_obj!='undefined'){  //是否隐藏模态框
                            $(modal_obj).modal('hide');
                        }
                        if(refresh!=0 && refresh!='undefined'){    //刷新当前页
                            location.reload(true);
                        }
                    }else{
                        Public.RightNoticeFalse('修改失败');
                        note_error_data(data);      //顶部显示出来出错的信息
                    }
                });
            });
        });
    }

    //公共：异步提交方法
    var public_ajax_submit=function(form,jump,modal_obj,refresh){  //参数（form对象，是否跳转，是否隐藏模态框，是否刷新当前页面）
        Public.Loading();   //loading
        $.post(form.action, $(form).serialize(), function(data, textStatus, xhr) {
            Public.Unloading();   //移除loading
            if(data.status){
                Public.RightNotice();   //提交成功的侧滑提示
                if(data['info']){
                    Public.ShowNoteTop(data['info'],2);   //顶部显示出来信息
                }
                if(jump=='back'){   //假如为返回上一页
                    Public.Loading('页面返回中');
                    window.history.back();
                }else if(jump!=0){   //是否跳转页面的判断
                    Public.Loading('页面跳转中');
                    location.href=data['url'];  //提交成功后的跳转
                }
                if(modal_obj!=0){  //是否隐藏模态框
                    $(modal_obj).modal('hide');
                }
                if(refresh!=0){    //刷新当前页
                    location.reload(true);
                }
            }else{
                Public.RightNoticeFalse();  //提交失败的侧滑提示
                note_error_data(data);      //顶部显示出来出错的信息
            }
        });
    }

    //公共：异步提交方法2(带表名)
    var public_ajax_submit2=function(jel){  //参数（触发的对象）
        var obj=jel;   //当前触发对象
        var ajax_url=$(obj).attr('action');     //页面处理的地址
        var jump=$(obj).attr('data-jump');     //页面是否跳转
        var modal_on=$(obj).attr('data-modal-on');     //是否启用模态框
        var modal_txt=$(obj).attr('data-modal-txt');     //模态框中要显示的内容
        var modal_obj=$(obj).attr('data-modal');   //页面模态框是否关闭
        var refresh=$(obj).attr('data-refresh');   //页面是否重载
        var ajax_data=$(obj).attr('data-ajax-json');    //页面是否要传递json数据
        var modal_keyborad=$(obj).attr('data-modal-keyborad');    //模态框是否绑定回车键提交

        Public.Loading();   //loading
        $.post(ajax_url, {'field_data':$(obj).serializeArray(),'table_name':$(obj).attr('data-table-name'),'pre_table':$(obj).attr('data-pretable')}, function(data, textStatus, xhr) {
            Public.Unloading();   //移除loading
            if(data.status){
                Public.RightNotice();   //提交成功的侧滑提示
                if(data['info']){
                    Public.ShowNoteTop(data['info'],2);   //顶部显示出来信息
                }
                if(jump=='back'){   //假如为返回上一页
                    Public.Loading('页面返回中');
                    window.history.back();
                }else if(jump!=0){   //是否跳转页面的判断
                    Public.Loading('页面跳转中');
                    location.href=data['url'];  //提交成功后的跳转
                }
                if(modal_obj!=0){  //是否隐藏模态框
                    $(modal_obj).modal('hide');
                }
                if(refresh!=0){    //刷新当前页
                    location.reload(true);
                }
            }else{
                Public.RightNoticeFalse();  //提交失败的侧滑提示
                note_error_data(data);      //顶部显示出来出错的信息
            }
        });
    }

    //公共：默认绑定日期插件
    var bind_date_picker=function(){
        $(".date_picker").each(function(index, el) {
            $(this).datepicker({
                rtl: App.isRTL(),
                orientation: "left",
                autoclose: true,
                format: 'yyyy-mm-dd',
                language: 'zh-CN',
                todayHighlight: true
            });
        });
    }
    //公共：默认绑定日期时间插件
    // var bind_datetime_picker=function(){
    //     $(".datetime_picker").each(function(index, el) {
    //         $(this).datetimepicker({
    //             autoclose: true,
    //             isRTL: App.isRTL(),
    //             format: 'yyyy-mm-dd',
    //             minView:'month',
    //             weekStart:0,
    //             pickerPosition: (App.isRTL() ? "bottom-left" : "bottom-right"),
    //             language: 'zh-CN'
    //         }).on('hide',function(ev){
    //             var d = new Date(ev.date.valueOf());
    //             $(this).val(formatDate(d));
    //         });
    //     });
    // }
    //公共：绑定日期时间插件(不指定3点钟)
    var bind_datetime_picker2=function(){
        $(".datetime_picker2").each(function(index, el) {
            $(this).datetimepicker({
                autoclose: true,
                isRTL: App.isRTL(),
                format: "yyyy-mm-dd hh:ii:ss",
                pickerPosition: (App.isRTL() ? "bottom-right" : "bottom-left"),
                language: 'zh-CN',
                todayBtn: true,
                todayHighlight: true
            });
        });
    }

    // 公共：格式化时间戳，获取明天3点的时间格式
    // var formatDate = function(now) {
    //     var year=now.getFullYear();
    //     var month=now.getMonth()+1;
    //     if (month < 10) {
    //         month = '0'+month;
    //     }
    //     var date=now.getDate();
    //     if (date < 10) {
    //         date = '0'+date;
    //     }
    //     var hour=now.getHours();
    //     var minute=now.getMinutes();
    //     var second=now.getSeconds();
    //     return year+"-"+month+"-"+date+" 03:00:00";
    // }

    //关闭后台左侧菜单栏
    // var close_sidemenu=function(){
    //     $('body').addClass('page-sidebar-closed');
    //     $('.page-sidebar-menu').addClass('page-sidebar-menu-closed');
    // }

    //公共：异步删除表格中的一行数据
    var ajax_del_row=function(){
        $(".ajax_del").each(function(index, el) {
            $(this).click(function(event) {
                var obj=this;   //当前点击对象
                var tr=$(obj).parents("tr[data-row-id]:first")[0];  //带有id的父级tr对象
                var form=$(obj).parents('form:first')[0];   //父级表单对象
                var jump=$(form).attr('data-jump');     //页面是否跳转
                var modal_obj=$(form).attr('data-modal');   //页面模态框是否关闭
                var refresh=$(form).attr('data-refresh');   //页面是否重载

                Public.Confirm_modal('确定要删除吗？');
                $(".confirm_modal .modal_ok").unbind('click');
                $(".confirm_modal .modal_ok").click(function(event) {
                    Public.Loading();   //loading
                    $.post($(form).attr("action"), {'id':$(tr).attr('data-row-id'),'table_name':$(form).attr('data-del-table'),'pre_table':$(form).attr('data-del-pretable')}, function(data, textStatus, xhr) {
                        Public.Unloading();   //移除loading+

                        if(data.status){
                            Public.RightNotice();   //提交成功的侧滑提示
                            $(tr).remove();  //删除该行
                            
                            if(data['info']){
                                Public.ShowNoteTop(data['info'],2);   //顶部显示出来信息
                            }
                            if(jump=='back'){   //假如为返回上一页
                                Public.Loading('页面返回中');
                                window.history.back();
                            }else if(jump!=0){   //是否跳转页面的判断
                                Public.Loading('页面跳转中');
                                location.href=data['url'];  //提交成功后的跳转
                            }
                            if(modal_obj!=0){  //是否隐藏模态框
                                $(modal_obj).modal('hide');
                            }
                            if(refresh!=0){    //刷新当前页
                                location.reload(true);
                            }
                        }else{
                            Public.RightNoticeFalse();  //提交失败的侧滑提示
                            note_error_data(data);      //顶部显示出来出错的信息
                        }
                        $(document).unbind('keyup');    //解除绑定回车提交事件
                    });
                });

                //绑定取消按钮，取消回车提交事件
                $(".confirm_modal .modal_close").unbind('click');
                $(".confirm_modal .modal_close").click(function(event) {
                    $(document).unbind('keyup');    //解除绑定回车提交事件
                });

                //绑定回车提交事件
                $(document).unbind('keyup');    //解除绑定回车提交事件
                $(document).keyup(function(event) {
                    if (event.keyCode == 13){
                        //alert('回车键.');
                        $(".confirm_modal .modal_ok").click();
                    }else{
                        //alert('你按错了键.');
                    }
                });
            });
        });
    }

    //公共：异步批量删除表格中的数据
    var ajax_del_rows=function(){
        $(".ajax_dels").each(function(index, el) {
            $(this).click(function(event) {
                var obj=this;   //当前点击对象
                var form=$(obj).parents('form:first')[0];   //父级表单对象
                var jump=$(form).attr('data-jump');     //页面是否跳转
                var modal_obj=$(form).attr('data-modal');   //页面模态框是否关闭
                var refresh=$(form).attr('data-refresh');   //页面是否重载

                var id_arr=new Array(); //id集合
                $(".check_li:checked").each(function(index, el) {   //遍历勾选了的id，压入数组，以便批量删除
                    var id=$(this).parents("tr[data-row-id]:first").attr('data-row-id');    //获取标记在行上的id
                    id_arr.push(id);    //压入数组
                });

                if(id_arr){
                    Public.Confirm_modal('确定要<b style="color:#f00">批量</b>删除吗？');
                    $(".confirm_modal .modal_ok").unbind('click');
                    $(".confirm_modal .modal_ok").click(function(event) {
                        Public.Loading();   //loading
                        $.post($(form).attr("action"), {'id_arr':id_arr,'table_name':$(form).attr('data-del-table'),'pre_table':$(form).attr('data-del-pretable')}, function(data, textStatus, xhr) {
                            Public.Unloading();   //移除loading
                            if(data.status){
                                Public.RightNotice();   //提交成功的侧滑提示
                                $(".check_li:checked").each(function(index, el) {   //批量删除对应行
                                    $(this).parents("tr[data-row-id]:first").remove();
                                });

                                if(data['info']){
                                    Public.ShowNoteTop(data['info'],2);   //顶部显示出来信息
                                }
                                if(jump=='back'){   //假如为返回上一页
                                    Public.Loading('页面返回中');
                                    window.history.back();
                                }else if(jump!=0){   //是否跳转页面的判断
                                    Public.Loading('页面跳转中');
                                    location.href=data['url'];  //提交成功后的跳转
                                }
                                if(modal_obj!=0){  //是否隐藏模态框
                                    $(modal_obj).modal('hide');
                                }
                                if(refresh!=0){    //刷新当前页
                                    location.reload(true);
                                }
                            }else{
                                Public.RightNoticeFalse();  //提交失败的侧滑提示
                                note_error_data(data);      //顶部显示出来出错的信息
                            }
                        });
                    });
                }

            });
        });
    }

    //公共：异步方法
    var ajax_bind=function(){
        $(".ajax_fun").each(function(index, el) {
            $(this).click(function(event) {
                var obj=this;   //当前点击对象
                var ajax_url=$(obj).attr('data-ajax-url');     //页面处理的地址
                var jump=$(obj).attr('data-jump');     //页面是否跳转
                var modal_on=$(obj).attr('data-modal-on');     //是否启用模态框
                var modal_txt=$(obj).attr('data-modal-txt');     //模态框中要显示的内容
                var modal_obj=$(obj).attr('data-modal');   //页面模态框是否关闭
                var refresh=$(obj).attr('data-refresh');   //页面是否重载
                var ajax_data=$(obj).attr('data-ajax-json');    //页面是否要传递json数据
                var modal_keyborad=$(obj).attr('data-modal-keyborad');    //模态框是否绑定回车键提交

                if(modal_on!='undefined' && modal_on){  //假如要启用模态框
                    Public.Confirm_modal(modal_txt);
                    $(".confirm_modal .modal_ok").unbind('click');
                    $(".confirm_modal .modal_ok").click(function(event) {
                        do_ajax();
                    });

                    if(modal_keyborad!='undefined' && modal_keyborad){  //假如要启用回车键提交
                        //绑定取消按钮，取消回车提交事件
                        $(".confirm_modal .modal_close").unbind('click');
                        $(".confirm_modal .modal_close").click(function(event) {
                            $(document).unbind('keyup');    //解除绑定回车提交事件
                        });
                        //绑定回车提交事件
                        $(document).unbind('keyup');    //解除绑定回车提交事件
                        $(document).keyup(function(event) {
                            if (event.keyCode == 13){
                                //alert('回车键.');
                                $(".confirm_modal .modal_ok").click();
                            }else{
                                //alert('你按错了键.');
                            }
                        });
                    }
                }else{
                    do_ajax();
                }
                
                //处理异步事件
                function do_ajax(){
                    Public.Loading();   //loading
                    var turn_ajax_obj;
                    if(ajax_data!='undefined' && ajax_data){
                        turn_ajax_obj=eval('(' + ajax_data + ')');
                    }

                    $.post(ajax_url, {'data':turn_ajax_obj}, function(data, textStatus, xhr) {
                        Public.Unloading();   //移除loading

                        if(data.status){
                            Public.RightNotice();   //提交成功的侧滑提示
                            $(tr).remove();  //删除该行
                            
                            if(data['info']){
                                Public.ShowNoteTop(data['info'],2);   //顶部显示出来信息
                            }
                            if(jump=='back'){   //假如为返回上一页
                                Public.Loading('页面返回中');
                                window.history.back();
                            }else if(jump!=0){   //是否跳转页面的判断
                                Public.Loading('页面跳转中');
                                location.href=data['url'];  //提交成功后的跳转
                            }
                            if(modal_obj!=0){  //是否隐藏模态框
                                $(modal_obj).modal('hide');
                            }
                            if(refresh!=0){    //刷新当前页
                                location.reload(true);
                            }
                        }else{
                            Public.RightNoticeFalse();  //提交失败的侧滑提示
                            note_error_data(data);      //顶部显示出来出错的信息
                        }
                        $(document).unbind('keyup');    //解除绑定回车提交事件
                    });
                }
            });
        });
    }

    //公共：默认绑定百度编辑器
    var public_bind_ueditor=function(){
        if (typeof(ue_obj) != "undefined") {    //首先判断下遍历是否存在
            ue_obj=new Array(); //重新实例化ue对象数组
            for (var i = 0; i < 30; i++) {
                if($('#bind_ue'+i)[0]){
                    ue_obj[i] = UE.getEditor('bind_ue'+i,{
                        autoHeightEnabled: false,   // {Boolean} [默认值：true] //是否自动长高，默认true
                        scaleEnabled: true //{Boolean} [默认值：false] //是否可以拉伸长高，默认true(当开启时，自动长高失效)
                    });
                }
            };
        }
    }

    //上传的ajax事件
    // var bind_upload_ajax = function(obj,upload_url,prog,box) {   //参数（上传图片的input容器，服务端的控制器地址，进度条容器选择器，上传成功的路径存放容器）
    //         $(obj).fileupload({      //input上传事件
    //             url: upload_url,       // 服务器上传的控制器地址
    //             dataType: 'json',
    //             done: function (e,data) {      //上传完成时的操作
    //                 //console.log(data.result);
    //                 if(data.result.status){
    //                     var file_url=data.result.data[0].savepath+data.result.data[0].savename;  //上传后的文件路径

    //                     $(box).val(file_url);
    //                 }else{
    //                     alert("上传失败！原因是："+data.result.info);
    //                 }
                    
    //                 //插件原有方法
    //                 // $.each(data.result.files, function (index, file) {
    //                 //     $('<p/>').text(file.name).appendTo('#files');
    //                 // });
    //             },
    //             progressall: function (e, data) {       //上传过程中的操作
    //                 var progress = parseInt(data.loaded / data.total * 100, 10);
    //                 $(prog).css(
    //                     'width',
    //                     progress + '%'
    //                 );
    //                 $(".progress_txt").text("上传进度：" + progress + "%");
    //             }
    //         }).prop('disabled', !$.support.fileInput).parent().addClass($.support.fileInput ? undefined : 'disabled');
    // }

    // //绑定上传事件
    // var bind_upload_event = function(){   //参数（上传图片的按钮，上传图片的input容器id选择器）
    //     $(".upload_btn").each(function(index, el) {     //上传按钮
    //         var upload_box=$(this).parents('.upload_event:first')[0];   //上传的盒子容器
    //         var progress_bar=$(upload_box).find('.progress-bar')[0];    //上传进度条
    //         var progress_txt=$(upload_box).find('.progress_txt-bar')[0];    //上传进度条百分比
    //         var upload_input=$(upload_box).find('.upload_input')[0];    //记录上传地址的输入框
    //         var upload_url=$(this).attr('data-upload-url'); //上传的地址
    //         var show_upload_box=$(upload_box).find('.show_upload_img')[0]; //显示图片的容器

    //         bind_upload_ajax(upload_input,upload_url,progress_bar,show_upload_box);     //绑定ajax上传

    //         //绑定点击事件
    //         $(this).unbind('click');
    //         $(this).click(function(event) {
    //             var upload_box=$(this).parents('.upload_event:first')[0];   //上传的盒子容器
    //             var progress_bar=$(upload_box).find('.progress-bar')[0];    //上传进度条
    //             var progress_txt=$(upload_box).find('.progress_txt-bar')[0];    //上传进度条百分比
    //             var upload_input=$(upload_box).find('.upload_input')[0];    //记录上传地址的输入框
    //             $(progress_bar).css("width",0);
    //             $(progress_txt).text("");



    //             $(upload_input).click();
    //         });
    //     });
    // }

    //绑定webuploader上传事件
    var bind_webuploader=function(){
        if(typeof(WebUploader) != 'undefined'){
            var uploader_arr=new Array();
            $(".fileList").each(function(index, el) {
            
                // 初始化Web Uploader
                uploader_arr[index] = WebUploader.create({

                    // 选完文件后，是否自动上传。
                    auto: true,

                    // swf文件路径
                    swf: '/static/assets/global/plugins/webuploader-0.1.5/Uploader.swf',

                    // 文件接收服务端。
                    server: '/manager/Publicfun/ajax_upload_file',

                    // 选择文件的按钮。可选。
                    // 内部根据当前运行是创建，可能是input元素，也可能是flash.
                    pick: $(".filePicker").eq(index)[0],

                    // 只允许选择图片文件。
                    // accept: {
                    //     title: 'Images',
                    //     extensions: 'gif,jpg,jpeg,bmp,png',
                    //     mimeTypes: 'image/*'
                    // }
                });

                // 当有文件添加进来的时候
                uploader_arr[index].on( 'fileQueued', function( file ) {
                    //console.log(file);
                    var img_li = $(
                            '<div id="' + file.id + '" class="file-item thumbnail lightbox_img">' +
                                '<a href="javascript:;">' +
                                    '<img>' +
                                '</a>' +
                                '<div class="info">' + file.name + '</div>' +
                            '</div>'
                            ),
                        img_list = img_li.find('img');

                    // $list为容器jQuery实例
                    $(el).append( img_li );

                    // 创建缩略图
                    // 如果为非图片文件，可以不用调用此方法。
                    // thumbnailWidth x thumbnailHeight 为 100 x 100
                    uploader_arr[index].makeThumb( file, function( error, src ) {
                        if ( error ) {
                            img_list.replaceWith('<span>不能预览</span>');
                            return;
                        }

                        img_list.attr( 'src', src );
                    }, 100, 100 );
                });

                // 文件上传过程中创建进度条实时显示。
                uploader_arr[index].on( 'uploadProgress', function( file, percentage ) {
                    var img_li = $( '#'+file.id ),
                        percent = img_li.find('.progress span');

                    // 避免重复创建
                    if ( !percent.length ) {
                        percent = $('<p class="progress"><span></span></p>')
                                .appendTo( img_li )
                                .find('span');
                    }

                    percent.css( 'width', percentage * 100 + '%' );
                });

                // 文件上传成功，给item添加成功class, 用样式标记上传成功。
                uploader_arr[index].on( 'uploadSuccess', function( file ,response) {
                    $( '#'+file.id ).addClass('upload-state-done');

                    //console.log(response);
                    //var json_result=eval('(' + response + ')');
                    if(response['status']){
                        //console.log(response['info']);

                        //记录到input中
                        var webuploader_parent=$(el).parents(".uploader-demo:first")[0];    //先找到上传容器的父节点
                        var webuploader_input=$(webuploader_parent).find('.webuploader_input')[0];  //找到上传容器对应的input
                        var webuploader_input_val=$(webuploader_input).val();   //获取原本记录的值
                        var new_webuploader_input_val=webuploader_input_val+","+response['info']['file_id'];   //上传成功后记录的图片id
                        new_webuploader_input_val=new_webuploader_input_val.trim(',');     //过滤两边多余的分隔符
                        $(webuploader_input).val(new_webuploader_input_val);   //赋值input完成，数据记录
                        //console.log(new_webuploader_input_val);
                        //console.log(file);

                        //添加删除按钮
                        $( '#'+file.id ).prepend('<i class="fa fa-times del_upload_file_style del_upload_file_btn" data-upload-id="'+response['info']['file_id']+'"></i>');
                        $( '#'+file.id ).find('.del_upload_file_btn').click(function(event) {
                            var event_obj=this; //当前点击对象
                            ajax_del_upload_fun(event_obj);     //删除上传文件
                        });

                        //绑定图片灯箱特效
                        $( '#'+file.id ).find('a').first().attr('href',response['info']['url']);
                        $( '#'+file.id ).find('a').first().attr('title',file.name);
                        $(".lightbox_img a").lightBox();
                    }else{
                        alert('服务端返回数据错误，有可能是thinkphp开启了trace导致');
                        console.log(response);
                    }
                });

                // 文件上传失败，显示上传出错。
                uploader_arr[index].on( 'uploadError', function( file ) {
                    var img_li = $( '#'+file.id ),
                        error = img_li.find('div.error');

                    // 避免重复创建
                    if ( !error.length ) {
                        error = $('<div class="error"></div>').appendTo( img_li );
                    }

                    error.text('上传失败');
                });

                // 完成上传完了，成功或者失败，先删除进度条。
                uploader_arr[index].on( 'uploadComplete', function( file ) {
                    $( '#'+file.id ).find('.progress').remove();
                });

            });
        }
    }

    //编辑页面有上传文件时，显示出来
    var edit_upload_file=function(){
        $(".webuploader_input").each(function(index, el) {
            if($(this).val() != ''){
                var upload_parent=$(this).parents('.uploader-demo:first')[0];   //获取上传的父容器
                $(upload_parent).find('.fileList').text('图片获取中...');

                $.post('/manager/PublicFun/get_edit_upload', {ids: $(this).val()}, function(data, textStatus, xhr) {
                    $(upload_parent).find('.fileList').text('');    //清除文字提示
                    if(data['status']){
                        for (var i = 0; i < data['info'].length; i++) {
                            var img_html='<div class="file-item thumbnail lightbox_img upload-state-done"><i class="fa fa-times del_upload_file_style del_upload_file_btn" data-upload-id="'+data['info'][i]['id']+'"></i><a href="'+data['info'][i]['url']+'" title="'+data['info'][i]['title']+'"><img src="'+data['info'][i]['url']+'"></a><div class="info">'+data['info'][i]['title']+'</div></div>';
                            $(upload_parent).find('.fileList').append(img_html);
                        };
                        $(".lightbox_img a").lightBox();    //绑定灯箱幻灯

                        //绑定删除
                        $(".del_upload_file_btn").each(function(index, el) {
                            $(this).click(function(event) {
                                var event_obj=this; //当前点击对象
                                ajax_del_upload_fun(event_obj);     //删除上传文件
                            });
                        });
                    }else{
                        console.log(data['info']);
                    }
                });
            }
        });
    }

    //删除上传文件
    var ajax_del_upload_fun=function(event_obj){  //参数（点击对象）
        Public.Confirm_modal('确定要删除吗？');
        $(".modal_ok").unbind('click');
        $(".modal_ok").click(function(event) {
            Public.Loading();
            $.post('/manager/PublicFun/ajax_del_upload_file', {id: $(event_obj).attr('data-upload-id')}, function(data, textStatus, xhr) {
                Public.Unloading();
                if(data['status']){
                    //删除对应input的val的id值
                    var find_input=$(event_obj).parents('.uploader-demo:first').find('input.webuploader_input')[0];
                    var id_arr=$(find_input).val().split(',');
                    //console.log(id_arr);
                    for (var i = 0; i < id_arr.length; i++) {
                        if(id_arr[i] == $(event_obj).attr('data-upload-id')){
                            //console.log(id_arr);
                            id_arr.splice(i,1); //删除对应id
                            //console.log(id_arr);
                            $(find_input).val(id_arr.join(","));   //赋值input完成，数据记录
                        }
                    };

                    $(event_obj).parents('.lightbox_img:first').remove();
                }else{
                    Public.Modal_alert('删除失败，原因是：'+data['info']);
                }
            });
        });
    }

    //后台获取在线人数
    // var get_inline=function(){
    //     setInterval(function(){ //定时异步获取在线人数
    //         $.post('/manager/inline', function(data, textStatus, xhr) {
    //             if(data.status){
    //                 $(".inline_count_num").text(data['info']);
    //             }else{
    //                 $(".inline_count_num").text(0);
    //                 console.log(data['error_info']);
    //             }
    //         });
    //     },inline_time);
    // }

    // //显示右下角的提示框
    // var show_right_bottom=function(str_html){   //参数（提示框中显示的内容，为html代码）
    //     var str_html=str_html||'';
    //     var rb_html='<div class="right_bottom_notice right_bottom_notice_event"><div class="rb_notice_title"><div class="rb_notice_title_font">提现提醒</div><div class="rb_notice_title_close rb_notice_close">关闭×</div></div><div class="rb_notice_box"><div class="rb_notice_content">'+str_html+'<div class="clearfix"></div></div></div></div>';
    //     $("body").append(rb_html);
    //     clearTimeout(right_bottom_notice_loopobj);
    //     right_bottom_notice_loopobj=setTimeout(function(){  //一定时间后自动关闭提示框
    //         $(".right_bottom_notice_event").remove();
    //     },10000);
    //     close_right_bottom();
    // }
    // //绑定右下角的关闭按钮事件
    // var close_right_bottom=function(){
    //     $(".rb_notice_close").each(function(index, el) {
    //         $(this).unbind('click');
    //         $(this).click(function(event) {
    //             var rb_obj=$(this).parents('.right_bottom_notice_event:first')[0];
    //             $(rb_obj).hide();
    //             $(rb_obj).remove();
    //         });
    //     });
    // }

    //小图标快捷调用
    var fa_fun=function(index){ //参数（图标索引）
        var fa_list=new Array();
        fa_list[0]='fa fa-file-text-o';
        fa_list[1]='fa fa-area-chart';
        fa_list[2]='fa fa-columns';
        fa_list[3]='fa fa-rocket';
        fa_list[4]='fa fa-star';
        fa_list[5]='fa fa-umbrella';
        fa_list[6]='fa fa-server';
        fa_list[7]='fa fa-map-pin';
        fa_list[8]='fa fa-glass';
        fa_list[9]='fa fa-bicycle';
        fa_list[10]='fa fa-bolt';
        fa_list[11]='fa fa-desktop';
        fa_list[12]='fa fa-fighter-jet';
        fa_list[13]='fa fa-hdd-o';
        fa_list[14]='fa fa-magic';
        fa_list[15]='fa fa-line-chart';
        fa_list[16]='fa fa-map';
        fa_list[17]='fa fa-pencil';
        fa_list[18]='fa fa-star-o';
        fa_list[19]='fa fa-paper-plane';
        return fa_list[index];
    }

    //按钮样式快捷调用
    var btnStyle_fun=function(index){ //参数（样式索引）
        var btn_style_list=new Array();
        btn_style_list[0]='btn btn-primary';
        btn_style_list[1]='btn purple';
        btn_style_list[2]='btn btn-success';
        btn_style_list[3]='btn btn-info';
        btn_style_list[4]='btn btn-warning';
        btn_style_list[5]='btn dark';
        btn_style_list[6]='btn default';
        btn_style_list[7]='btn btn-danger';
        btn_style_list[8]='btn blue btn-outline';
        btn_style_list[9]='btn green btn-outline';
        btn_style_list[10]='btn yellow btn-outline';
        btn_style_list[11]='btn purple btn-outline';
        btn_style_list[12]='btn dark btn-outline';
        btn_style_list[13]='btn red btn-outline';
        btn_style_list[14]='btn grey-mint btn-outline sbold uppercase';
        return btn_style_list[index];
    }

    //小图标快捷调用2
    var icon_fun=function(index){ //参数（图标索引）
        var icon_list=new Array();
        icon_list[0]='icon-note';
        icon_list[1]='icon-pencil';
        icon_list[2]='icon-trophy';
        icon_list[3]='icon-speedometer';
        icon_list[4]='icon-social-dropbox';
        icon_list[5]='icon-plane';
        icon_list[6]='icon-fire';
        icon_list[7]='icon-envelope-letter';
        icon_list[8]='icon-energy';
        icon_list[9]='icon-emoticon-smile';
        icon_list[10]='icon-book-open';
        icon_list[11]='icon-briefcase';
        icon_list[12]='icon-cup';
        icon_list[13]='icon-diamond';
        icon_list[14]='icon-drop';
        icon_list[15]='icon-film';
        icon_list[16]='icon-globe';
        icon_list[17]='icon-globe-alt';
        icon_list[18]='icon-handbag';
        icon_list[19]='icon-layers';
        icon_list[20]='icon-map';
        icon_list[21]='icon-present';
        icon_list[22]='icon-speech';
        icon_list[23]='icon-wallet';
        icon_list[24]='icon-bar-chart';
        icon_list[25]='icon-calendar';
        icon_list[26]='icon-equalizer';
        icon_list[27]='icon-graph';
        icon_list[28]='icon-grid';
        icon_list[29]='icon-list';
        icon_list[30]='icon-pie-chart';
        icon_list[31]='icon-rocket';
        icon_list[32]='icon-support';
        icon_list[33]='icon-umbrella';
        icon_list[34]='icon-flag';
        icon_list[35]='icon-paper-plane';
        icon_list[36]='icon-star';
        icon_list[37]='icon-target';
        icon_list[38]='icon-info';
        icon_list[39]='icon-eye';
        return icon_list[index];
    }

    //公共：把php时间戳转成js日期格式
    var getYmdTime=function(time){     //参数（php时间戳）
        if(time > 0){
            var dateStr = new Date(time * 1000);
            return dateStr.getFullYear() + '-' + (dateStr.getMonth()+1) +'-' + dateStr.getDate() + ' ' + dateStr.getHours() + ':' + dateStr.getMinutes() + ':' + dateStr.getSeconds();
        }else{
            return '末知时间';
        }
    }

    //公共：显示出出错的数据信息
    var note_error_data=function(data){ //参数（出错的信息数据）
        if(data['info']){
            Public.ShowNoteTop(data['info']);   //顶部显示出来出错的信息
        }else if(data['code']==0){
            Public.ShowNoteTop(data['msg']);   //顶部显示出来error出错的信息
        }else{
            Public.ShowNoteTop(data);   //顶部显示出来出错的调试信息
        }
    }

    //判断是否为数组
    var isArray=function(o){    //参数（判断是否为数组）
        return Object.prototype.toString.call(o)=='[object Array]';
    }

    //js的trim函数
    String.prototype.trim = function (char, type) {
      if (char) {
        if (type == 'left') {
          return this.replace(new RegExp('^\\'+char+'+', 'g'), '');
        } else if (type == 'right') {
          return this.replace(new RegExp('\\'+char+'+$', 'g'), '');
        }
        return this.replace(new RegExp('^\\'+char+'+|\\'+char+'+$', 'g'), '');
      }
      return this.replace(/^\s+|\s+$/g, '');
    };
    // // 去除字符串首尾的全部空白
    // var str = ' Ruchee ';
    // console.log('xxx' + str.trim() + 'xxx'); // xxxRucheexxx
     
     
    // // 去除字符串左侧空白
    // str = ' Ruchee ';
    // console.log('xxx' + str.trim(' ', 'left') + 'xxx'); // xxxRuchee xxx
     
     
    // // 去除字符串右侧空白
    // str = ' Ruchee ';
    // console.log('xxx' + str.trim(' ', 'right') + 'xxx'); // xxx Rucheexxx
     
     
    // // 去除字符串两侧指定字符
    // str = '/Ruchee/';
    // console.log(str.trim('/')); // Ruchee
     
     
    // // 去除字符串左侧指定字符
    // str = '/Ruchee/';
    // console.log(str.trim('/', 'left')); // Ruchee/
     
     
    // // 去除字符串右侧指定字符
    // str = '/Ruchee/';
    // console.log(str.trim('/', 'right')); // /Ruchee

    // public functions
    return {

        //main function
        init: function () {
            //initialize here something.
            quick_topMenu();    //顶部按钮样式快速设定
            quick_leftMenu();    //左边菜单栏样式快速设定

            set_left_menu_active();     //根据当前页的url的path，设置左边菜单栏的激活效果

            form_ajax_submit(); //公用后台表单异步提交方法
            modal_ajax_submit();    //公用后台模态框中表单异步提交方法

            bind_toggle_btn();    //绑定开关事件的按钮
            
            edit_user_btn_submit(); //修改用户数据的提交按钮
            check_edit_user();  //修改用户数据时的校验
            edit_user_group();  //修改用户组
            
            //init_table();   //初始化数据表格

            // init_bootstrap_select();    //初始化bootstrap的select

            edit_user_info();    //编辑用户资料
            add_user_check();   //新增用户的验证

            change_switch_fun();    //公用异步开关插件

            bind_data_trash();  //绑定数据清理

            ajax_bind();    //公共：异步方法

            bind_date_picker(); //公用：默认绑定日期插件
            //bind_datetime_picker(); //公共：默认绑定日期时间插件
            bind_datetime_picker2();    //公共：绑定日期时间插件(不指定3点钟)

            ajax_del_row(); //公共：异步删除表格中的一行数据
            ajax_del_rows();    //公共：异步批量删除表格中的数据

            public_bind_ueditor();  //公共：默认绑定百度编辑器

            //bind_upload_event();    //绑定上传事件
            bind_webuploader();     //绑定webuploader上传事件
            edit_upload_file(); //编辑页面有上传文件时，显示出来

            // get_inline();   //后台获取在线人数
        },

        //左边栏菜单的当前页面激活效果显示
        // NowPageMenu:function(nav_li,li_one,li_two){
        //     now_page_menu(nav_li,li_one,li_two);  //参数（栏目菜单对象，一级子菜单对象，二级子菜单对象）
        // },

        //公共异步提交方法
        AjaxSubmit:function(form,jump,modal_obj,refresh){  //参数（form对象，是否跳转，是否隐藏模态框，是否刷新当前页）
            public_ajax_submit(form,jump,modal_obj,refresh);
        },

        //关闭侧边栏菜单
        // CloseSideMenu:function(){
        //     close_sidemenu();
        // },

        //调用datatable
        InitTable:function(){
            init_table();
        },

        //some helper function
        doSomeStuff: function () {
            myFunc();
        }

    };

}();

jQuery(document).ready(function() {    
   Custom.init(); 
});

/***
Usage
***/
//Custom.doSomeStuff();