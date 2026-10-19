---
title: admininterface.ini [PaginationSettings]
audience: developer, admin
category: examples
summary: "The settings of [PaginationSettings] in admininterface.ini: DefaultItemsPerPage, ItemsPerPage, ItemsPerPageList_search_stats, ItemsPerPageList_shop_currencylist, ItemsPerPageList_shop_productsoverview, ItemsPerPageList_workflow_processlist, ItemsPerPageList_setup_session"
keywords: admininterface.ini, PaginationSettings, INI, setting, DefaultItemsPerPage, ItemsPerPage, ItemsPerPageList_search_stats, ItemsPerPageList_shop_currencylist, ItemsPerPageList_shop_productsoverview, ItemsPerPageList_workflow_processlist, ItemsPerPageList_setup_session
type: generated
generated: exp:help:examples
---
# admininterface.ini [PaginationSettings]

The block `[PaginationSettings]` of `settings/admininterface.ini`, with 7 setting(s).

Never edit `settings/admininterface.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/admininterface.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/admininterface.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/admininterface.ini.append.php` in an extension (read when the extension is active).

## DefaultItemsPerPage

> How long the lists in the administration interface are.  
>
> One block rather than a setting in each area's own ini file, because the  
> question is "where do I change how long these lists are" and it should have  
> one answer. The key is the module and view that draws the list, so a setting  
> can be found from the address of the page it governs.  
>
> Clear the caches after changing these:  
>   php bin/php/ezcache.php --clear-all  
> Used by any list below that names no size of its own.  

Default: `25`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[PaginationSettings]
DefaultItemsPerPage=25
*/ ?>
```

## ItemsPerPage

> shop/preferredcurrency is not listed: it is a drop-down for choosing one  
> currency, not a list of rows, and paging it would make it unusable.  

Default: a hash of 22: `setup/extensions=25`, `setup/cronjobs=25`, `section/list=25`, `workflow/grouplist=25`, `workflow/processlist=10`, `oauthadmin/list=25`, `oauthadmin/keys=25`, `package/list=25`, `pdf/list=25`, `content/translations=25`, `infocollector/overview=25`, `class/grouplist=25` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[PaginationSettings]
# an empty "ItemsPerPage[]" first replaces the shipped list; without it the items are added
ItemsPerPage[]
ItemsPerPage[setup/extensions]=25
ItemsPerPage[setup/cronjobs]=25
ItemsPerPage[section/list]=25
ItemsPerPage[workflow/grouplist]=25
ItemsPerPage[workflow/processlist]=10
ItemsPerPage[oauthadmin/list]=25
ItemsPerPage[oauthadmin/keys]=25
ItemsPerPage[package/list]=25
ItemsPerPage[pdf/list]=25
ItemsPerPage[content/translations]=25
ItemsPerPage[infocollector/overview]=25
ItemsPerPage[class/grouplist]=25
ItemsPerPage[class/classlist]=25
ItemsPerPage[shop/orderlist]=15
ItemsPerPage[shop/customerlist]=15
ItemsPerPage[shop/archivelist]=50
ItemsPerPage[shop/discountgroup]=25
ItemsPerPage[shop/status]=25
ItemsPerPage[shop/vattype]=25
ItemsPerPage[shop/vatrules]=25
# ... 2 more in the shipped file
*/ ?>
```

## ItemsPerPageList_search_stats

Default: a list of 3: `10`, `25`, `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[PaginationSettings]
# an empty "ItemsPerPageList_search_stats[]" first replaces the shipped list; without it the items are added
ItemsPerPageList_search_stats[]
ItemsPerPageList_search_stats[]=10
ItemsPerPageList_search_stats[]=25
ItemsPerPageList_search_stats[]=50
*/ ?>
```

## ItemsPerPageList_shop_currencylist

> Lists that offer an items per page selector take the sizes they offer. The  
> first is what somebody who has never chosen gets, and the list also bounds  
> the query: a size that is not on it cannot be asked for.  

Default: a list of 3: `10`, `25`, `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[PaginationSettings]
# an empty "ItemsPerPageList_shop_currencylist[]" first replaces the shipped list; without it the items are added
ItemsPerPageList_shop_currencylist[]
ItemsPerPageList_shop_currencylist[]=10
ItemsPerPageList_shop_currencylist[]=25
ItemsPerPageList_shop_currencylist[]=50
*/ ?>
```

## ItemsPerPageList_shop_productsoverview

Default: a list of 3: `10`, `25`, `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[PaginationSettings]
# an empty "ItemsPerPageList_shop_productsoverview[]" first replaces the shipped list; without it the items are added
ItemsPerPageList_shop_productsoverview[]
ItemsPerPageList_shop_productsoverview[]=10
ItemsPerPageList_shop_productsoverview[]=25
ItemsPerPageList_shop_productsoverview[]=50
*/ ?>
```

## ItemsPerPageList_workflow_processlist

Default: a list of 3: `10`, `25`, `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[PaginationSettings]
# an empty "ItemsPerPageList_workflow_processlist[]" first replaces the shipped list; without it the items are added
ItemsPerPageList_workflow_processlist[]
ItemsPerPageList_workflow_processlist[]=10
ItemsPerPageList_workflow_processlist[]=25
ItemsPerPageList_workflow_processlist[]=50
*/ ?>
```

## ItemsPerPageList_setup_session

> Setup > Sessions: the sessions (or, without a session table, the recent  
> sign-ins) shown at once. The first size is the default.  

Default: a list of 3: `50`, `25`, `100`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[PaginationSettings]
# an empty "ItemsPerPageList_setup_session[]" first replaces the shipped list; without it the items are added
ItemsPerPageList_setup_session[]
ItemsPerPageList_setup_session[]=50
ItemsPerPageList_setup_session[]=25
ItemsPerPageList_setup_session[]=100
*/ ?>
```

## Related

- [All blocks of admininterface.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/admininterface.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
