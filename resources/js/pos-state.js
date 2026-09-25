export const quantityNumber = (value, maxDecimals = 3) =>
    new Intl.NumberFormat("id-ID", {
        minimumFractionDigits: 0,
        maximumFractionDigits: maxDecimals,
    }).format(Number(value) || 0);

export const money = (value) =>
    "Rp " +
    new Intl.NumberFormat("id-ID", {
        maximumFractionDigits: 0,
    }).format(Number(value) || 0);

export const quantity = (value) =>
    new Intl.NumberFormat("id-ID", {
        minimumFractionDigits: 0,
        maximumFractionDigits: 3,
    }).format(Number(value) || 0);

/**
 * Mengambil tier harga grosir yang aktif berdasarkan jumlah pembelian.
 */
export function activePriceTier(product, qty = 1) {
    const quantityValue = Number(qty) || 0;

    return (
        [...(product.price_tiers || [])]
            .filter(
                (tier) =>
                    Number(tier.min_quantity) > 0 &&
                    Number(tier.min_quantity) <= quantityValue,
            )
            .sort(
                (a, b) => Number(b.min_quantity) - Number(a.min_quantity),
            )[0] || null
    );
}

/**
 * Mengurutkan seluruh tier harga grosir.
 * Digunakan untuk kebutuhan display di UI.
 */
export function priceTiers(product) {
    return [...(product.price_tiers || [])]
        .filter(
            (tier) => Number(tier.min_quantity) > 0 && Number(tier.price) >= 0,
        )
        .sort((a, b) => Number(a.min_quantity) - Number(b.min_quantity));
}

/**
 * Mengecek apakah produk memiliki harga grosir.
 */
export function hasWholesalePrice(product) {
    return priceTiers(product).length > 0;
}

/**
 * Mengecek apakah produk memiliki pilihan satuan jual.
 */
export function hasSaleUnits(product) {
    return Array.isArray(product.units) && product.units.length > 0;
}

/**
 * Harga dasar produk setelah diskon.
 */
export function basePrice(product) {
    return Math.max(
        0,
        Number(product.price || 0) - Number(product.discount || 0),
    );
}

/**
 * Menghitung penghematan harga grosir dibanding harga dasar.
 */
export function wholesaleSaving(product, qty = 1) {
    const tier = activePriceTier(product, qty);

    if (!tier) {
        return 0;
    }

    const normal = basePrice(product);
    const tierPrice = Number(tier.price || 0) - Number(tier.discount || 0);

    return Math.max(0, Math.round((normal - tierPrice) * Number(qty || 0)));
}

export function unitFor(
    product,
    variantId,
    addonIds = [],
    unitId = null,
    quantity = 1,
) {
    const variant = product.variants.find((v) => v.id === Number(variantId));

    const saleUnit = (product.units || []).find((u) => u.id === Number(unitId));

    const tier = activePriceTier(product, quantity);

    /*
     * Prioritas:
     * 1. Satuan jual khusus
     * 2. Varian
     * 3. Harga grosir
     * 4. Harga dasar produk
     */
    const unit =
        saleUnit || variant || (tier ? { ...product, ...tier } : product);

    const extras = product.addons.filter((a) => addonIds.includes(a.id));

    return {
        price:
            Number(unit.price) +
            extras.reduce((total, addon) => total + Number(addon.price), 0),

        discount: Number(unit.discount || 0),

        stock: Number(variant ? variant.stock : product.stock),

        factor: Number(saleUnit?.factor || 1),

        unit_name: saleUnit?.name || product.base_unit || "pcs",

        name:
            product.name +
            (variant ? " · " + variant.name : "") +
            (saleUnit ? " · " + saleUnit.name : ""),
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

    return {
        subtotal,
        discount,
        tax,
        total: subtotal - discount + tax,
    };
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
