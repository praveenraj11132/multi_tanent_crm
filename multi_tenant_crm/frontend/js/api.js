function apiFetch(url, options = {}) {
    const token = localStorage.getItem('token');

    return fetch(url, {
        ...options,
        headers: {
            ...(options.headers || {}),
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token
        }
    }).then(res => res.json());
}

function getUser() {
    return JSON.parse(localStorage.getItem('user'));
}
