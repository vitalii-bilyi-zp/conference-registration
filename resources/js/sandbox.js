import axios from "axios";

window.apiClient = axios.create({
    baseURL: import.meta.env.VITE_APP_URL || "",
    headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
    },
});

function bindToken(token) {
    window.apiClient.defaults.headers.common[
        "Authorization"
    ] = `Bearer ${token}`;
}

function removeToken() {
    delete window.apiClient.defaults.headers.common["Authorization"];
}

(function () {
    let tokenBinded = false;

    const form = document.getElementById("token-form");
    if (!form) {
        return;
    }

    form.addEventListener("submit", (event) => {
        event.preventDefault();

        if (tokenBinded) {
            handleRemoveToken();
        } else {
            handleBindToken();
        }

        tokenBinded = !tokenBinded;
    });

    function handleBindToken() {
        const input = document.getElementById("token-input");
        if (!input) {
            return;
        }

        bindToken(input.value);

        input.disabled = true;

        const submitBtn = document.getElementById("token-submit");
        if (!submitBtn) {
            return;
        }
        submitBtn.innerText = "Reset";
    }

    function handleRemoveToken() {
        const input = document.getElementById("token-input");
        if (!input) {
            return;
        }

        removeToken();

        input.value = "";
        input.disabled = false;

        const submitBtn = document.getElementById("token-submit");
        if (!submitBtn) {
            return;
        }
        submitBtn.innerText = "Submit";
    }
})();

(function () {
    const exportConferencesBtn = document.getElementById("export-conferences");
    if (exportConferencesBtn) {
        exportConferencesBtn.addEventListener("click", (event) => {
            event.preventDefault();
            exportConferences();
        });
    }
    function exportConferences() {
        const url = "/api/export/conferences-csv";
        window.apiClient.get(url);
    }

    const exportLecturesBtn = document.getElementById("export-lectures");
    if (exportLecturesBtn) {
        exportLecturesBtn.addEventListener("click", (event) => {
            event.preventDefault();
            exportLectures();
        });
    }
    function exportLectures() {
        const url = "/api/export/lectures-csv";
        window.apiClient.get(url);
    }

    const exportListenersBtn = document.getElementById("export-listeners");
    if (exportListenersBtn) {
        exportListenersBtn.addEventListener("click", (event) => {
            event.preventDefault();
            exportListeners();
        });
    }
    function exportListeners() {
        const url = "/api/export/listeners-csv";
        window.apiClient.get(url);
    }

    const exportCommentsBtn = document.getElementById("export-comments");
    if (exportCommentsBtn) {
        exportCommentsBtn.addEventListener("click", (event) => {
            event.preventDefault();
            exportComments();
        });
    }
    function exportComments() {
        const url = "/api/export/comments-csv";
        window.apiClient.get(url);
    }
})();
