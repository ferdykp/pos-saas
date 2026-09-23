export const money = (value) =>
    "Rp " + new Intl.NumberFormat("id-ID", { maximumFractionDigits: 0 }).format(Number(value) || 0);
export function unitFor(product, variantId, addonIds = [], unitId = null, quantity = 1) {
    const variant = product.variants.find((v) => v.id === Number(variantId));
    const saleUnit = (product.units || []).find(u => u.id === Number(unitId));
    const tier = [...(product.price_tiers || [])].filter(t => Number(t.min_quantity) <= quantity).sort((a,b) => b.min_quantity-a.min_quantity)[0];
    const unit = saleUnit || variant || (tier ? {...product, ...tier} : product);
    const extras = product.addons.filter((a) => addonIds.includes(a.id));
    return {
        price:
            Number(unit.price) +
            extras.reduce((n, a) => n + Number(a.price), 0),
        discount: Number(unit.discount),
        stock: Number(variant ? variant.stock : product.stock),
        factor: Number(saleUnit?.factor || 1),
        unit_name: saleUnit?.name || product.base_unit || 'pcs',
        name: product.name + (variant ? " · " + variant.name : "") + (saleUnit ? " · " + saleUnit.name : ""),
    };
}
export function totals(lines, taxRate) {
    const subtotal = lines.reduce(
        (sum, line) => sum + Math.round(line.price * line.quantity),
        0,
    );
    const discount = lines.reduce(
        (sum, line) => sum + Math.round(line.discount * line.quantity),
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
