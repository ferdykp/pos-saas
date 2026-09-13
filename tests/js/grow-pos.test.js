import { test } from "node:test";
import assert from "node:assert/strict";
import growPos from "../../resources/js/grow-pos.js";

function terminal() {
    Object.defineProperty(globalThis, "navigator", {
        value: { onLine: true },
        configurable: true,
    });
    const pos = growPos();
    pos.config = { checkout: "/pos" };
    pos.persist = () => true;
    return pos;
}
test("network failure preserves the original payload and stops automatic sync", async () => {
    const pos = terminal();
    const payload = { checkout_key: "stable", payment_method: "cash" };
    pos.queue = [
        { payload },
        { payload: { checkout_key: "next", payment_method: "cash" } },
    ];
    let calls = 0;
    pos.request = async () => {
        calls++;
        throw new Error("Connection lost");
    };
    await pos.syncQueue();
    assert.equal(calls, 1);
    assert.equal(pos.queue.length, 2);
    assert.equal(pos.queue[0].payload, payload);
    assert.equal(pos.syncing, false);
    pos.request = async () => ({});
    await pos.syncQueue();
    assert.equal(pos.queue.length, 0);
});
test("stock conflict stays visible while other cash orders sync; QR is never charged in background", async () => {
    const pos = terminal();
    pos.queue = ["conflict", "good", "qr"].map((key) => ({
        payload: {
            checkout_key: key,
            payment_method: key === "qr" ? "midtrans" : "cash",
        },
    }));
    const sent = [];
    pos.request = async (url, method, payload) => {
        sent.push(payload.checkout_key);
        if (payload.checkout_key === "conflict")
            throw Object.assign(new Error("Stok kurang"), { status: 422 });
        return {};
    };
    await pos.syncQueue();
    assert.deepEqual(sent, ["conflict", "good"]);
    assert.deepEqual(
        pos.queue.map((e) => e.payload.checkout_key),
        ["conflict", "qr"],
    );
    assert.equal(pos.queue[0].error, "Stok kurang");
});
test("paid QR replay opens receipt, pending QR remains recoverable without duplicates", () => {
    const pos = terminal();
    const pending = { order_id: 3, qr_url: "/qr", payment_status: "unpaid" };
    pos.showResult(pending);
    pos.showResult(pending);
    assert.equal(pos.pendingPayments.length, 1);
    assert.equal(pos.modal, "qris");
    pos.showResult({ ...pending, payment_status: "paid" });
    assert.equal(pos.modal, "receipt");
});
test("payment confirmation removes recovered QR from waiting list", async () => {
    const pos = terminal();
    pos.payment = { order_id: 3 };
    pos.pendingPayments = [{ order_id: 3 }, { order_id: 4 }];
    pos.request = async () => ({ status: "paid" });
    await pos.checkPayment();
    assert.equal(pos.modal, "receipt");
    assert.deepEqual(pos.pendingPayments, [{ order_id: 4 }]);
});

test("barcode scan selects an exact item and refuses ambiguous codes", () => {
    const pos = terminal();
    pos.products = [
        {
            id: 1,
            sku: "A",
            barcode: "899001",
            name: "Buku",
            type: "product",
            variants: [],
            addons: [],
        },
    ];
    let added = 0;
    pos.addItem = () => added++;
    pos.scan("899001");
    assert.equal(pos.selected.id, 1);
    assert.equal(added, 1);
    pos.products.push({ id: 2, sku: "899001", barcode: null });
    pos.scan("899001");
    assert.equal(added, 1);
    assert.match(pos.error, /beberapa item/);
});

test("cashier filters goods and services without changing the catalog", () => {
    const pos = terminal();
    pos.products = [
        { id: 1, name: "Buku", sku: "A", type: "product" },
        { id: 2, name: "Pasang", sku: "B", type: "service" },
    ];
    pos.itemType = "service";
    assert.deepEqual(
        pos.filtered.map((p) => p.id),
        [2],
    );
    pos.itemType = "all";
    assert.equal(pos.filtered.length, 2);
});
