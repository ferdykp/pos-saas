import { money, unitFor, totals, readState, queueOnce } from "./pos-state.js";

export default () => ({
    pendingPayments: [],
    config: null,
    products: [],
    cart: [],
    favorites: [],
    drafts: [],
    queue: [],
    search: "",
    category: "all",
    itemType: "all",
    orderType: "takeaway",
    table: "",
    customer: "",
    orderNote: "",
    modal: "",
    selected: null,
    variant: "",
    addonIds: [],
    itemNote: "",
    cash: "",
    method: "cash",
    busy: false,
    syncing: false,
    online: navigator.onLine,
    notice: "",
    error: "",
    receipt: null,
    payment: null,
    shift: null,
    shiftCash: "",
    shiftSummary: null,
    key: "",
    storageReady: true,
    init() {
        this.config = JSON.parse(
            document.getElementById("grow-pos-data").textContent,
        );
        this.products = this.config.products;
        this.pendingPayments = this.config.pendingPayments || [];
        this.shift = this.config.shift;
        this.key = `growpos:${this.config.tenant}:${this.config.user}:v1`;
        try {
            const state = readState(localStorage, this.key, {});
            this.cart = state.cart || [];
            this.favorites = state.favorites || [];
            this.drafts = state.drafts || [];
            this.queue = state.queue || [];
            this.orderType = state.orderType || "takeaway";
            this.table = state.table || "";
            this.customer = state.customer || "";
            this.orderNote = state.orderNote || "";
            this.persist();
        } catch {
            this.storageReady = false;
            this.error =
                "Penyimpanan perangkat tidak tersedia. Aktifkan penyimpanan browser sebelum menerima transaksi.";
        }
        this.$watch("cart", () => this.persist());
        this.$watch("modal", (value) => {
            document.body.style.overflow = value ? "hidden" : "";
            if (value)
                this.$nextTick(() => {
                    const panel = document.querySelector(".gp-dialog-panel");
                    const focusable = [
                        ...panel.querySelectorAll(
                            "input,select,button,a[href]",
                        ),
                    ].filter((el) => el.offsetParent !== null && !el.disabled);
                    focusable[0]?.focus();
                });
        });
        ["orderType", "table", "customer", "orderNote"].forEach((field) =>
            this.$watch(field, () => this.persist()),
        );
        window.addEventListener("online", () => {
            this.online = true;
            this.syncQueue();
        });
        window.addEventListener("offline", () => {
            this.online = false;
        });
        this.syncQueue();
    },
    money,
    get filtered() {
        const q = this.search.toLowerCase().trim();
        return this.products.filter(
            (p) =>
                (this.itemType === "all" || p.type === this.itemType) &&
                (this.category === "all" ||
                    (this.category === "favorites" &&
                        this.favorites.includes(p.id)) ||
                    Number(this.category) === p.category_id) &&
                (!q ||
                    `${p.name} ${p.sku} ${p.barcode || ""}`
                        .toLowerCase()
                        .includes(q)),
        );
    },
    scan(code) {
        const matches = this.products.filter(
            (p) => p.sku === code.trim() || p.barcode === code.trim(),
        );
        if (matches.length !== 1) {
            this.error = matches.length
                ? "Kode cocok dengan beberapa item. Pilih item dari katalog."
                : "Kode tidak ditemukan di katalog aktif.";
            return;
        }
        this.choose(matches[0]);
        if (!matches[0].variants.length && !matches[0].addons.length)
            this.addItem();
        this.search = "";
    },
    get amounts() {
        return totals(this.cart, this.config?.taxRate || 0);
    },
    get itemCount() {
        return this.cart.reduce((n, i) => n + i.quantity, 0);
    },
    get selectedUnit() {
        return this.selected
            ? unitFor(this.selected, this.variant, this.addonIds)
            : { price: 0, discount: 0 };
    },
    persist() {
        if (!this.key || !this.storageReady) return false;
        try {
            localStorage.setItem(
                this.key,
                JSON.stringify({
                    cart: this.cart,
                    favorites: this.favorites,
                    drafts: this.drafts,
                    queue: this.queue,
                    orderType: this.orderType,
                    table: this.table,
                    customer: this.customer,
                    orderNote: this.orderNote,
                }),
            );
            return true;
        } catch {
            this.storageReady = false;
            this.error =
                "Penyimpanan perangkat penuh. Antrean belum tersimpan; jangan tutup halaman.";
            return false;
        }
    },
    favorite(id) {
        this.favorites = this.favorites.includes(id)
            ? this.favorites.filter((v) => v !== id)
            : [...this.favorites, id];
        this.persist();
    },
    choose(product) {
        this.selected = product;
        this.variant = "";
        this.addonIds = [];
        this.itemNote = "";
        this.modal = "item";
        this.error = "";
    },
    addItem() {
        const p = this.selected,
            unit = this.selectedUnit;
        const variantId = this.variant ? Number(this.variant) : null;
        const existingQty = this.cart
            .filter((i) => i.id === p.id && i.variant_id === variantId)
            .reduce((n, i) => n + i.quantity, 0);
        if (p.tracked && existingQty + 1 > unit.stock) {
            this.error = "Stok produk tidak mencukupi.";
            return;
        }
        const signature = JSON.stringify([
            p.id,
            variantId,
            [...this.addonIds].sort(),
            this.itemNote.trim(),
        ]);
        const existing = this.cart.find((i) => i.key === signature);
        if (existing) existing.quantity++;
        else
            this.cart.push({
                key: signature,
                id: p.id,
                variant_id: variantId,
                addon_ids: [...this.addonIds],
                note: this.itemNote.trim(),
                name: unit.name,
                price: unit.price,
                discount: unit.discount,
                stock: unit.stock,
                tracked: p.tracked,
                addons: p.addons
                    .filter((a) => this.addonIds.includes(a.id))
                    .map((a) => a.name),
                quantity: 1,
            });
        this.modal = "";
        this.error = "";
        this.persist();
    },
    quantity(key, delta) {
        const item = this.cart.find((i) => i.key === key);
        if (!item) return;
        const quantity = this.cart
            .filter((i) => i.id === item.id && i.variant_id === item.variant_id)
            .reduce((n, i) => n + i.quantity, 0);
        if (delta > 0 && item.tracked && quantity + delta > item.stock) {
            this.error = "Stok produk tidak mencukupi.";
            return;
        }
        item.quantity += delta;
        this.cart = this.cart.filter((i) => i.quantity > 0);
        this.persist();
    },
    hold() {
        if (!this.cart.length) return;
        if (!this.storageReady) {
            this.error = "Pesanan belum bisa disimpan di perangkat.";
            return;
        }
        this.drafts.push({
            id: crypto.randomUUID(),
            name: this.table
                ? "Meja " + this.table
                : "Pesanan " +
                  new Date().toLocaleTimeString("id-ID", {
                      hour: "2-digit",
                      minute: "2-digit",
                  }),
            cart: JSON.parse(JSON.stringify(this.cart)),
            orderType: this.orderType,
            table: this.table,
            customer: this.customer,
            note: this.orderNote,
        });
        if (this.persist()) {
            this.clearCart();
            this.notice =
                "Pesanan tersimpan di perangkat. Stok belum dipotong.";
        }
    },
    resume(id) {
        if (this.cart.length) {
            this.error = "Simpan pesanan saat ini sebelum membuka draft lain.";
            return;
        }
        const d = this.drafts.find((v) => v.id === id);
        if (!d) return;
        this.cart = d.cart;
        this.orderType = d.orderType;
        this.table = d.table;
        this.customer = d.customer;
        this.orderNote = d.note;
        this.drafts = this.drafts.filter((v) => v.id !== id);
        this.modal = "";
        this.persist();
    },
    clearCart() {
        this.cart = [];
        this.table = "";
        this.customer = "";
        this.orderNote = "";
        this.cash = "";
        this.persist();
    },
    pay() {
        this.error = "";
        if (!this.shift) {
            this.modal = "openShift";
            return;
        }
        if (!this.cart.length) {
            this.error = "Pilih produk atau layanan terlebih dahulu.";
            return;
        }
        this.cash = "";
        this.method = "cash";
        this.modal = "pay";
    },
    async request(url, method = "GET", body = null) {
        const response = await fetch(url, {
            method,
            credentials: "same-origin",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": this.config.csrf,
            },
            body: body ? JSON.stringify(body) : undefined,
        });
        let data;
        try {
            data = await response.json();
        } catch {
            throw new Error(
                "Sesi atau koneksi terputus. Muat ulang setelah menyimpan antrean.",
            );
        }
        if (!response.ok) {
            const message = data.errors
                ? Object.values(data.errors).flat().join(" ")
                : data.message || "Permintaan gagal.";
            const e = new Error(message);
            e.status = response.status;
            throw e;
        }
        return data;
    },
    async submit() {
        if (this.busy) return;
        this.error = "";
        const total = this.amounts.total;
        if (this.method === "cash" && Number(this.cash) < total) {
            this.error = "Uang tunai kurang.";
            return;
        }
        if (this.method === "midtrans" && !navigator.onLine) {
            this.error = "QRIS membutuhkan koneksi internet.";
            return;
        }
        if (!this.storageReady) {
            this.error =
                "Aktifkan penyimpanan browser agar transaksi dapat disimpan sebelum dikirim.";
            return;
        }
        const payload = {
            checkout_key: crypto.randomUUID(),
            shift_id: this.shift.id,
            sold_at: new Date().toISOString(),
            payment_method: this.method,
            payment_status: "paid",
            paid_amount: this.method === "cash" ? Number(this.cash) : 0,
            grand_total: total,
            order_type: this.orderType,
            table_number: this.table || null,
            customer_id: this.customer ? Number(this.customer) : null,
            note: this.orderNote || null,
            items: this.cart.map((i) => ({
                id: i.id,
                variant_id: i.variant_id,
                addon_ids: i.addon_ids,
                note: i.note || null,
                quantity: i.quantity,
            })),
        };
        this.queue = queueOnce(this.queue, payload);
        if (!this.persist()) return;
        this.busy = true;
        if (!navigator.onLine) {
            this.clearCart();
            this.modal = "queue";
            this.notice =
                "Transaksi tunai tersimpan di perangkat, belum masuk laporan server.";
            this.busy = false;
            return;
        }
        try {
            const result = await this.request(
                this.config.checkout,
                "POST",
                payload,
            );
            this.queue = this.queue.filter(
                (i) => i.payload.checkout_key !== payload.checkout_key,
            );
            this.clearCart();
            this.persist();
            this.showResult(result);
        } catch (e) {
            const pending = this.queue.find(
                (i) => i.payload.checkout_key === payload.checkout_key,
            );
            if (pending) pending.error = e.message;
            this.clearCart();
            this.persist();
            this.modal = "queue";
            this.error =
                e.message +
                " Transaksi tetap disimpan dalam antrean. Jangan menerima pembayaran ulang.";
        } finally {
            this.busy = false;
        }
    },
    async syncQueue() {
        if (
            this.syncing ||
            this.busy ||
            !navigator.onLine ||
            !this.queue.length
        )
            return;
        this.syncing = true;
        try {
            for (const entry of [...this.queue]) {
                // Digital retries need explicit attention; do not initiate QR charges in the background.
                if (entry.payload.payment_method !== "cash") continue;
                try {
                    await this.request(
                        this.config.checkout,
                        "POST",
                        entry.payload,
                    );
                    this.queue = this.queue.filter(
                        (i) =>
                            i.payload.checkout_key !==
                            entry.payload.checkout_key,
                    );
                    this.persist();
                } catch (e) {
                    entry.error = e.message;
                    this.persist();
                    if (!e.status || e.status >= 500) break;
                }
            }
            if (!this.queue.length)
                this.notice = "Semua transaksi sudah tersinkron ke server.";
        } finally {
            this.syncing = false;
        }
    },
    async retry(entry) {
        if (this.busy || this.syncing) return;
        this.busy = true;
        this.error = "";
        try {
            const result = await this.request(
                this.config.checkout,
                "POST",
                entry.payload,
            );
            this.queue = this.queue.filter(
                (i) => i.payload.checkout_key !== entry.payload.checkout_key,
            );
            this.persist();
            this.showResult(result);
        } catch (e) {
            entry.error = e.message;
            this.persist();
        } finally {
            this.busy = false;
        }
    },
    showResult(result) {
        if (result.order_status === "cancelled") {
            this.modal = "";
            this.notice = "Pembayaran sudah dibatalkan.";
            return;
        }
        if (result.qr_url && result.payment_status !== "paid") {
            this.payment = result;
            if (
                !this.pendingPayments.some(
                    (p) => p.order_id === result.order_id,
                )
            )
                this.pendingPayments.push(result);
            this.modal = "qris";
        } else {
            this.receipt = result;
            this.modal = "receipt";
        }
    },
    trapFocus(event) {
        const nodes = [
            ...document.querySelectorAll(
                ".gp-dialog-panel button,.gp-dialog-panel a[href],.gp-dialog-panel input,.gp-dialog-panel select",
            ),
        ].filter((el) => el.offsetParent !== null && !el.disabled);
        if (!nodes.length) return;
        const index = nodes.indexOf(document.activeElement);
        const next = event.shiftKey
            ? index <= 0
                ? nodes.length - 1
                : index - 1
            : (index + 1) % nodes.length;
        event.preventDefault();
        nodes[next].focus();
    },
    backupQueue() {
        const blob = new Blob([JSON.stringify(this.queue, null, 2)], {
            type: "application/json",
        });
        const a = document.createElement("a");
        a.href = URL.createObjectURL(blob);
        a.download = "growpos-antrean-" + this.config.tenant + ".json";
        a.click();
        URL.revokeObjectURL(a.href);
    },
    async checkPayment() {
        if (this.busy || !this.payment) return;
        this.error = "";
        this.busy = true;
        try {
            const result = await this.request(
                "/orders/" + this.payment.order_id + "/check-status",
            );
            if (["paid", "cancelled"].includes(result.status))
                this.pendingPayments = this.pendingPayments.filter(
                    (p) => p.order_id !== this.payment.order_id,
                );
            if (result.status === "paid") {
                this.receipt = this.payment;
                this.modal = "receipt";
                this.notice = "Pembayaran QRIS terkonfirmasi.";
            } else if (result.status === "cancelled") {
                this.modal = "";
                this.notice = "Pembayaran batal. Stok telah dipulihkan.";
            } else this.notice = "Pembayaran masih menunggu konfirmasi.";
        } catch (e) {
            this.error = e.message;
        } finally {
            this.busy = false;
        }
    },
    async openShift() {
        this.error = "";
        this.busy = true;
        try {
            await this.request("/shifts/open", "POST", {
                cash_start: Number(this.shiftCash),
            });
            this.shift = (await this.request("/shifts/current")).shift;
            this.modal = "";
            this.notice = "Shift dibuka. Selamat berjualan!";
        } catch (e) {
            this.error = e.message;
        } finally {
            this.busy = false;
        }
    },
    async prepareClose() {
        if (this.queue.length) {
            this.modal = "queue";
            this.error =
                "Sinkronkan antrean perangkat ini sebelum menutup shift.";
            return;
        }
        try {
            this.shiftSummary = await this.request("/shifts/summary");
            this.shiftCash = "";
            this.modal = "closeShift";
        } catch (e) {
            this.error = e.message;
        }
    },
    async closeShift() {
        if (this.queue.length) return;
        this.error = "";
        this.busy = true;
        try {
            await this.request("/shifts/close", "POST", {
                cash_actual: Number(this.shiftCash),
            });
            this.shift = null;
            this.modal = "";
            this.notice = "Shift ditutup. Terima kasih untuk hari ini.";
        } catch (e) {
            this.error = e.message;
        } finally {
            this.busy = false;
        }
    },
});
