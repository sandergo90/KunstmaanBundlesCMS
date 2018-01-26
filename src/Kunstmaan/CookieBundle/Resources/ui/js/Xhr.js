/**
 * GET Wrapper
 * @param url
 * @returns Promise
 */
function get(url) {
    return new Promise((resolve, reject) => {
        const req = new XMLHttpRequest();

        req.onload = () => (
            (req.status >= 200 && req.status < 400) ? resolve(req) : reject(Error(req.statusText))
        );

        req.onerror = (e) => {
            reject(Error(`Network Error: ${e}`));
        };

        req.open('GET', url);
        req.send();
    });
}

/**
 * POST Wrapper
 * @param url
 * @param data
 * @returns Promise
 */
function post(url, data) {
    return new Promise((resolve, reject) => {
        const req = new XMLHttpRequest();

        req.onload = () => (
            req.status === 200 ? resolve(req) : reject(Error(req.statusText))
        );

        req.onerror = (e) => {
            reject(Error(`Network Error: ${e}`));
        };

        req.open('POST', url);
        req.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        req.send(data);
    });
}

export default {
    get,
    post
};
