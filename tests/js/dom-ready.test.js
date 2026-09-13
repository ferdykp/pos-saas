import { test } from "node:test";
import assert from "node:assert/strict";
import { onDomReady } from "../../resources/js/dom-ready.js";

test("late module initialization does not miss DOM readiness", () => {
    for (const readyState of ["interactive", "complete"]) {
        let calls = 0;
        onDomReady(() => calls++, {
            readyState,
            addEventListener() {
                throw new Error("Event already fired");
            },
        });
        assert.equal(calls, 1);
    }
});

test("early module waits for DOM readiness with a one-time listener", () => {
    let calls = 0;
    let listener;
    onDomReady(() => calls++, {
        readyState: "loading",
        addEventListener(event, callback, options) {
            assert.equal(event, "DOMContentLoaded");
            assert.equal(options.once, true);
            listener = callback;
        },
    });
    assert.equal(calls, 0);
    listener();
    assert.equal(calls, 1);
});
