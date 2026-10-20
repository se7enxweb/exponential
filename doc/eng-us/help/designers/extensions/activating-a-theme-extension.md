---
title: "Activating a theme: ActiveExtensions and ActiveAccessExtensions"
audience: designer, admin
category: theme-extensions
order: 170
summary: Switch a theme extension on for the whole installation or for one siteaccess, in the browser (Setup > Extensions) or on the command line, and run the steps that make the change visible.
keywords: activate, ActiveExtensions, ActiveAccessExtensions, Setup Extensions, exp:ini add, autoloads, cache, switch on
type: help
---
# Activating a theme: ActiveExtensions and ActiveAccessExtensions

This page is for designers testing a theme on an installation, and for administrators installing one.

## Two switches

| Setting (`site.ini [ExtensionSettings]`) | Scope | Use it when |
|---|---|---|
| `ActiveExtensions[]` | The whole installation | The theme's settings are safe for every siteaccess (usual case) |
| `ActiveAccessExtensions[]` | One siteaccess (set in that siteaccess's settings) | The theme must not touch other siteaccesses at all |

## In the browser

1. Open **Setup > Extensions** (`/setup/extensions`).
2. Find the theme's card and press **Activate**.
3. Press **Review changes**: the page shows the resulting `ActiveExtensions[]` lines and the risks.
4. Press **Apply changes**. The page names the file it saved and the backup copy it kept.

![Setup, Extensions: the counters, search, sort and filter of the extensions, above the cards with Activate and Deactivate](../../images/designers/extensions-activate.webp)

The whole page: [The Extensions page](../../../guides/extensions-page.md).

## On the command line

```bash
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' mytheme global --dry-run --allow-root-user
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' mytheme global --allow-root-user
```

The first prints the change without writing; the second writes it to `settings/override/site.ini.append.php` with a
backup. For one siteaccess, use the scope `siteaccess:<name>` and `ActiveAccessExtensions[]`.

## Make the change visible

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all --allow-root-user
```

Expected from the first: `Found <n> classes, added <n> of them to the autoload array.` Then reload the PHP service
that serves the site (PHP-FPM or Exponential Velocity) so that its workers see new classes
([Deploying](../../../guides/deploying.md)).

## Check

- **Setup > Extensions** lists the theme as active.
- **Setup > About** shows its version.
- A page of the siteaccess that uses the design shows it.

## Related pages

- [Loading order and which theme wins](loading-order-for-themes.md)
- [Registering the design](registering-the-design.md)
- [Extensions guide, section 4](../../../guides/extensions.md#4-switch-it-on)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
