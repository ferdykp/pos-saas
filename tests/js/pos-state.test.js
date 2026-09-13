import { test } from "node:test";
import assert from "node:assert/strict";
import {
    totals,
    unitFor,
    queueOnce,
    readState,
} from "../../resources/js/pos-state.js";

test("same checkout reference is queued only once", () => {
    const p = { checkout_key: "one", grand_total: 10000 };
    assert.equal(queueOnce(queueOnce([], p), p).length, 1);
});
test("variant price and addons use catalog prices", () => {
    const product = {
        name: "Kopi",
        price: 10000,
        discount: 1000,
        stock: 9,
        variants: [
            { id: 4, name: "Large", price: 15000, discount: 1500, stock: 5 },
        ],
        addons: [{ id: 1, price: 3000 }],
    };
    const unit = unitFor(product, "4", [1]);
    assert.equal(unit.price, 18000);
    assert.equal(unit.discount, 1500);
    assert.equal(unit.stock, 5);
    assert.deepEqual(totals([{ ...unit, quantity: 2 }], 10), {
        subtotal: 36000,
        discount: 3000,
        tax: 3300,
        total: 36300,
    });
});
test("pending payload survives persistence without changing reference", () => {
    const data = {
        queue: queueOnce([], {
            checkout_key: "same-reference",
            items: [{ id: 2, quantity: 1 }],
        }),
    };
    const storage = { getItem: () => JSON.stringify(data) };
    assert.deepEqual(readState(storage, "test", {}), data);
});
