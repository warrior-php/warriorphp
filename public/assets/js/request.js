/** @type {{ get:Function, post:Function, put:Function, delete:Function }} */
const request = {
    async send(url, method = 'GET', data = {}, headers = {}) {
        method = method.toUpperCase();
        let options = {
            method,
            headers: {
                'Accept': 'application/json',
                ...headers
            }
        };

        if (method === 'GET' && Object.keys(data).length > 0) {
            const query = new URLSearchParams(data).toString();
            url += (url.includes('?') ? '&' : '?') + query;
        } else if (['POST', 'PUT', 'DELETE'].includes(method)) {
            options.headers['Content-Type'] = 'application/json';
            options.body = JSON.stringify(data);
        }

        try {
            const res = await fetch(url, options);
            const contentType = res.headers.get('content-type');
            let result = contentType?.includes('application/json') ? await res.json() : await res.text();

            if (!res.ok) {
                return {code: res.status, msg: result.message || result, error: true};
            }

            return result;
        } catch (err) {
            console.error('请求出错：', err);
            return {code: 500, msg: err.message || '请求失败', error: true};
        }
    },

    get(url, params = {}, headers = {}) {
        return this.send(url, 'GET', params, headers);
    },
    post(url, data = {}, headers = {}) {
        return this.send(url, 'POST', data, headers);
    },
    put(url, data = {}, headers = {}) {
        return this.send(url, 'PUT', data, headers);
    },
    delete(url, data = {}, headers = {}) {
        return this.send(url, 'DELETE', data, headers);
    }
};
