# Which siteaccess an order came from

This page is for shop administrators who run more than one store or site on one installation, and for developers
who build on the shop. Every order now records the siteaccess (store or site) it was placed in, and the order
lists show it and filter by it. Added 2026-10-28.

## Use it

1. Open **Store > Orders** (or **Archive**).
2. Choose a siteaccess in the **Siteaccess** filter above the list and click **Filter**. **All** lists every
   order; **Unknown** lists the orders placed before the siteaccess was recorded.
3. Click the **Siteaccess** heading of the order list to sort by it.

The filter is part of the address, so paging, sorting and a copied link keep it:

    /shop/orderlist/(sort)/<column>/(dir)/<asc|desc>/(siteaccess)/<name>
    /shop/archivelist/(siteaccess)/-

`-` stands for the orders without a siteaccess. The filter offers the siteaccesses of
`site.ini [SiteAccessSettings] AvailableSiteAccessList` and every siteaccess an order was placed in, so a
siteaccess removed since can still be chosen.

The order view shows the siteaccess, with the store's name (`site.ini [SiteSettings] SiteName` of that siteaccess)
when it differs. The customer order view lists it per order. The order view a customer sees (`design/standard`,
`design/base`) shows the store's name.

## What is recorded

- `eZBasket::createOrder()` writes the name of the current siteaccess into `ezorder.siteaccess`.
- `eZOrder::activate()` fills it in for an order that still has none, such as a basket started before the update.
- On the command line the order records the siteaccess the script runs in (its `--siteaccess`, or the configured
  default); through REST, the siteaccess REST runs in. When no siteaccess is known the column stays `''`.
- Orders placed before the update keep `''` and are shown as unknown. Which siteaccess they came from cannot be told
  afterwards, so there is no backfill script.

## For developers

- Column `ezorder.siteaccess varchar(255) NOT NULL default ''`, in every kernel schema, `share/db_schema.dba` and
  `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`. The MySQL and PostgreSQL statements add it only when
  it is missing, so they can run again.
- Before the update has run, `eZOrder::definition()` leaves the column out (`eZOrder::hasSiteaccessColumn()`), so
  the shop keeps working with the old table; the lists then show every order as unknown and offer no filter.
- Template attributes of an order: `siteaccess` (the column), `siteaccess_name` (the same, `''` before the update)
  and `store_name`.
- `eZOrder::active()`, `activeSorted()` and `activeCount()` take an optional last argument: `null` for every order,
  `''` for the orders without a siteaccess, or a name. `sortColumnsForList()` has `siteaccess`.
- `expOrderSiteaccess` (`kernel/classes/expordersiteaccess.php`) records the name and builds the filters.
- Tests: `tests/tests/kernel/classes/shop/expOrderSiteaccessTest.php` (no database).

## Related pages

- [Sort the order list by any column](order-list-sorting.md)
- [Store dashboard](store-dashboard.md)
- [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../help/partners/partners-start-here.md) and [How to apply](../../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
