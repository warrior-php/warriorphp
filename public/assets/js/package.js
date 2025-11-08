// 定义包路径
const js = document.scripts;
const currentPath = js[js.length - 1].src.substring(0, js[js.length - 1].src.lastIndexOf("/") + 1); // 当前目录
const parentPath = currentPath.substring(0, currentPath.lastIndexOf("/", currentPath.length - 2) + 1); // 上一级目录

// 指定核心库
Do.setConfig('coreLib', [currentPath + 'jquery.min.js']);

Do.add('base', {path: currentPath + 'common.js', type: 'js'});