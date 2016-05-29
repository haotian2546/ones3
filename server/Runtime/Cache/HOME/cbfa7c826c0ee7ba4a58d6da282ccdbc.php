<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta name="renderer" content="webkit" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta charset="utf-8" />
    <title>ONES Login Page</title>
    <meta name="description" content="User login page" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
    <link rel="shortcut icon" href="common/statics/images/favicon.ico" />
    <link rel="stylesheet" href="common/statics/css/bootstrap.min.css" />
    <link rel="stylesheet" href="common/statics/css/bootstrap-theme.min.css" />
    <link rel="stylesheet" href="common/statics/css/font-awesome.min.css" />
    <link rel="stylesheet" href="common/statics/css/app/login.css">
</head>

<body class="login-layout ones-login-signin" id="animate-area">

    <div class="signin-container" ng-controller="LoginCtl" id="signin-container">

        <div class="signin-info">
            <a href="#" target="_blank" class="logo">
                <img src="common/statics/images/logo_mini_white.png" />
            </a>
            <div class="slogan">
                简单. 灵活. 实用. 强大.
            </div>
            <ul>
                <li>
                    <a onclick="ones.caches.clear();window.location.reload()">
                        <i class="fa fa-warning signin-icon"></i>
                        清除缓存
                    </a>
                </li>
                <!--<li>
                    <a href="http://project.ng-erp.com/projects/ones/wiki/ONES_ERP_%E4%BD%BF%E7%94%A8%E6%89%8B%E5%86%8C" target="_blank">
                        <i class="fa fa-file-text-o signin-icon"></i> 使用手册
                    </a>
                </li>
                <li>
                    <a href="http://project.ng-erp.com/projects/ones/boards" target="_blank">
                        <i class="fa fa-comments signin-icon"></i> 讨论 &amp; 反馈
                    </a>
                </li>
                <li>
                    <a href="http://project.ng-erp.com/projects/ones/boards" target="_blank">
                        <i class="fa fa-support signin-icon"></i> 商业用户支持
                    </a>
                </li>
                <li>
                    <a href="https://ng-erp.com/mobile" target="_blank">
                        <i class="fa fa-apple signin-icon"></i> 手机客户端下载
                    </a>
                </li>-->
            </ul>
        </div>

        <div class="signin-form">
            <form name="LoginForm" autocomplete="off">
                <div class="signin-text">
                    <span ng-bind="'lang.messages.please_enter_your_login_info'|lang"></span>
                </div>

                <div class="alert alert-danger hide" ng-class="{'hide':!error.isError}">
                    <div><span ng-bind-html="error.msg"></span></div>
                </div>

                <div class="form-group w-icon">
                    <input type="text" name="signin_username" id="username_id" class="form-control input-lg" ng-model="loginInfo.username" placeholder="{{'lang.username'|lang}}" autocomplete="off" autofocus>
                    <span class="fa fa-user signin-form-icon"></span>
                </div>

                <div class="form-group w-icon">
                    <input type="password" name="signin_password" id="password_id" class="form-control input-lg" ng-model="loginInfo.password" placeholder="{{'lang.password'|lang}}" autocomplete="off">
                    <span class="fa fa-lock signin-form-icon"></span>
                </div>

                <div class="form-actions">
                    <button class="signin-btn bg-primary" ng-click="doLogin()" ng-disabled="LoginForm.$invalid">
                        <span ng-bind="'lang.actions.login'|lang"></span>
                    </button>
                    <a href="#" class="forgot-password" id="forgot-password-link">
                        <span ng-bind="'lang.messages.i_fogot_password'|lang"></span>
                    </a>
                </div>
            </form>

            <div class="signin-with">
                Present by <a href="#" target="_blank">ONES Team</a> &copy; 2014
            </div>

            <div class="password-reset-form" id="password-reset-form" style="display: none;">
                <div class="header">
                    <div class="signin-text">
                        <span>Password reset</span>
                        <div class="close">×</div>
                    </div>
                </div>

                <form action="index.html" id="password-reset-form_id" novalidate="novalidate">
                    <div class="form-group w-icon">
                        <input type="text" name="password_reset_email" id="p_email_id" class="form-control input-lg" placeholder="{{'lang.email'|lang}}">
                        <span class="icon fa-envelope signin-form-icon"></span>
                    </div>

                    <div class="form-actions">
                        <input type="submit" value="SEND PASSWORD RESET LINK" class="signin-btn bg-primary">
                    </div>
                </form>
            </div>

        </div>
    </div>
<script type="text/javascript">
    var ones = {
        BaseConf: {
            LoadedApps: ['<?php echo ($loadedAppsStr); ?>'],
            DEBUG: <?php echo ($isDebug); ?>
        }
    };
</script>
<script src="common/vendor/jquery/jquery-2.1.1.min.js"></script>
<script src="common/vendor/angular-1.3.0-rc/angular.min.js"></script>
<script src="common/vendor/angular-1.3.0-rc/angular-resource.min.js"></script>
<script src="common/lib/function.js"></script>
<script src="common/lib/caches.js"></script>
<script src="common/base/config.js"></script>
<script src="common/base/login.js"></script>
<script src="common/base/filter.js"></script>

<script type="text/javascript">
    function show_box(id) {
        jQuery('.widget-box.visible').removeClass('visible');
        jQuery('#' + id).addClass('visible');
    }
    /**
     * i18n
     * */
    jQuery(function(){
        /**
         * 加载语言包
         * */
        ones.i18n = ones.caches.getItem("ones.i18n");
        if((!ones.i18n || isEmptyObject(ones.i18n)) && !ones.installing) {
            jQuery.get(ones.BaseConf.BSU+"FrontendRuntime/index/action/getI18n/lang/zh-cn").success(function(data) {
                ones.caches.setItem("ones.i18n", data, 1);
                ones.i18n = ones.caches.getItem("ones.i18n");
                if(!ones.i18n) {
                    throw("can't load i18n package.");
                }

                angular.element(document).ready(function() {
                    angular.bootstrap(document, ['ones.login']);
                });
            });
        } else {
            angular.element(document).ready(function() {
                angular.bootstrap(document, ['ones.login']);
            });
        }

        setTimeout(function(){
            $("#signin-container").animate({
                opacity: 1
            }, 800);
        }, 500);


        $("#password-reset-form .close").click(function(){
            $("#password-reset-form").fadeOut();
        });
        $("#forgot-password-link").click(function(){
            $("#password-reset-form").fadeIn();
        });
    });
</script>

</body>
</html>