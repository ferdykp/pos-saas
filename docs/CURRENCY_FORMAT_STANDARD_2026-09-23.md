# GrowPOS Currency Display Standard

- Monetary values shown to users use Indonesian Rupiah format: `Rp 70.000`.
- Negative/positive adjustments keep the sign before the currency: `-Rp 5.000`, `+Rp 1.000`.
- POS JavaScript uses `Intl.NumberFormat('id-ID')` and the same `Rp ` prefix.
- Non-monetary values (quantity, stock, percentage, points) are not prefixed with Rupiah.
- Database/API numeric values remain numeric; this change is display formatting only.
