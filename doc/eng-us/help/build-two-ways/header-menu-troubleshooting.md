---
title: Header menu troubleshooting
audience: admin, website-creator, developer, designer
category: build-two-ways
order: 160
summary: The new header menu item does not appear - the causes found on a real installation, in the order to check them - compiled template, page cache, wrong file or scope, wrong siteaccess, a node that cannot be fetched, Velocity workers.
keywords: header menu not updating, menu item missing, cache, compiled template, ezini, exphttpcache, siteaccess, scope, exp:ini where, Velocity restart, troubleshooting
type: help
---
# Header menu troubleshooting

You added a node ID to `MainMenuID[]` and the header did not change. Check these in order; each one was seen while
writing these pages.

## Contents

1. [The compiled template still has the old list](#1-the-compiled-template-still-has-the-old-list)
2. [The page cache serves the old page](#2-the-page-cache-serves-the-old-page)
3. [The value went into a file that does not win](#3-the-value-went-into-a-file-that-does-not-win)
4. [You changed another siteaccess](#4-you-changed-another-siteaccess)
5. [The node cannot be fetched](#5-the-node-cannot-be-fetched)
6. [Velocity workers kept the old settings](#6-velocity-workers-kept-the-old-settings)
7. [A quick check list](#a-quick-check-list)

## 1. The compiled template still has the old list

The most common cause. `pagelayout/header/main_menu.tpl` reads the list with `ezini()`, and the template compiler
writes the values into the compiled file under `var/<site>/cache/template/compiled/` (a file named
`main_menu-<hash>.php`). Saving the setting clears the INI cache, not the compiled templates, so the old list keeps
being shown.

On the test copy, after saving `=192`: clearing the INI cache, the content view cache, the template block cache and the
page cache each left the menu unchanged; clearing the **template** cache brought the new item at once.

Fix: administration **Clear cache > Template & content > Clear**, or
`./console exp:cache clear --tag=template,content`. Once on the test copy (for the footer list), a page
rendered between the setting change and the clear kept the old list after the first clear; a second
`exp:cache clear --tag=template,content` showed the new item. Clear again before looking further.

## 2. The page cache serves the old page

Pages are kept in the HTTP page cache (cache id `exphttpcache`). A response header shows it:

```text
X-Exp-Cache: HIT
Age: 9
```

The tags `template` and `content` both include `exphttpcache`, so the fix in 1 clears it too. Alone:
`./console exp:cache httpcache clear`.

## 3. The value went into a file that does not win

`MainMenuID[]` is set in more than one file. On the Fit & Healthy site the theme extension sets five IDs, and
`settings/siteaccess/site/menu.ini.append.php` starts with `MainMenuID[]` (the reset) and sets its own. A value added
to the extension's file is dropped by that reset. Check with:

```text
./console exp:ini where menu.ini/SiteInfo/MainMenuID site
```

The administration shows the same under **Set in 2 files, 1 overridden**. Write to the file marked "elements in
effect" (scope `siteaccess:site`).

## 4. You changed another siteaccess

The site `site` and, for example, `bold` or `eng` each read their own `menu.ini`. On the settings page the
**Siteaccess** choice decides which one you edit. The siteaccess of a page is the one its address matches (host or
first path element); `./console exp:ini where ... <siteaccess>` shows each list.

## 5. The node cannot be fetched

`main_menu.tpl` skips an ID whose node cannot be fetched, without a message. Causes: a typo in the ID, an object ID
instead of a node ID (the admin shows both: `Node 192 · Object 190`), a page in the trash. Open
`/content/view/full/<id>` in the administration to check the ID.

## 6. Velocity workers kept the old settings

On a site served by Exponential Velocity, `exp:ini` prints: *Velocity workers keep the settings of their warm-up:
exp:velocity restart to apply it there.* After the cache clear, run `./console exp:velocity restart` on such a site.

## A quick check list

1. `./console exp:ini get menu.ini/SiteInfo/MainMenuID -s site` lists the new ID.
2. `./console exp:cache clear --tag=template,content` ends with `PASS`.
3. The page source has `menu-item-main_menu-location-id-<new id>`.
4. Velocity: restarted.

## Related pages

- [Caches after a change](caches-after-a-change.md)
- [Why doesn't my change appear?](why-doesnt-my-change-appear.md)
- [Add a header menu item](add-a-header-menu-item.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
