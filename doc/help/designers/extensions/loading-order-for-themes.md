---
title: "Loading order and which theme wins"
audience: designer, admin
category: theme-extensions
order: 180
summary: When two extensions provide the same template or setting, the loading order decides which one wins; how the order is set, how ExtensionOrdering and extension.xml dependencies change it, and how to check it.
keywords: loading order, priority, ActiveExtensions order, ExtensionOrdering, extends, requires, uses, template conflict, which theme wins
type: help
---
# Loading order and which theme wins

This page is for designers whose theme overrides templates of another extension (for example a base theme), and
for administrators who combine several themes.

## The rule

The order of the active extensions is the order of priority: **an extension loaded earlier wins over a later one**
for settings and for design templates.

## What decides the order

1. The order of the `ActiveExtensions[]` lines (and `ActiveAccessExtensions[]` for a siteaccess).
2. With `site.ini [ExtensionSettings] ExtensionOrdering=enabled` (the default in `settings/site.ini`), the
   dependencies declared in each active extension's `extension.xml` reorder that list first:

   | Declared in your `extension.xml` | Effect on loading |
   |---|---|
   | `<extends><extension name="base_theme" /></extends>` | Your theme loads **before** `base_theme`, so it wins |
   | `<requires>` or `<uses>` | Your theme loads **after** the named extension |

   See [Declaring dependencies in extension.xml](dependencies-in-extension-xml.md). The comment in `site.ini` lists
   the known limits: the ordering does not fix order issues across `ActiveExtensions` and `ActiveAccessExtensions`,
   and it still loads an extension whose dependency is not active.

## Step by step: make your theme win over a base theme

1. In your `extension.xml` add:

   ```xml
   <dependencies>
       <extends>
           <extension name="base_theme" />
       </extends>
   </dependencies>
   ```

2. Clear the INI cache: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.
3. Open **Setup > Extensions**: each card shows where the extension really loads.
4. Open a page that both themes have a template for. With template debugging on, the path comment names your file.

## When you cannot declare it

If you do not control the other extension or the order still is wrong, move your theme above the other one in the
list on **Setup > Extensions** (the order is changed in the list, reviewed, then written).

## Related pages

- [Setup > Extensions: loading order and safe saving](../../../features/6.0/extension-loading-order.md)
- The design cascade
- Override ordering
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
