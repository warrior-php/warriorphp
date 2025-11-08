(function (window, document) {
    const loaded = {}, loading = {}, waiting = {};
    const config = {
        autoLoad: true, timeout: 6000, coreLib: ["https://cdn.bootcdn.net/ajax/libs/jquery/3.5.1/jquery.min.js"], mods: {}
    };
    const lastScript = (() => {
        const scripts = document.getElementsByTagName("script");
        return scripts[scripts.length - 1];
    })();
    let globalQueue = [], readyQueue = [], isReady = false;
    const dataCache = {}, dataWait = {};

    const isArray = a => a.constructor === Array;
    const getMod = a => {
        const mods = config.mods;
        if (typeof a === "string") return mods[a] ? mods[a] : {path: a, async: true};
        return a;
    };

    const loadFile = (mod, callback) => {
        if (!mod || loaded[mod.path]) {
            if (callback) callback();
            return;
        }
        if (loading[mod.path]) {
            setTimeout(() => loadFile(mod, callback), 10);
            return;
        }
        loading[mod.path] = true;

        const done = () => {
            loaded[mod.path] = 1;
            loading[mod.path] = false;
            if (callback) callback();
        };

        const ext = mod.type || mod.path.toLowerCase().split('.').pop();
        let element;

        if (ext === "js") {
            element = document.createElement("script");
            element.type = "text/javascript";
            element.src = mod.path;
            element.async = mod.async !== false; // 默认异步
        } else if (ext === "css") {
            element = document.createElement("link");
            element.type = "text/css";
            element.rel = "stylesheet";
            element.href = mod.path;
        }

        if (mod.charset) element.charset = mod.charset;

        if (ext === "css") {
            const img = new Image();
            img.onerror = () => {
                done();
                img.onerror = null;
            };
            img.src = mod.path;
        } else {
            element.onerror = () => {
                done();
                element.onerror = null;
            };
            element.onload = element.onreadystatechange = function () {
                if (!this.readyState || this.readyState === "loaded" || this.readyState === "complete") {
                    done();
                    element.onload = element.onreadystatechange = null;
                }
            };
        }

        lastScript.parentNode.insertBefore(element, lastScript);
    };

    const loadMods = (mods, callback) => {
        if (!mods || !mods.length) {
            if (callback) callback();
            return;
        }
        let count = mods.length;
        const next = () => {
            if (--count === 0 && callback) callback();
        };

        for (const modName of mods) {
            const mod = getMod(modName);
            if (mod.requires) {
                loadMods(mod.requires, () => loadFile(mod, next));
            } else {
                loadFile(mod, next);
            }
        }
    };

    const domReady = fn => {
        if (document.readyState === "complete") {
            fn();
            return;
        }
        document.addEventListener("DOMContentLoaded", fn);
    };

    const Do = function () {
        const args = [].slice.call(arguments);
        let cb = null;
        if (typeof args[args.length - 1] === "function") cb = args.pop();
        if (config.autoLoad && !loaded[config.coreLib.join("")]) {
            loadMods(config.coreLib, () => Do.apply(null, args.concat(cb)));
            return;
        }
        if (globalQueue.length > 0 && !loaded[globalQueue.join("")]) {
            loadMods(globalQueue, () => Do.apply(null, args.concat(cb)));
            return;
        }
        if (cb) loadMods(args, cb); else loadMods(args);
    };

    // API
    Do.add = (name, mod) => {
        if (!name || !mod || !mod.path) return;
        if (mod.async === undefined) mod.async = true;
        config.mods[name] = mod;
    };

    Do.delay = (...args) => {
        const time = args.shift();
        setTimeout(() => Do.apply(null, args), time);
    };

    Do.global = (...args) => {
        const arr = isArray(args[0]) ? args[0] : args;
        globalQueue = globalQueue.concat(arr);
    };

    Do.ready = (...args) => {
        if (isReady) Do.apply(null, args); else readyQueue.push(args);
    };

    Do.css = css => {
        let style = document.getElementById("do-inline-css");
        if (!style) {
            style = document.createElement("style");
            style.id = "do-inline-css";
            lastScript.parentNode.insertBefore(style, lastScript);
        }
        if (style.styleSheet) style.styleSheet.cssText += css; else style.appendChild(document.createTextNode(css));
    };

    Do.setData = Do.setPublicData = (key, value) => {
        dataCache[key] = value;
        if (dataWait[key]) while (dataWait[key].length) dataWait[key].pop()(value);
    };

    Do.getData = Do.getPublicData = (key, callback) => {
        if (dataCache[key]) callback(dataCache[key]); else {
            if (!dataWait[key]) dataWait[key] = [];
            dataWait[key].push(callback);
        }
    };

    Do.setConfig = (key, value) => {
        config[key] = value;
        return Do;
    };
    Do.getConfig = key => config[key];

    domReady(() => {
        isReady = true;
        while (readyQueue.length) Do.apply(null, readyQueue.shift());
    });

    window.Do = Do;

})(window, document);
