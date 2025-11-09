(function ($) {
    $.fn.isForm = function (options, callback) {
        let param = {
            type: 'POST', timeout: 10000, datatype: "json", headers: {
                "X-SOFT-NAME": "WarriorPHP SaaS Framework", 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        };

        if (typeof options !== 'function') {
            param = $.extend(param, options);
        }

        return this.each(function () {
            const $form = $(this);
            // 防止重复提交
            $form.data('submitting', false);
            // 添加 holdSubmit 方法
            $form.holdSubmit = function (hold = true) {
                $form.data('submitting', hold);
            };
            // 绑定提交事件
            $form.off('submit.isForm').on('submit.isForm', function (e) {
                e.preventDefault();
                // 防止重复提交
                if ($form.data('submitting')) {
                    return false;
                }
                $form.holdSubmit(true);
                // 如果不是 POST 就正常提交
                if (param.type.toUpperCase() !== 'POST') {
                    this.submit();
                    $form.holdSubmit(false);
                    return;
                }
                // 使用 ajax 提交
                $form.ajaxSubmit({
                    type: param.type, dataType: param.datatype, headers: param.headers, timeout: param.timeout, async: true, success: function (rel) {
                        switch (rel.code) {
                            case 200:
                                toastr.success(rel.msg);
                                break;
                            case 204:
                                console.info(rel);
                                break;
                            case 302:
                                toastr.success(rel.msg);
                                setTimeout(function () {
                                    if (rel.hasOwnProperty('url')) window.location.replace(rel.url);
                                    else window.location.reload();
                                }, 1000);
                                break;
                            default:
                                toastr.error(rel.msg);
                                break;
                        }
                        // 调用回调
                        if (typeof options === 'function') {
                            options(rel);
                        } else if (typeof callback === 'function') {
                            callback(rel);
                        }
                        $form.holdSubmit(false);
                    }, error: function () {
                        // 请求出错，请稍后重试
                        $form.holdSubmit(false);
                    }
                });
            });
        });
    };
})(jQuery);