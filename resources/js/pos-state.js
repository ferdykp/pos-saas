export const money = (value) =>
    "Rp" + new Intl.NumberFormat("id-ID").format(Number(value) || 0);
export function unitFor(product, variantId, addonIds = []) {
    const variant = product.variants.find((v) => v.id === Number(variantId));
    const unit = variant || product;
    const extras = product.addons.filter((a) => addonIds.includes(a.id));
    return {
        price:
            Number(unit.price) +
            extras.reduce((n, a) => n + Number(a.price), 0),
        discount: Number(unit.discount),
        stock: Number(unit.stock),
        name: product.name + (variant ? " · " + variant.name : ""),
    };
}
export function totals(lines, taxRate) {
    const subtotal = lines.reduce(
        (sum, line) => sum + line.price * line.quantity,
        0,
    );
    const discount = lines.reduce(
        (sum, line) => sum + line.discount * line.quantity,
        0,
    );
    const tax = Math.round(((subtotal - discount) * taxRate) / 100);
    return { subtotal, discount, tax, total: subtotal - discount + tax };
}
export function readState(storage, key, fallback) {
    const raw = storage.getItem(key);
    return raw ? JSON.parse(raw) : fallback;
}
export function queueOnce(queue, payload) {
    return queue.some(
        (item) => item.payload.checkout_key === payload.checkout_key,
    )
        ? queue
        : [...queue, { payload, error: null }];
}
