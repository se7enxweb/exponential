---
title: Settings scopes and the order they win in
audience: developer, designer, app-creator
category: extension-development
order: 20
summary: The places a settings file can live - default, extension, siteaccess, the per-site folders inside extensions, and the global override - the exact order they are read in, and how to choose the right one for a change.
keywords: settings order, scope, override, siteaccess, extension, sa-extension, override__group, placement, priority, which file wins, exp:ini where
type: help
---
# Settings scopes and the order they win in

Part 2, chapter 2. The previous chapter showed that many files make one value. This chapter names the places those
files live, the order the kernel reads them in, and how to pick the right place for a change. The facts come from
`eZINI::overrideDirs()` in `lib/ezutils/classes/ezini.php` and from the specification
[INI override directories and placements](../../../specifications/6.0/ini-override-placements.md).

## The places

| Scope | Directory | Read for | What belongs there |
|---|---|---|---|
| default | `settings/` | everything | the kernel's defaults; never edited |
| siteaccess extensions (`sa-extension`) | `extension/<ext>/settings/` of the extensions in `ActiveAccessExtensions[]` | the siteaccesses that switch them on | extensions active for one siteaccess only |
| siteaccess, inside extensions (`siteaccess`) | `extension/<ext>/settings/siteaccess/<sa>/`, `extension/<ext>/settings/override__<group>/`, `extension/<ext>/settings/override/` | the siteaccesses an extension owns | per-site settings kept inside an extension |
| siteaccess (`siteaccess`) | `settings/siteaccess/<sa>/` | one siteaccess | the settings of one site or of the administration |
| extension | `extension/<ext>/settings/` | every request, for active extensions | the defaults an extension ships |
| override | `settings/override/` | every request | this installation's own global settings |

## The order

After the default file, the kernel reads the override directories in this order, and **a later file wins**:

1. `extension/<ext>/settings/` of the extensions in `ActiveAccessExtensions[]` (`sa-extension`),
2. the extension siteaccess directories, then `settings/siteaccess/<sa>/` (both `siteaccess`),
3. `extension/<ext>/settings/` of each active extension (`extension`),
4. `settings/override/` (`override`).

The code is one line:

```php
return array_merge( $dirs['sa-extension'], $dirs['siteaccess'], $dirs['extension'], $dirs['override'] );
```

Inside one scope, a directory registered later with `eZINI::prependOverrideDir()` stands earlier in the list and so
has lower priority. For extensions this means: **an extension earlier in `ActiveExtensions[]` wins** over a later
one, which is why an installation's own settings extension is usually listed first.

> **Note**
> Notice that the `extension` scope is read **after** the `siteaccess` scope. A value an extension ships in
> `extension/<ext>/settings/` beats the same value in `settings/siteaccess/<sa>/`. When a site needs a different value
> than an extension's default, put it in `settings/override/` (which applies to every siteaccess), or remove the key
> from the extension's `settings/` file. The extension's own per-siteaccess folders do not help: they are in the
> `siteaccess` scope too, and the extension's `settings/` beats them as well.

## The per-site folders inside an extension

An extension can carry per-siteaccess settings, so that a whole site, with all its siteaccesses, is one folder:

| Directory | Applies to |
|---|---|
| `extension/<ext>/settings/siteaccess/<sa>/` | one siteaccess |
| `extension/<ext>/settings/override__<group>/` | every siteaccess whose name starts with `<group>__` (for example `shop__en`, `shop__de` for the group `shop`) |
| `extension/<ext>/settings/override/` | every siteaccess the extension owns |

These are registered only for a siteaccess that has its folder `extension/<ext>/settings/siteaccess/<sa>/`; that folder
is what makes the extension the owner. Details and an example are in
[Per-site settings inside extensions](../../../features/6.0/multi-site-ini-overrides.md).

## Placements: where did a value come from?

`exp:ini where` and the settings page name each file's **placement**:

| Placement | File |
|---|---|
| `default` | `settings/site.ini` |
| `override` | `settings/override/site.ini.append.php` |
| `extension:<ext>` | `extension/<ext>/settings/site.ini.append.php` |
| `siteaccess` | `settings/siteaccess/<sa>/site.ini.append.php` |
| `ext-siteaccess:<ext>` | `extension/<ext>/settings/siteaccess/<sa>/site.ini.append.php` |
| `ext-siteaccess-override:<ext>` | `extension/<ext>/settings/override/site.ini.append.php` |
| `ext-siteaccess-override__<group>:<ext>` | `extension/<ext>/settings/override__<group>/site.ini.append.php` |
| `injected` | set by code at run time |

Try it for a setting that many files touch:

```bash
./console exp:ini where design.ini/ExtensionSettings/DesignExtensions
./console exp:ini where site.ini/DesignSettings/SiteDesign -s <your siteaccess>
```

`-s <siteaccess>` shows the result as that siteaccess sees it.

## Choosing the place for a change

| You want to change | Put it in | Scope word for `exp:ini` |
|---|---|---|
| Something for the whole installation, that differs per server (database, mail server, paths) | `settings/override/` | `global` |
| One site's look or language | the siteaccess, or better your site extension's `settings/siteaccess/<sa>/` | `siteaccess:<sa>` or `extension:<ext>:siteaccess:<sa>` |
| What your own extension needs everywhere | `extension/<yours>/settings/` | `extension:<yours>` |
| The behavior of someone else's extension | `settings/override/` or your own extension listed before it | `global`, or `extension:<yours>` |

Two rules keep this manageable:

- **Server-specific and secret values** (database passwords, host names) go in `settings/override/`, which is never
  committed. Everything else can live in extensions under version control.
- **Never edit another extension's settings files.** They are replaced when that extension updates. Override them.

## Moving settings into an extension

`exp:ini` can move settings between scopes for you, keeping the value in effect:

```bash
./console exp:ini move site.ini/DesignSettings siteaccess:mysite extension:mysite_settings:siteaccess:mysite --dry-run
./console exp:ini move-all siteaccess:mysite extension:mysite_settings:siteaccess:mysite --create-extension --dry-run
```

`move-all` with `--create-extension` creates the target extension (`extension.xml`, `ezinfo.php`, `settings/`) if it
does not exist, and `--activate` switches it on. Remove `--dry-run` when the shown result is what you want. The next
chapters use this to build a site extension.

## Related pages

- Previous: [How INI settings work](how-ini-settings-work.md)
- Next: [Siteaccess settings](siteaccess-settings.md)
- [INI override directories and placements](../../../specifications/6.0/ini-override-placements.md)
- [Extension loading order](../../../features/6.0/extension-loading-order.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
