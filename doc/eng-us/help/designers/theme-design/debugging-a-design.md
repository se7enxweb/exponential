---
title: Debugging a design
audience: designer, developer
category: theme-design
order: 170
summary: Find out which template, override and stylesheet produced a piece of a page, why a new file is ignored, and how to check template syntax, with the debug settings, template path comments, the template checker and the caches to clear.
keywords: debug design, debugging templates, which template, template path comments, ShowUsedTemplates, DebugOutput, template debug, eztemplatecheck, template syntax check, new template ignored, stale page, cache clear, error.log
type: help
---
# Debugging a design

This page is for designers who look at a page and ask "where does this come from?" or "why does my change not
show?". It lists the tools, in the order you usually need them.

## Contents

1. [First: which template drew this?](#first-which-template-drew-this)
2. [Which templates were used at all?](#which-templates-were-used-at-all)
3. [My new template or override is ignored](#my-new-template-or-override-is-ignored)
4. [My stylesheet change does not show](#my-stylesheet-change-does-not-show)
5. [Template syntax errors](#template-syntax-errors)
6. [Errors and warnings](#errors-and-warnings)
7. [The page shows old content](#the-page-shows-old-content)
8. [Quick reference](#quick-reference)

## First: which template drew this?

Turn on **template path comments** for a test siteaccess:

```ini
# settings/siteaccess/<test siteaccess>/site.ini.append.php
[TemplateSettings]
ShowTemplatePathComments=enabled
```

Every template's output is then wrapped in comments such as
`<!-- START design/media/templates/pagelayout/footer.tpl --> ... <!-- STOP ... -->`. Open the page source or the
browser's element inspector and read the comment around the markup you care about. It works with the template
compiler on or off. The setting injects markup into every template, so it must stay off in production and on any
siteaccess that serves JSON, XML or RSS (comment in `settings/site.ini`). Details:
[Template path comments](../../../features/6.0/template-path-comments.md).

## Which templates were used at all?

With the debug output on, a table of all templates used for the page is added:

```ini
[DebugSettings]
DebugOutput=enabled
# limit it to your own address
DebugByIP=enabled
DebugIPList[]=203.0.113.10

[TemplateSettings]
ShowUsedTemplates=enabled
```

`ShowUsedTemplates` needs `DebugOutput` (comment in `settings/site.ini`). Use your own address in `DebugIPList[]`;
the one above is a documentation example.

## My new template or override is ignored

Work through these, in order:

1. **The template override cache.** A new template file in a design, or a new rule in `override.ini`, is only seen
   after `php bin/php/ezcache.php --clear-id=template-override`. A page that suddenly renders with an empty content
   area right after adding templates has the same cause.
2. **The INI cache.** A new `override.ini` or `design.ini` line needs `--clear-tag=ini`.
3. **The path.** The file must be at the exact path the kernel asks for, inside `templates/` (or
   `override/templates/` for an override's `MatchFile`). A typo in `MatchFile` is written to the error log as
   "Custom match file: path '...' not found in any resource".
4. **The cascade.** A design higher in the list may have the same file. Template path comments show which copy won
   ([The design cascade](the-design-cascade.md)).
5. **The override order.** Another rule for the same `Source` may match first. Rules are tried by `Priority`
   (lowest first; no `Priority` sorts last) and the first match wins ([Override ordering](../templates/override-ordering.md)).
6. **The extension.** Is the design extension active for this siteaccess, and listed in `DesignExtensions[]`?

## My stylesheet change does not show

1. Is the file loaded at all? The packed file in the network panel is named after a hash of its parts; with
   `[TemplateSettings] DevelopmentMode=enabled` on a test siteaccess the packer serves each file on its own, so you see
   yours by name.
2. The admin pagelayout caches the link to the packed file inside a cache-block. After a CSS change in an admin design,
   clear `template-block` too (a deploy with `--packer` does it).
3. The browser may hold the old file: hard-reload. Do not add `?v=` to links ([No version queries on asset links](no-version-queries-on-asset-links.md)).

## Template syntax errors

The template checker parses templates without rendering them:

```bash
# every template of a siteaccess's designs
./console exp:eztemplatecheck -s mysite
# or one design directory
./console exp:eztemplatecheck extension/mytheme/design/mytheme/templates/
```

It prints "Template file valid" or the parse error for each file. Run it before every commit of a design. It checks
syntax only: an operator that does not exist (or is not active on the siteaccess) parses fine and fails only when
the page is rendered, so open the pages as well.

## Errors and warnings

Template errors and warnings (unregistered operators, unknown functions, missing include files) are shown in the
debug output; errors are also written to the error log (`site.ini [DebugSettings] AlwaysLog[]=error`), which Setup >
Log files shows. An unknown operator is the usual result of using an
operator from an extension that is not active on the siteaccess.

## The page shows old content

- **View cache.** Full views are cached (`site.ini [ContentSettings] ViewCaching=enabled`, cached view modes
  `CachedViewModes=full;sitemap;pdf`). Publishing content clears the right pages; a template change does not. Clear
  `--clear-id=content` after changing a view template.
- **Cache-blocks.** Parts in `{cache-block}` keep their output until their keys change or they expire; clear
  `--clear-id=template-block`.
- **Velocity.** On installations that run the Velocity application server, its response cache is separate:
  `./console exp:velocity cache clear`.

## Quick reference

| Symptom | First thing to try |
|---|---|
| Which file drew this? | `ShowTemplatePathComments=enabled` on a test siteaccess |
| New template ignored | `--clear-id=template-override` |
| New INI line ignored | `--clear-tag=ini` |
| Changed view template, old page | `--clear-id=content` |
| Changed part in a cache-block | `--clear-id=template-block` |
| CSS change missing in admin | deploy with `--packer`, then hard-reload |
| "Operator '...' is not registered" | extension of that operator not active here |
| Template does not parse | `./console exp:eztemplatecheck` |

## Related pages

- [Template debugging](../templates/template-debugging.md)
- [Template compilation and cache](../templates/template-compilation-and-cache.md)
- [Caching for designers](caching-for-designers.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
