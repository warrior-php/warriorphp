// 定义包路径
const js = document.scripts;
const currentPath = js[js.length - 1].src.substring(0, js[js.length - 1].src.lastIndexOf("/") + 1); // 当前目录
const parentPath = currentPath.substring(0, currentPath.lastIndexOf("/", currentPath.length - 2) + 1); // 上一级目录

// 指定核心库
Do.setConfig('coreLib', [currentPath + 'jquery.min.js']);

Do.add('bootstrap', {path: parentPath + 'libs/bootstrap/dist/js/bootstrap.bundle.min.js', type: 'js'});
Do.add('theme', {path: currentPath + 'theme/theme.js', type: 'js'});
Do.add('app.init', {path: currentPath + 'theme/app.init.js', type: 'js'});
Do.add('app.min', {path: currentPath + 'theme/app.min.js', type: 'js', requires: ['theme', 'app.init']});
Do.add('toastr-init', {path: currentPath + 'plugins/toastr-init.js', type: 'js'});
Do.add('jquery.sessionTimeout.min', {path: currentPath + 'extra-libs/jquery-sessiontimeout/jquery.sessionTimeout.min.js', type: 'js'});
Do.add('session-timeout-init', {path: currentPath + 'extra-libs/jquery-sessiontimeout/session-timeout-init.js', type: 'js', requires: ['jquery.sessionTimeout.min']});

// 基础
Do.add('base', {path: currentPath + 'common.js', type: 'js', requires: ['bootstrap', 'app.min', 'http', 'toastr-init', 'session-timeout-init']});

// 请求
Do.add('request', {path: currentPath + 'http/http.min.js', type: 'js',});
Do.add('http', {path: currentPath + 'http/request.js', type: 'js', requires: ['request']});
// 表单
Do.add('validation', {path: currentPath + 'extra-libs/jqbootstrapvalidation/validation.js', type: 'js'});
Do.add('custom-validation-init', {path: currentPath + 'forms/custom-validation-init.js', type: 'js', requires: ['validation']});
Do.add('jquery.form', {path: currentPath + 'forms/jquery.form.js', type: 'js'});
Do.add('form', {path: currentPath + 'forms/form.js', type: 'js', requires: ['validation', 'custom-validation-init', 'jquery.form']});
// 仪表盘
Do.add('dashboard', {path: currentPath + 'dashboards/dashboard.js', type: 'js',});