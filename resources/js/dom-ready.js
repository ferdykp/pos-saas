/** Run initialization once, including when a module loads after DOMContentLoaded. */
export function onDomReady(callback, documentRef = document) {
    if (documentRef.readyState === "loading") {
        documentRef.addEventListener("DOMContentLoaded", callback, {
            once: true,
        });
        return;
    }

    callback();
}
