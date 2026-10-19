---
title: Reorder or remove header menu items
audience: website-creator, admin, developer, editor
category: layouts
order: 140
summary: Change the order of the header menu or take an item out, in the administration (edit the lines) or with exp:ini rem and exp:ini clear, with the real diffs the console shows.
keywords: header menu, reorder menu, remove menu item, MainMenuID, exp:ini rem, exp:ini clear, menu order, menu.ini
type: help
---
# Reorder or remove header menu items

The header menu shows the node IDs of `menu.ini [SiteInfo] MainMenuID[]` in the order they are listed. Reordering
means reordering the lines; removing means deleting one line. Both need the template and content caches cleared
afterwards, as adding does ([Caches after a change](caches-after-a-change.md)).

## Contents

1. [In the administration](#in-the-administration)
2. [Remove one item by command line](#remove-one-item-by-command-line)
3. [Reorder by command line](#reorder-by-command-line)
4. [Take a page out of the menu without touching the settings](#take-a-page-out-of-the-menu-without-touching-the-settings)

## In the administration

Open **Setup > Ini settings**, `menu.ini`, siteaccess `site`, and press **Edit** on `MainMenuID[]`
([the walkthrough](header-menu-in-the-admin.md) has screenshots). In **Value**:

- to **reorder**, move the lines: the first `=<id>` line is the first item;
- to **remove**, delete the item's line;
- keep the **empty first line**: it is the reset that drops the theme extension's own list. Without it, the theme's
  five IDs would be added in front of yours.

Press **Save**, then clear **Template & content** in the Clear cache box.

## Remove one item by command line

`exp:ini rem` with the value removes one element from one file:

```text
$ ./console exp:ini rem menu.ini/SiteInfo/MainMenuID[] 185 siteaccess:site --dry-run
--- a/settings/siteaccess/site/menu.ini.append.php
+++ b/settings/siteaccess/site/menu.ini.append.php
@@ -10,7 +10,6 @@
 MainMenuID[]=164
 MainMenuID[]=26210
 MainMenuID[]=192
-MainMenuID[]=185
 NexusMainMenuID[]
 NexusMainMenuID[]=167
 NexusMainMenuID[]=168
Dry run: remove menu.ini/SiteInfo/MainMenuID[] in siteaccess:site, nothing written
```

Run it again without `--dry-run` to write it, then `./console exp:cache clear --tag=template,content`.

## Reorder by command line

`exp:ini add` always appends. To set a new order, empty the list in that file and add the IDs in the order you want.
`exp:ini clear` keeps the reset line and removes the file's values:

```text
$ ./console exp:ini clear menu.ini/SiteInfo/MainMenuID[] siteaccess:site --dry-run
--- a/settings/siteaccess/site/menu.ini.append.php
+++ b/settings/siteaccess/site/menu.ini.append.php
@@ -3,14 +3,6 @@
 [SiteInfo]
 RemoteID=media-o-site-info
 MainMenuID[]
-MainMenuID[]=92
-MainMenuID[]=130
-MainMenuID[]=144
-MainMenuID[]=163
-MainMenuID[]=164
-MainMenuID[]=26210
-MainMenuID[]=192
-MainMenuID[]=185
 NexusMainMenuID[]
 NexusMainMenuID[]=167
 NexusMainMenuID[]=168
Dry run: clear menu.ini/SiteInfo/MainMenuID[] in siteaccess:site, nothing written
```

Then, for example to put Showcase (192) first:

```bash
./console exp:ini clear menu.ini/SiteInfo/MainMenuID[] siteaccess:site
for id in 192 92 130 144 163 164 26210; do
    ./console exp:ini add menu.ini/SiteInfo/MainMenuID[] $id siteaccess:site --no-backup
done
./console exp:ini where menu.ini/SiteInfo/MainMenuID site
./console exp:cache clear --tag=template,content
```

The first command keeps its backup under `var/backup/ini/<timestamp>/`, so the old order can be restored from it.

## Take a page out of the menu without touching the settings

The template skips list entries whose node cannot be fetched, so moving a page to the trash also takes it out of the
menu. That is a side effect, not a menu tool: the ID stays in the setting, and a page restored from the trash may
get a new node ID, so check the list after a restore. To take an item out for good, remove its line.

## Related pages

- [Add a header menu item](add-a-header-menu-item.md)
- [Header menu by command line](header-menu-by-cli.md)
- [exp:ini for site builders](exp-ini-for-site-builders.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
