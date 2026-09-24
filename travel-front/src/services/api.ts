import axios from "axios";
import i18n from "@/i18n"; // Import your vue-i18n instance

const rawBaseUrl = import.meta.env.VITE_API_BASE_URL || "";
const baseURL = `${rawBaseUrl.replace(/\/+$/, "")}/api`;

const api = axios.create({
  baseURL,
  headers: {
    Accept: "application/json",
  },
});

api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem("apiToken");

    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }

    // 1. Read locale directly from vue-i18n reactive instance (with localStorage fallback)
    const currentLocale =
      (i18n.global.locale as any)?.value ||
      i18n.global.locale ||
      localStorage.getItem("locale") ||
      "en";

    // 2. Set headers for backend frameworks (Laravel reads Accept-Language by default)
    config.headers["X-Locale"] = currentLocale;
    config.headers["Accept-Language"] = currentLocale;

    // 3. Attach query parameter as a fallback (?lang=en)
    config.params = {
      lang: currentLocale,
      ...config.params,
    };

    return config;
  },
  (error) => Promise.reject(error)
);

const AUTH_ENDPOINTS = ["/login", "/register"];

api.interceptors.response.use(
  (response) => response,

  (error) => {
    const requestUrl: string = error?.config?.url || "";

    const isAuthAttempt = AUTH_ENDPOINTS.some((path) =>
      requestUrl.includes(path)
    );

    if (
      error.response &&
      error.response.status === 401 &&
      !isAuthAttempt
    ) {
      localStorage.removeItem("user");
      localStorage.removeItem("apiToken");

      if (window.location.pathname !== "/") {
        window.location.href = "/";
      }
    }

    return Promise.reject(error);
  }
);

export default api;