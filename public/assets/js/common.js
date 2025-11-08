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
})(jQuery);