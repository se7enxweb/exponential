---
title: Override basics
audience: designer, developer
category: templates
order: 570
summary: How template overrides work: a rule in override.ini names a source template, the template that replaces it and the conditions; where rule files live (siteaccess, extension, extension siteaccess); how the kernel finds the replacement under override/templates; the visual template editor; and the caches.
keywords: override, override.ini, template override, Source, MatchFile, Subdir, Match, Priority, override rule, override/templates, template editor, override cache
featured: true
type: help
---
# Override basics

This page is for designers who want a different template for some content: articles, one special page, everything in
a section. An **override** is a rule: "when the template `Source` is used for content that matches these conditions,
use `MatchFile` instead".

## Contents

1. [A rule](#a-rule)
2. [Where rules live](#where-rules-live)
3. [How the replacement is found](#how-the-replacement-is-found)
4. [When several rules match](#when-several-rules-match)
5. [Managing overrides in the administration](#managing-overrides-in-the-administration)
6. [Caches](#caches)

## A rule

```ini
[mytheme_full_article]
Source=node/view/full.tpl
MatchFile=full/article.tpl
Subdir=templates
Match[class_identifier]=article
Priority=10
```

| Key | Meaning |
|---|---|
| section name | unique name of the rule; prefix it with your theme |
| `Source` | the template being replaced, a path under `templates/` |
| `MatchFile` | the replacement, a path under `override/templates/` |
| `Subdir` | `templates` (the folder kind; always this value in designs) |
| `Match[<key>]` | conditions; all must be true ([Override match keys](override-match-keys.md)); none means always |
| `Priority` | order of evaluation, lowest first; rules without one come last ([Override ordering](override-ordering.md)) |

The kernel's `settings/override.ini` shows the syntax as a commented example and holds no rules itself.

## Where rules live

Rules are read from `override.ini` with all its append files, for the current siteaccess:

- `settings/siteaccess/<siteaccess>/override.ini.append.php` (the siteaccess's own);
- an extension's `settings/override.ini.append.php` (every siteaccess where the extension is active);
- an extension's `settings/siteaccess/<siteaccess>/override.ini.append.php` (that siteaccess only; how the media and
  starter themes ship theirs).

Prefer the extension's siteaccess file for a theme: its rules then never touch the administration or other sites.

## How the replacement is found

When `Source` is requested, the kernel checks the rules for it in order; the first whose conditions all match wins. Its
`MatchFile` is searched under `override/templates/` in every design base of the cascade
(`eZTemplateDesignResource::overrideArray()` in `kernel/common/eztemplatedesignresource.php`). A `MatchFile` found in
no design is reported in the error log ("Custom match file: path ... not found in any resource").

## When several rules match

Only the first rule in order counts. Give specific rules (one node) a lower `Priority` than general ones (a class), so
they are checked first. See [Override ordering](override-ordering.md).

## Managing overrides in the administration

**Design > Template Editor** (`/visual/templatelist`) lists the templates of a siteaccess with their overrides, creates
new ones (written with `Priority=0`), reorders them by drag and drop and edits the files
([Template editor: create, order and edit overrides](../../../features/6.0/template-editor-overrides.md)). For a theme in
an extension, keep the rules in the extension's files, under version control.

## Caches

After adding a rule or an override template: `php bin/php/ezcache.php --clear-tag=ini` and
`--clear-id=template-override`. Then `--clear-id=content` for full views already cached.

## Related pages

- [Override match keys](override-match-keys.md)
- [Node templates by class](node-templates-by-class.md)
- [Tutorial: override a full view](../theme-design/tutorial-override-a-full-view.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
