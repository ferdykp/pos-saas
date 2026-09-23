# GrowPOS — Retail & Launch Readiness

## Implemented in this revision

- Operational heartbeats for scheduler, queue worker, and successful database backup.
- Launch check verifies recent scheduler/worker/backup activity and built frontend/mail assets.
- Product base units, optional fractional quantities (3 decimal places), sale packages, and wholesale price tiers.
- Order-item snapshots of sale unit name and conversion factor so later catalog edits do not change historical returns.
- Fraction-aware stock movements, order returns, inventory adjustment, recipe usage, and POS stock validation.
- POS unit/package selector, fractional quantity input, tier repricing, and package-aware stock checks.
- Printable Code 128-B product barcode labels without a third-party runtime dependency.
- Public plan copy no longer advertises 24/7 chat, a personal account manager, or an open API when those services are not implemented.

## Before production launch

1. Run migrations on a verified backup/staging database first.
2. Run `php artisan launch:check` after scheduler and queue worker have been active for at least several minutes and after a successful backup.
3. Verify `php artisan schedule:work`/cron and `php artisan queue:work` are supervised by the production process manager.
4. Complete a real restore drill from the generated database backup; a backup is not proven until it can be restored.
5. Test cash and Midtrans flows using production-like credentials, including timeout, duplicate submit, late settlement, cancellation, and reconciliation.
6. Test the actual receipt printer, barcode scanner, and label printer models intended for merchants.
7. Pilot at least one retail/kelontong workflow with pcs → pak → dus conversion and one weighed item such as 1.5 kg.
8. Reconcile stock before/after sale and return for base units, packages, wholesale tiers, variants, and recipes.
9. Confirm HTTPS, production email delivery, logs, disk capacity, queue failures, scheduler health, and alert ownership.

## Validation completed in this workspace

- PHP syntax lint passes for application, database, and route PHP files.
- JavaScript test suite passes: 15/15 tests.
- `git diff --check` passes.
- Full Laravel test execution could not start in this container because its PHP runtime does not provide `mb_split`/mbstring.
- Vite build could not run from the uploaded `node_modules` because it contains platform-specific Rolldown optional bindings from another environment. Reinstall dependencies on the target machine with `npm ci` before building.
