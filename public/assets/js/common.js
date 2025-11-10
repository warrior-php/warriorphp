(function ($) {
    /**
     * 随机字符串
     * @param options
     */
    $.fn.randomWord = function (options) {
        options = $.extend({len: 43, dom: this}, options);
        $(this).click(function () {
            let str = "", range = options.min;
            const arr = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
            if (options.len) {
                range = Math.round(Math.random() * (options.len - options.len)) + options.len;
            }
            let pos;
            for (let i = 0; i < range; i++) {
                pos = Math.round(Math.random() * (arr.length - 1));
                str += arr[pos];
            }
            $(options.dom).val(str)
        })
    };

    /**
     * 点击URL请求
     * @param options
     * @param callback
     */
    $.fn.actUrl = function (options, callback) {
        options = $.extend({confirm: false}, options);
        $(this).click(function () {
            options.follow = this;
            $(this).requestUrl(options, callback);
            return false;
        })
    };

    /**
     * requestUrl
     * @param options
     * @param callback
     * @returns {boolean}
     */
    $.fn.requestUrl = function (options, callback) {
        if ("disabled" === $(this).attr("disabled")) {
            return false;
        }
        $(this).attr("disabled", "disabled");
        options = $.extend({confirm: false, post: false, param: {}, content: 'Confirm action', text: 'Click Confirm to perform this action'}, options);
        let uri = options.hasOwnProperty('uri') ? options.uri : (!!$(this).attr("url") ? $(this).attr("url") : $(this).attr("href"));
        if (uri === undefined) {
            $(this).removeAttr("disabled");
            toastr.error('The requested URL could not be found.');
            return false;
        }

        // 返回结果处理
        let returnfun = (rel) => {
            if (options.delete && rel.code === 204) {
                $(this).parents("tr").remove();
                $(this).parent().remove();
            }
            "function" == typeof callback && callback(rel);
        }

        // 确认还是直接执行
        options.end = () => {
            $(this).removeAttr("disabled");
        }
        // 显示弹窗操作
        if (options.confirm) {
            console.log('确认弹窗没有实现');
        } else {
            options.post ? http.post(uri, options.param, returnfun) : http.get(uri, options.param, returnfun);
        }
        return false;
    };
})(jQuery);