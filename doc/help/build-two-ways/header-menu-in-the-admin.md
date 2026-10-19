---
title: Header menu in the administration
audience: website-creator, admin, editor, designer
category: layouts
order: 110
summary: Step by step with screenshots - add a page to the header menu without code, through Setup > Ini settings, then clear the Template & content caches.
keywords: header menu, main menu, add menu item, Setup, Ini settings, menu.ini, MainMenuID, settings edit, clear cache, Template & content
type: help
---
# Header menu in the administration

You need an account that may use the settings pages of the administration (the Administrator role may). The example adds the page
**Showcase** to the header menu of the Fit & Healthy site.

## Contents

1. [Find the node ID](#1-find-the-node-id)
2. [Open the menu settings](#2-open-the-menu-settings)
3. [Edit MainMenuID](#3-edit-mainmenuid)
4. [Save](#4-save)
5. [Clear the caches](#5-clear-the-caches)
6. [Check the site](#6-check-the-site)

## 1. Find the node ID

Open the page in **Content structure**. The line under the buttons shows `Node 192`: that is the number the menu
needs (not the object number next to it).

![The administration's view of the Showcase page; the line under the buttons reads Node 192 and Object 190](images/hm-02-find-node-id.webp)

Other ways to find it are on [Find a node ID](find-a-node-id.md).

## 2. Open the menu settings

Go to **Setup > Ini settings** (section *System*). Choose **INI file** `menu.ini` and **Siteaccess** `site`, then press
**Show settings**.

![The Settings page with the INI file and Siteaccess choices and the Show settings button](images/hm-11-settings-pick-file.webp)

Find the block `[SiteInfo]` and the setting `MainMenuID[]`. It lists the menu items in their order. Open **Set in 2
files, 1 overridden** to see where the list comes from: the theme extension `sevenx_themes_media` brings a list, and the
installation's file `settings/siteaccess/site/menu.ini.append.php` empties it (`MainMenuID[]`) and sets its own. The
second file wins, so that is the one to change.

![The MainMenuID setting with six node IDs, and the two files that set it](images/hm-03-settings-mainmenuid.webp)

## 3. Edit MainMenuID

Press **Edit** on the `MainMenuID[]` row. The edit page shows the same chain again, then **New value**:

- **Type**: `Array` (leave it).
- **Value**: one line per item, `=` followed by the node ID. The first line is empty: that keeps the reset, so only
  these items are shown.

Add a line at the place where the item should appear. To make Showcase the last item, add `=192` at the end.

![The Value field with the lines =92, =130, =144, =163, =164, =26210 and the new line =192](images/hm-04-settings-edit-add-192.webp)

**Save it in** stays at *Siteaccess site only* (`settings/siteaccess/site/menu.ini.append.php`), the file that already
holds the list.

## 4. Save

Press **Save**. The page returns to the settings of `menu.ini` with the message *Saved: [SiteInfo] MainMenuID in
settings/siteaccess/site/menu.ini.append.php* and marks the changed row. The list now has seven items.

![The MainMenuID row after saving: list of 7, the last one 192](images/hm-05-settings-saved.webp)

Saving clears the INI caches. It does **not** clear the compiled templates, and the header's menu template is compiled
with the list in it.

## 5. Clear the caches

In the **Clear cache** box on the right, choose **Template & content** and press **Clear**. That clears the compiled
templates, the template blocks, the content view cache and the page (HTTP) cache together.

![The Clear cache box of the administration with its choice list and the Clear button](images/hm-07-clear-cache-panel.webp)

The choices of that box are: All caches, Content, Content - node, Content - subtree, Template, Template & content, Ini
settings and Static.

## 6. Check the site

Reload the site. The header has the new item, with the page's name as its text.

![The header after the change: Fitness, Healthy eating, Recipes, Running, Video, Tour, Showcase](images/hm-06-header-after.webp)

Nothing appears? See [Header menu troubleshooting](header-menu-troubleshooting.md).

## Related pages

- [Add a header menu item](add-a-header-menu-item.md)
- [Header menu by command line](header-menu-by-cli.md)
- [Ini settings in the administration](ini-settings-in-the-admin.md)
- [Caches after a change](caches-after-a-change.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
