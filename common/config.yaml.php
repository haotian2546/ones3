# <?php exit; ?>

- backend:
  MAIL_CHARSET:   UTF-8   #编码
  MAIL_AUTH:      true   #邮箱认证
  MAIL_HTML:      true   #true HTML格式 false TXT格式
  TMPL_EXCEPTION_FILE: ./server/Tpl/exception.html
  LANG: zh-cn
  - preloadJSDebug:
    - "vendor/jquery/jquery-2.1.1.min.js"
    - "vendor/angular-1.3.0-rc/angular.min.js"
    - "vendor/bootstrap.min.js"
    - "vendor/angular-1.3.0-rc/angular-resource.min.js"
    - "vendor/angular-1.3.0-rc/angular-cookies.min.js"
    - "vendor/angular-1.3.0-rc/angular-sanitize.min.js"
    - "vendor/angular-1.3.0-rc/angular-route.min.js"
    - "vendor/angular-1.3.0-rc/angular-touch.min.js"
    - "vendor/angular-1.3.0-rc/angular-animate.min.js"
    - "vendor/angularModules/angular-strap/angular-strap.js"
    - "vendor/angularModules/angular-strap/angular-strap.tpl.js"
    - "vendor/angularModules/ui-utils/ui-utils.min.js"
    - "vendor/angularModules/gridster/angular-gridster.js"
    - "vendor/jquery/chosen/chosen.jquery.js"
    - "vendor/angularModules/angular-chosen/chosen.js"
    - "vendor/highcharts/highcharts.js"
    - "vendor/highcharts/highchartsExporting.js"
    - "vendor/highcharts/ng-highcharts.js"
    - "vendor/jquery/select2.js"
    - "base/config.js"
    - "lib/gridView.js"
    - "lib/detailView.js"
    - "lib/caches.js"
    - "lib/plugin.js"
    - "lib/function.js"
    - "lib/select3.js"
    - "lib/formMaker.js"
    - "lib/print.js"
    - "base/controller.js"
    - "base/filter.js"
    - "base/directive.js"
    - "base/service.js"
    - "base/plugin.js"
  - preloadJS:
    - "vendor/jquery/jquery-2.1.1.min.js"
    - "vendor/angular-1.3.0-rc/angular.min.js"
    - "vendor/bootstrap.min.js"
    - "vendor/angular-1.3.0-rc/angular-resource.min.js"
    - "vendor/angular-1.3.0-rc/angular-cookies.min.js"
    - "vendor/angular-1.3.0-rc/angular-sanitize.min.js"
    - "vendor/angular-1.3.0-rc/angular-route.min.js"
    - "vendor/angular-1.3.0-rc/angular-touch.min.js"
    - "vendor/angular-1.3.0-rc/angular-animate.min.js"
    - "vendor/angularModules/angular-strap/angular-strap.min.js"
    - "vendor/angularModules/angular-strap/angular-strap.tpl.min.js"
    - "vendor/angularModules/ui-utils/ui-utils.min.js"
    - "vendor/angularModules/gridster/angular-gridster.min.js"
    - "vendor/jquery/chosen/chosen.jquery.js"
    - "vendor/angularModules/angular-chosen/chosen.js"
    - "vendor/highcharts/highcharts.js"
    - "vendor/highcharts/highchartsExporting.js"
    - "vendor/highcharts/ng-highcharts.js"
    - "vendor/jquery/select2.js"
    - "base/config.js"
    - "lib/gridView.js"
    - "lib/detailView.js"
    - "lib/caches.js"
    - "lib/plugin.js"
    - "lib/function.js"
    - "lib/select3.js"
    - "lib/formMaker.js"
    - "lib/print.js"
    - "base/controller.js"
    - "base/filter.js"
    - "base/directive.js"
    - "base/service.js"
    - "base/plugin.js"