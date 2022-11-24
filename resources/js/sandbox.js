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

import Echo from "laravel-echo";

import Pusher from "pusher-js";
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    wsHost: import.meta.env.VITE_PUSHER_HOST
        ? import.meta.env.VITE_PUSHER_HOST
        : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
    wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
    wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? "https") === "https",
    enabledTransports: ["ws", "wss"],
});

(function () {
    const exportConferencesBtn = document.getElementById("export-conferences");
    if (exportConferencesBtn) {
        exportConferencesBtn.addEventListener("click", (event) => {
            event.preventDefault();
            exportConferences(event);
        });
    }
    async function exportConferences({ target }) {
        const prevText = target.innerText;
        target.disabled = true;
        target.innerText = "Loading...";

        try {
            const url = "/api/export/conferences-csv";
            await window.apiClient.get(url);

            const channel = window.Echo.channel("export.conferences");
            channel.listen("ExportFinished", (e) => {
                console.log(e);

                window.Echo.leaveChannel("export.conferences");
                target.disabled = false;
                target.innerText = prevText;
            });
        } catch {
            target.disabled = false;
            target.innerText = prevText;
        }
    }

    const exportLecturesBtn = document.getElementById("export-lectures");
    if (exportLecturesBtn) {
        exportLecturesBtn.addEventListener("click", (event) => {
            event.preventDefault();
            exportLectures(event);
        });
    }
    async function exportLectures({ target }) {
        const prevText = target.innerText;
        target.disabled = true;
        target.innerText = "Loading...";

        try {
            const url = "/api/export/lectures-csv";
            await window.apiClient.get(url);

            const channel = window.Echo.channel("export.lectures");
            channel.listen("ExportFinished", (e) => {
                console.log(e);

                window.Echo.leaveChannel("export.lectures");
                target.disabled = false;
                target.innerText = prevText;
            });
        } catch {
            target.disabled = false;
            target.innerText = prevText;
        }
    }

    const exportListenersBtn = document.getElementById("export-listeners");
    if (exportListenersBtn) {
        exportListenersBtn.addEventListener("click", (event) => {
            event.preventDefault();
            exportListeners(event);
        });
    }
    async function exportListeners({ target }) {
        const prevText = target.innerText;
        target.disabled = true;
        target.innerText = "Loading...";

        try {
            const url = "/api/export/listeners-csv";
            await window.apiClient.get(url);

            const channel = window.Echo.channel("export.listeners");
            channel.listen("ExportFinished", (e) => {
                console.log(e);

                window.Echo.leaveChannel("export.listeners");
                target.disabled = false;
                target.innerText = prevText;
            });
        } catch {
            target.disabled = false;
            target.innerText = prevText;
        }
    }

    const exportCommentsBtn = document.getElementById("export-comments");
    if (exportCommentsBtn) {
        exportCommentsBtn.addEventListener("click", (event) => {
            event.preventDefault();
            exportComments(event);
        });
    }
    async function exportComments({ target }) {
        const prevText = target.innerText;
        target.disabled = true;
        target.innerText = "Loading...";

        try {
            const url = "/api/export/comments-csv";
            await window.apiClient.get(url);

            const channel = window.Echo.channel("export.comments");
            channel.listen("ExportFinished", (e) => {
                console.log(e);

                window.Echo.leaveChannel("export.comments");
                target.disabled = false;
                target.innerText = prevText;
            });
        } catch {
            target.disabled = false;
            target.innerText = prevText;
        }
    }
})();
