---
title: Header menu by command line
audience: admin, developer, website-creator, designer
category: layouts
order: 120
summary: Add a page to the header menu with ./console exp:ini - see where the list is set, preview the change with --dry-run, write it, clear the caches. Real output from Exponential 6.0.15+.
keywords: exp:ini, console, command line, CLI, header menu, MainMenuID, menu.ini, dry run, exp:cache, clear cache, siteaccess scope
type: help
---
# Header menu by command line

Two commands add a page to the header menu: `exp:ini add` writes the node ID into `menu.ini`, `exp:cache clear` makes
the site show it. Run them in the installation's root directory. As root, add `--allow-root-user` to every command (the
output below was made that way; the option is left out of the prompts for readability).

## Contents

1. [See where the list is set](#1-see-where-the-list-is-set)
2. [Preview the change](#2-preview-the-change)
3. [Write it](#3-write-it)
4. [Clear the caches](#4-clear-the-caches)
5. [Check the result](#5-check-the-result)
6. [The same for every siteaccess](#6-the-same-for-every-siteaccess)

## 1. See where the list is set

`exp:ini where` lists every file that sets a setting, in the order they are read, and the value in effect:

```text
$ ./console exp:ini where menu.ini/SiteInfo/MainMenuID site
menu.ini/SiteInfo/MainMenuID (load order of siteaccess site)
 1. extension/sevenx_themes_media/settings/siteaccess/site/menu.ini.append.php scope extension:sevenx_themes_media:siteaccess:site
      MainMenuID[]=79
      MainMenuID[]=117
      MainMenuID[]=131
      MainMenuID[]=150
      MainMenuID[]=151
 2. settings/siteaccess/site/menu.ini.append.php                 scope siteaccess:site
      MainMenuID[]=92
      MainMenuID[]=130
      MainMenuID[]=144
      MainMenuID[]=163
      MainMenuID[]=164
      MainMenuID[]=26210
      MainMenuID[]=192
In effect:
      MainMenuID[]=92
      ...
```

The second file (scope `siteaccess:site`) is the one in effect, so that is the scope to write. (This listing already
has `192`, added in the administration on [the other page](header-menu-in-the-admin.md).)

## 2. Preview the change

`--dry-run` shows the change as a diff and writes nothing. The example adds **Contact** (node 185):

```text
$ ./console exp:ini add menu.ini/SiteInfo/MainMenuID[] 185 siteaccess:site --dry-run
--- a/settings/siteaccess/site/menu.ini.append.php
+++ b/settings/siteaccess/site/menu.ini.append.php
@@ -10,6 +10,7 @@
 MainMenuID[]=164
 MainMenuID[]=26210
 MainMenuID[]=192
+MainMenuID[]=185
 NexusMainMenuID[]
 NexusMainMenuID[]=167
 NexusMainMenuID[]=168
Dry run: add to menu.ini/SiteInfo/MainMenuID[] in siteaccess:site, nothing written
```

`add` appends: the new item becomes the last one. For another position, see
[Reorder or remove header menu items](reorder-or-remove-header-menu-items.md).

## 3. Write it

```text
$ ./console exp:ini add menu.ini/SiteInfo/MainMenuID[] 185 siteaccess:site
Add to menu.ini/SiteInfo/MainMenuID[] in siteaccess:site: settings/siteaccess/site/menu.ini.append.php
Backup: var/backup/ini/20261019-031440/settings/siteaccess/site/menu.ini.append.php
INI cache: cleared tag ini: 5 caches
Hint: PHP-FPM reads the change on its next request (ini cache cleared); Velocity workers keep the settings of their warm-up: exp:velocity restart to apply it there.
Done
```

The file is written line by line (comments and order stay), atomically, after a backup copy under `var/backup/ini/`.
The INI cache is cleared. As the hint says, a site served by Exponential Velocity applies the change after
`./console exp:velocity restart`.

## 4. Clear the caches

The header's menu template is compiled with the list of IDs in it, so clear the template and content caches:

```text
$ ./console exp:cache clear --tag=template,content
  exphttpcache         on  content,template           new HTTP cache generation: every stored page becomes stale
  template             on  template                   directory removed
  template-block       on  template,content           expiry timestamp global-template-block-cache set, directory moved aside
  template-override    on  template                   directory removed, in-memory override list emptied
  ...
  content              on  content                    expiry timestamp content-view-cache set, directory moved aside
  ...
PASS  cleared tag template, content: 17 caches
```

## 5. Check the result

The menu items carry the attribute `id="menu-item-main_menu-location-id-<id>"`, so a quick check is:

```text
$ curl -s https://www.example.com/ | grep -o 'menu-item-main_menu-location-id-[0-9]*' | tail -3
menu-item-main_menu-location-id-26210
menu-item-main_menu-location-id-192
menu-item-main_menu-location-id-185
```

(On this site the first five items show the IDs from `NexusMainMenuID[]` in that attribute instead, see
[How the header is built](how-the-header-is-built.md).)

## 6. The same for every siteaccess

Each site siteaccess can have its own list. To find all of them: `./console exp:ini scopes` lists the scopes the
installation has, and `exp:ini where menu.ini/SiteInfo/MainMenuID <siteaccess>` shows the list in effect for one of
them. Write each one with its own scope, `siteaccess:<name>`. The scope `global` (`settings/override/menu.ini.append.php`)
is read after every siteaccess file, so a value added there is added to the list of every siteaccess; keep menu lists
in the siteaccess files, where each site has its own.

## Related pages

- [Add a header menu item](add-a-header-menu-item.md)
- [exp:ini for site builders](exp-ini-for-site-builders.md)
- [Caches after a change](caches-after-a-change.md)
- [Measured steps and times](measured-steps-and-times.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
