import { test } from "node:test";
import assert from "node:assert/strict";
import {
    rupiahDigits,
    rupiahInput,
} from "../../resources/js/currency-input.js";

test("currency helpers import without a browser and keep numeric payloads", () => {
    assert.equal(typeof document, "undefined");
    assert.equal(rupiahDigits("Rp 125.000"), "125000");
    assert.equal(rupiahInput(125000), "Rp 125.000");
    assert.equal(rupiahInput(""), "");
});
