//services/api.ts

import axios from "axios";

const rawBaseUrl = import.meta.env.VITE_API_BASE_URL;
const baseURL = `${rawBaseUrl.replace(/\/+$/, "")}/api`;

const api = axios.create({
    baseURL,
    headers: {
        "Accept": "application/json"
    },
});

api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem("apiToken");
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => Promise.reject(error)
);

// Requests where a 401 means "wrong credentials", not "session expired"
const AUTH_ENDPOINTS = ['/login', '/register'];

api.interceptors.response.use(
    (response) => response,
    (error) => {
        const requestUrl: string = error?.config?.url || '';
        const isAuthAttempt = AUTH_ENDPOINTS.some((path) => requestUrl.includes(path));

        if (error.response && error.response.status === 401 && !isAuthAttempt) {
            localStorage.removeItem('user')
            localStorage.removeItem('apiToken')

            if (window.location.pathname !== "/") {
                window.location.href = "/";
            }
        }

        return Promise.reject(error);
    }
);

export default api;