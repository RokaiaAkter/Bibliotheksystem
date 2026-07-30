"use strict";

document.addEventListener("DOMContentLoaded", () => {
    markCurrentNavigation();
    enableConfirmations();
    enableAjaxBookSearch();
});

function markCurrentNavigation() {
    const route = new URLSearchParams(window.location.search).get("route") || "dashboard";

    document.querySelectorAll(".sidebar nav a").forEach((link) => {
        const linkRoute = new URL(link.href).searchParams.get("route") || "dashboard";

        if (linkRoute === route) {
            link.setAttribute("aria-current", "page");
        }
    });
}

function enableConfirmations() {
    document.addEventListener("click", (event) => {
        const button = event.target.closest("[data-confirm]");

        if (button && !window.confirm(button.dataset.confirm)) {
            event.preventDefault();
        }
    });
}

function enableAjaxBookSearch() {
    const input = document.querySelector("[data-ajax-book-search]");
    const tableBody = document.querySelector("[data-book-results]");
    const status = document.querySelector("[data-search-status]");

    if (!input || !tableBody || !status) {
        return;
    }

    let timer = null;
    let controller = null;

    input.addEventListener("input", () => {
        window.clearTimeout(timer);
        timer = window.setTimeout(async () => {
            if (controller) {
                controller.abort();
            }

            controller = new AbortController();
            status.textContent = "Suche läuft …";

            try {
                const endpoint = new URL(input.dataset.endpoint, window.location.origin);
                endpoint.searchParams.set("q", input.value.trim());
                const response = await fetch(endpoint, {
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                    signal: controller.signal,
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const payload = await response.json();
                renderBookRows(tableBody, Array.isArray(payload.data) ? payload.data : []);
                status.textContent = `${payload.data.length} Ergebnis(se) · per Ajax geladen`;
            } catch (error) {
                if (error.name !== "AbortError") {
                    status.textContent = "Ajax-Suche fehlgeschlagen. Die normale Suche funktioniert weiter.";
                }
            }
        }, 280);
    });
}

function renderBookRows(tableBody, books) {
    const canManage = tableBody.dataset.canManage === "1";
    const deleteUrl = tableBody.dataset.deleteUrl;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || "";
    const fragment = document.createDocumentFragment();

    books.forEach((book) => {
        const row = document.createElement("tr");
        row.append(
            textCell(book.title),
            textCell(book.author),
            codeCell(book.isbn),
            statusCell(book.status)
        );

        if (canManage) {
            const actionCell = document.createElement("td");
            const form = document.createElement("form");
            const csrfInput = hiddenInput("_csrf", csrfToken);
            const idInput = hiddenInput("book_id", String(book.id));
            const button = document.createElement("button");

            form.method = "post";
            form.action = deleteUrl;
            button.type = "submit";
            button.className = "button button-danger button-small";
            button.textContent = "Löschen";
            button.disabled = book.status !== "available";
            button.dataset.confirm = "Dieses verfügbare Buch wirklich löschen?";
            form.append(csrfInput, idInput, button);
            actionCell.append(form);
            row.append(actionCell);
        }

        fragment.append(row);
    });

    tableBody.replaceChildren(fragment);
}

function textCell(value) {
    const cell = document.createElement("td");
    cell.textContent = value ?? "";
    return cell;
}

function codeCell(value) {
    const cell = document.createElement("td");
    const code = document.createElement("code");
    code.textContent = value ?? "";
    cell.append(code);
    return cell;
}

function statusCell(statusValue) {
    const cell = document.createElement("td");
    const badge = document.createElement("span");
    const status = statusValue === "available" ? "available" : "loaned";

    badge.className = `status status-${status}`;
    badge.textContent = status === "available" ? "verfügbar" : "ausgeliehen";
    cell.append(badge);
    return cell;
}

function hiddenInput(name, value) {
    const input = document.createElement("input");
    input.type = "hidden";
    input.name = name;
    input.value = value;
    return input;
}
