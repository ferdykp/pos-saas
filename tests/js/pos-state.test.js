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

test("package unit converts stock factor while keeping package price", () => {
    const product = {
        name: "Air Mineral",
        base_unit: "pcs",
        price: 10000,
        discount: 0,
        stock: 48,
        price_tiers: [],
        variants: [],
        units: [{ id: 8, name: "Dus", factor: 12, price: 108000, discount: 0 }],
        addons: [],
    };
    const unit = unitFor(product, null, [], 8, 2);
    assert.equal(unit.price, 108000);
    assert.equal(unit.factor, 12);
    assert.equal(unit.unit_name, "Dus");
    assert.equal(unit.stock, 48);
});

test("highest matching wholesale tier is selected", () => {
    const product = {
        name: "Gelas",
        base_unit: "pcs",
        price: 10000,
        discount: 0,
        stock: 100,
        variants: [],
        units: [],
        addons: [],
        price_tiers: [
            { min_quantity: 10, price: 9000, discount: 0 },
            { min_quantity: 25, price: 8000, discount: 0 },
        ],
    };
    assert.equal(unitFor(product, null, [], null, 9).price, 10000);
    assert.equal(unitFor(product, null, [], null, 10).price, 9000);
    assert.equal(unitFor(product, null, [], null, 30).price, 8000);
});

test("fractional quantity totals are rounded to whole rupiah", () => {
    assert.deepEqual(totals([{ price: 15500, discount: 500, quantity: 1.5 }], 11), {
        subtotal: 23250,
        discount: 750,
        tax: 2475,
        total: 24975,
    });
});
