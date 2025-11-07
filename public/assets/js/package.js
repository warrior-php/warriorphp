// 定义包路径
const js = document.scripts;
const packagePath = js[js.length - 1].src.substring(0, js[js.length - 1].src.lastIndexOf("/") + 1);

// 定义加载核心库
Do.setConfig('coreLib', [packagePath + 'libs/jquery.repeater/jquery-1.11.1.js']);