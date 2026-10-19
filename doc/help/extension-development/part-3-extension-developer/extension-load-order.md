---
title: Extension load order
audience: developer, app-creator, admin
category: extension-development
order: 50
summary: Which extension wins when two provide the same setting or template: the written order of ActiveExtensions, how declared dependencies reorder it, extra extension folders, and how to see the real order.
keywords: load order, loading order, priority, ActiveExtensions, ExtensionOrdering, requires, extends, ActiveAccessExtensions, AdditionalExtensionDirectories, extension_src, which wins
type: help
---
# Extension load order

Part 3, chapter 5. When two extensions ship the same setting, the same template path or the same override, one of
them wins. Which one depends on the **load order**. This chapter explains how the order is made and how to control it.

## The rule

An extension **earlier** in the load order has the **higher** priority: its settings win, and its design folders are
searched first. So the extension that should win is listed first.

## Where the order comes from

1. **The written order**: `ActiveExtensions[]` in the order the settings files list them, followed by the
   siteaccess's `ActiveAccessExtensions[]`.
2. **Declared dependencies**: with `site.ini [ExtensionSettings] ExtensionOrdering=enabled` (the default) the kernel
   sorts the list by the `<dependencies>` of each `extension.xml` (`eZExtension::extensionOrdering()`):

   | Your `extension.xml` says | Effect |
   |---|---|
   | `<requires><extension name="x"/></requires>` | `x` loads before yours |
   | `<uses><extension name="x"/></uses>` | `x` loads before yours |
   | `<extends><extension name="x"/></extends>` | yours loads before `x`, and so wins over it |

   Extensions with no relation to each other keep their written order. A cycle of dependencies cannot be sorted; the
   written order is used then.

## Practical consequences

| You want | Do |
|---|---|
| Your site's settings extension to win over everything | list it first in `ActiveExtensions[]` |
| Your theme to override another theme's templates | declare `<extends>` on it in your `extension.xml`, or list yours earlier |
| A library extension available before yours | declare `<requires>` |

## See the real order

```bash
./console exp:ini get site.ini/ExtensionSettings/ActiveExtensions
```

prints the written order. **Setup > Extensions** shows, on each card, "Loads as number *n*" when the real position
differs from the written one, and lists dependency problems. From PHP, `eZExtension::activeExtensions()` returns the
order the kernel uses.

The design chain shows the effect on templates directly; the script in
[The design chain](../part-2-builder/the-design-chain.md) prints it.

## Extensions in more than one folder

Since July 2026 extensions can live in more than one folder:

```ini
[ExtensionSettings]
AdditionalExtensionDirectories[]
AdditionalExtensionDirectories[]=extension_src
```

`extension/` stays the first folder; the extra folders are searched after it. A typical split keeps installed
extensions in `extension/` and your own in `extension_src/`. Activation and load order do not change: an extension is
named by its folder name, wherever it lives. See
[Extensions in more than one folder](../../../features/6.0/additional-extension-directories.md).

## Related pages

- Previous: [Activating an extension](activating-an-extension.md)
- Next: [composer.json for an extension](composer-json-for-extensions.md)
- [Extension loading order and safe saving](../../../features/6.0/extension-loading-order.md)
- [extension.xml: metadata and dependencies](extension-xml.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
