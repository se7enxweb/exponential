---
title: Template compilation and cache
audience: designer, developer
category: templates
order: 770
summary: How templates become PHP and which caches hold them: TemplateCompile, compiled templates in var/<site>/cache/template/compiled, DevelopmentMode for checking file times, the template, template-override and template-block caches, and exp:eztc to precompile.
keywords: template compilation, compiled templates, TemplateCompile, DevelopmentMode, template cache, template-override cache, eztc, precompile templates, cache clear templates, TemplateCache
type: help
---
# Template compilation and cache

This page is for designers who want to know why a template change sometimes shows at once and sometimes not.

## Contents

1. [Compilation](#compilation)
2. [The template caches](#the-template-caches)
3. [Development settings](#development-settings)
4. [Precompiling](#precompiling)

## Compilation

With `site.ini [TemplateSettings] TemplateCompile=enabled` (default) each template is translated into PHP the first time
it is used and stored under `var/<site>/cache/template/compiled/`; later requests run the PHP. `TemplateOptimization`
(enabled) optimizes the generated code.

## The template caches

| Cache id | Holds | Clear after |
|---|---|---|
| `template` | compiled templates | template changes when file times are not checked |
| `template-override` | the override and template lookup tables | adding templates or override rules |
| `template-block` | the stored output of cache-blocks | changing parts inside cache-blocks |
| `content` | the view cache (rendered full views) | changing view templates |
| `design_base` | the list of design folders (with `DesignLocationCache=enabled`) | activating a design extension |

`php bin/php/ezcache.php --clear-id=<id>`; `--list-ids` prints them all. `[TemplateSettings] TemplateCache=enabled`
controls cache-blocks (disabled: never used).

## Development settings

`[TemplateSettings] DevelopmentMode=enabled` makes the template engine compare the times of compiled and source files,
so edited templates are recompiled without a cache clear (the comment in `settings/site.ini` warns that it adds file
access and is not for live sites). It also turns the packer to level 0 ([The packer](../theme-design/the-packer.md)).
Enable it on a development siteaccess only, and clear compiled templates when switching it.

## Precompiling

`./console exp:eztc` compiles the templates of a siteaccess into the template cache in advance (`--force` recompiles
all), so the first visitors after a deploy do not pay for compilation.

## Related pages

- [Caching for designers](../theme-design/caching-for-designers.md)
- [The packer cache and deploys](../theme-design/packer-cache-and-deploy.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
