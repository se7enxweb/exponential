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

## Open the store and the products as the customer saw them

The order view (`/shop/orderview/<id>`, designs `admin` and `admin4`) links the siteaccess the order was placed in:

- **Site URL**, below the siteaccess: the address its front page is served at, opening in a new tab. It is the
  address the siteaccess menu of the administration links (`ezpSiteAccessURL::root()`): a host, a URI, or both, as
  the siteaccess is matched, with the scheme and port of the administration's request.
- **View on site**, next to each product, after the link to the product in the administration: the product's node
  on that siteaccess, opening in a new tab. The address is the front page address and the node's URL alias in the
  siteaccess's languages (`[RegionalSettings] SiteLanguageList`), without its `[SiteAccessSettings] PathPrefix`. On
  a siteaccess with `PathPrefix=fit-healthy`, the node `fit-healthy/recipes/x` is linked as `<front page>/recipes/x`.
  A node outside the prefix is not served there and gets no link; `PathPrefixExclude[]` keeps a path whole.

An order without a siteaccess, or one whose siteaccess is no longer configured, shows neither link. The customer
order view and the order lists are unchanged.

## What is recorded

- `eZBasket::createOrder()` writes the name of the current siteaccess into `ezorder.siteaccess`.
- `eZOrder::activate()` fills it in for an order that still has none, such as a basket started before the update.
- On the command line the order records the siteaccess the script runs in (its `--siteaccess`, or the configured
  default); through REST, the siteaccess REST runs in. When no siteaccess is known the column stays `''`.
- Orders placed before the update keep `''` and are shown as unknown, until the backfill below infers their
  siteaccess where it can.

## Fill in the siteaccess of older orders

`bin/php/expbackfillordersiteaccess.php` infers the siteaccess of orders that have none from the content tree each
siteaccess serves: the node of its `site.ini [SiteAccessSettings] PathPrefix`, else its
`content.ini [NodeSettings] RootNode`. An order whose products (the main location of each item's object) all lie in
the tree of exactly one siteaccess gets that siteaccess. An order matching none or several, or with a product that is
no longer in the tree, stays unknown. An order that has a siteaccess is never changed.

Administrations (admin, editor and the like) never take orders, so they are never candidates. They are decided as
the siteaccess menu of the admin header decides them: the siteaccesses of `site.ini [AdminSiteAccessMenu]
AdminSiteAccessList[]`, or, when that list is empty, those with an administration design (`SiteDesign` or
`AdditionalSiteDesignList[]` starting with `admin`, so an editor that falls back to the admin designs counts) or a
`[SiteSettings] FrontSiteAccess`.

```bash
php bin/php/expbackfillordersiteaccess.php                        # dry run: lists every order and the counts
php bin/php/expbackfillordersiteaccess.php --prefer-deepest-root  # see below
php bin/php/expbackfillordersiteaccess.php --exclude=site_mobile,site_preview  # see below
php bin/php/expbackfillordersiteaccess.php --apply                # writes
```

Without `--apply` nothing is written: the siteaccesses considered (with the root each serves), those excluded with
`--exclude` and those left out as administrations are listed, then each order with the siteaccess it would get, or
why it stays unknown, followed by the counts and the excluded siteaccesses. Read the dry run first.

`--exclude=<siteaccess>[,<siteaccess>...]` names siteaccesses that are never chosen, such as a mobile twin or a
preview siteaccess that serves the same tree as the shop but never took an order. Give a comma list, repeat the
option (`--exclude=site_mobile --exclude=site_preview`), or both. An excluded siteaccess is dropped from an order's
candidates, so the next candidate can win: with a shop nested in a whole-tree site, excluding the shop gives its
orders to the site. An order that only excluded siteaccesses serve stays unknown, reported as
`stays unknown, only excluded siteaccesses serve its products: ...`, and is counted in the summary. A name that is
not in `site.ini [SiteAccessSettings] AvailableSiteAccessList[]` (a typo, a siteaccess since removed) is a warning
line, not an error; the run goes on.

`--prefer-deepest-root` is off by default and only for public sites with nested roots, such as a shop inside
another site's tree: of several sites that serve every product, the one whose root is deepest wins, when it is the
only one that deep; two sites with the same root (a shop and its mobile twin) still leave the order unknown.

## For developers

- Column `ezorder.siteaccess varchar(255) NOT NULL default ''`, in every kernel schema, `share/db_schema.dba` and
  `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`. The MySQL and PostgreSQL statements add it only when
  it is missing, so they can run again.
- Oracle (the `ezoracle` extension): `update/database/ezpublish/6.0/dbupdate-6.0.0-6.0.15-order-siteaccess.sql`
  adds a nullable `VARCHAR2(255)` in a PL/SQL block that checks `user_tab_columns` first, so it can run again.
  Oracle stores `''` as `NULL`, so the column cannot be `NOT NULL DEFAULT ''`; the kernel reads `NULL` as no
  siteaccess, and the Unknown filter matches both.
- Before the update has run, `eZOrder::definition()` leaves the column out (`eZOrder::hasSiteaccessColumn()`), so
  the shop keeps working with the old table; the lists then show every order as unknown and offer no filter.
- Template attributes of an order: `siteaccess` (the column), `siteaccess_name` (the same, `''` before the update),
  `store_name`, `siteaccess_url` (the front page address, `''` when there is none) and `siteaccess_product_urls`
  (content object id => the product's address on the siteaccess).
- `expOrderSiteaccess::frontURL()`, `nodeURL()` and `servedURL()` build those addresses;
  `expSubitemsURLColumn::servedPath()` takes the PathPrefix off (null for a path outside it).
- `eZOrder::active()`, `activeSorted()` and `activeCount()` take an optional last argument: `null` for every order,
  `''` for the orders without a siteaccess, or a name. `sortColumnsForList()` has `siteaccess`.
- `expOrderSiteaccess` (`kernel/classes/expordersiteaccess.php`) records the name and builds the filters.
- The commerce services of `expservices` (every order they return, such as `expOrderServices` `view`, `list` and
  `mine`) include `siteaccess`: the name, or `null` when it is not known.
- Tests: `tests/tests/kernel/classes/shop/expOrderSiteaccessTest.php` and `expOrderSiteaccessURLTest.php` (no
  database).

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
