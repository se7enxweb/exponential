---
title: "The design chain: where a template is found"
audience: developer, designer, app-creator
category: extension-development
order: 90
summary: How design:path/to/template.tpl is resolved: SiteDesign, AdditionalSiteDesignList, StandardDesign and DesignExtensions in each design, with the kernel code and a script that prints your chain.
keywords: design chain, design bases, SiteDesign, AdditionalSiteDesignList, StandardDesign, DesignExtensions, design resource, fallback, template lookup, ezdesign
type: help
---
# The design chain: where a template is found

Part 2, chapter 9. When a template asks for `design:node/view/full.tpl`, the kernel searches a list of folders, the
**design chain**, and uses the first file it finds. Knowing the chain explains why your template is used, or not. The
rules below are read from `eZTemplateDesignResource::findDesignBase()` in `kernel/common/eztemplatedesignresource.php`.

## The rule

1. Take the list of **designs**: `SiteDesign`, then every `AdditionalSiteDesignList[]` entry in order, then
   `StandardDesign` (all in `site.ini [DesignSettings]`, usually set per siteaccess). Duplicates are dropped.
2. For **each design** in that order:
   1. look in `extension/<ext>/design/<design>/` for every extension listed in
      `design.ini [ExtensionSettings] DesignExtensions[]`, in that order,
   2. then in the kernel's `design/<design>/`.
3. Every folder that exists is a **design base**. A template path is tried in each base, first match wins.

So the **design** order matters more than the extension order: any extension's `design/mysite/` beats every folder of
the next design, including the kernel's.

## See your chain

A small script prints it (save it in your extension, for example `extension/mysite_hello/scripts/designbases.php`):

```php
<?php
// Prints the design chain (the folders searched for a design: template), in search order.
// Run: php bin/php/ezexec.php extension/mysite_hello/scripts/designbases.php -s <siteaccess>
$cli = eZCLI::instance();
foreach ( eZTemplateDesignResource::allDesignBases() as $i => $base )
{
    $cli->output( ( $i + 1 ) . '. ' . $base );
}
```

On an installation whose public siteaccess uses `SiteDesign=media` with `ezwebin`, `standard` and `base` as additional
designs, the start of the output looked like this:

```text
1. extension/sevenx_alpha_settings/design/media
2. extension/sevenx_themes_media/design/media
3. extension/sevenx_authentication_2fa/design/media
4. design/media
5. extension/cjw_newsletter/design/ezwebin
6. extension/ezautosave/design/ezwebin
7. extension/ezwebin/design/ezwebin
8. design/ezwebin
9. extension/expui/design/standard
10. extension/ezjscore/design/standard
```

Each extension appears under each design it has a folder for. The full list is long; the top of it is what matters.

## Registering your extension's design

An extension's `design/` folder is searched only if the extension is in `DesignExtensions[]`. Every extension with
templates ships this in `settings/design.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[ExtensionSettings]
DesignExtensions[]=mysite_hello

*/ ?>
```

Check:

```bash
./console exp:ini where design.ini/ExtensionSettings/DesignExtensions
```

## Which design folder should I use?

| Your template is for | Put it in |
|---|---|
| One site's look | `extension/<yours>/design/<that site's design>/templates/` |
| Every site, any design (a module's own page, a datatype's view) | `extension/<yours>/design/standard/templates/` (the `StandardDesign`, at the end of every chain) |
| The administration | `extension/<yours>/design/admin/templates/` and, for the modern admin, `design/admin4/` (see [Admin pages in the admin4 style](../part-3-extension-developer/admin-pages-in-admin4-style.md)) |

Putting a module's templates in `standard` means they work under any site design; a site design can still replace
them by having the same path earlier in the chain.

## Two ways to replace a template

1. **Same path, earlier in the chain.** Put `node/view/full.tpl` in your design: it replaces the standard one for
   **all** content. Simple, but blunt.
2. **An override.** Keep the standard path and add a rule: "for articles use `full/article.tpl`". Precise, and the
   subject of the next chapter.

Prefer overrides for content templates; use the same-path method for templates that have no override conditions
(the pagelayout, a module's page).

## Files other than templates

The same chain finds stylesheets, scripts and images: `{'stylesheets/site.css'|ezdesign}` prints the address of the
first `stylesheets/site.css` in the chain, for example `"/design/base/stylesheets/site.css"`. The packer of ezjscore
uses it too (see [Theme assets, the packer and CSP](../part-6-theme-developers/theme-assets-the-packer-and-csp.md)).

## Caching

`site.ini [DesignSettings] DesignLocationCache=enabled` caches the list of design bases. After adding a design folder
or changing `DesignExtensions[]`, clear the caches:

```bash
php bin/php/ezcache.php --clear-tag=ini
php bin/php/ezcache.php --clear-id=design_base,template-override
php bin/php/ezcache.php --clear-tag=template
```

A **new** template file needs the template override cache (`template-override`) cleared, or the page may render with
an empty content area. `--clear-all` covers everything; `php bin/php/ezcache.php --list-ids` lists every cache id.

## Related pages

- Previous: [Templates for builders](templates-for-builders.md)
- Next: [Template overrides: the basics](template-overrides-basics.md)
- [Templates and design](../../../guides/templates-and-design.md)
- [Template path comments](../../../features/6.0/template-path-comments.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
