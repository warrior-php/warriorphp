// 实例级配置
fly.config.timeout = 10000;
fly.config.headers = {
    'content-type': "application/json;charset=utf-8",
    'accept': "application/json",
    "x-requested-with": "XMLHttpRequest",
    "X-SOFT-NAME": "WarriorPHP SaaS Framework",
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
};
fly.config.mode = "no-cors";
fly.config.baseURL = "";

// 添加请求拦截器
fly.interceptors.request.use(request => request);

// 添加响应拦截器，响应拦截器会在 then/catch 处理之前执行
fly.interceptors.response.use(response => {
    const rel = response.data;
    switch (rel.code) {
        case 200: // 请求成功
            break;
        case 202: // Debug
            console.log(rel.message);
            break;
        case 204:
            break;
        case 302:
            break;
        default: // 请求发生错误
            break;
    }
    return response;
}, err => {
    return Promise.reject(err);
});

// http 封装
const http = new Fly();

// GET 请求
http.get = (url, params, callback) => {
    const param = typeof params === 'function' ? {} : params;
    return fly.get(url, param).then(response => {
        typeof params === 'function' ? params(response.data) : (callback && callback(response.data));
    }).catch(console.error);
};

// POST 请求
http.post = (url, params, callback) => {
    const param = typeof params === 'function' ? {} : params;
    return fly.post(url, param).then(response => {
        typeof params === 'function' ? params(response.data) : (callback && callback(response.data));
    }).catch(console.error);
};

// PUT 请求
http.put = (url, params, callback) => {
    const param = typeof params === 'function' ? {} : params;
    return fly.put(url, param).then(response => {
        typeof params === 'function' ? params(response.data) : (callback && callback(response.data));
    }).catch(console.error);
};
