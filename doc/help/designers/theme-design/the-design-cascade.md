---
title: The design cascade
audience: designer, developer
category: theme-design
order: 30
summary: The exact order in which Exponential searches designs and extensions for a template, stylesheet or image, with a worked example from this installation and the caches that hold the result.
keywords: design cascade, fallback, design fallback, design bases, search order, SiteDesign, AdditionalSiteDesignList, StandardDesign, DesignExtensions, DesignLocationCache, design_base cache, which template wins
featured: true
type: help
---
# The design cascade

This page is for designers who want to know exactly which file wins when the same template or stylesheet exists in
several places. It answers: in what order the kernel searches, how extensions fit in, how to predict the winner, and
which caches remember the result.

## Contents

1. [The rule in one sentence](#the-rule-in-one-sentence)
2. [Step 1: the list of designs](#step-1-the-list-of-designs)
3. [Step 2: each design in each extension](#step-2-each-design-in-each-extension)
4. [A worked example](#a-worked-example)
5. [What the cascade is used for](#what-the-cascade-is-used-for)
6. [Overrides come before the cascade](#overrides-come-before-the-cascade)
7. [The caches of the cascade](#the-caches-of-the-cascade)
8. [Rules of thumb](#rules-of-thumb)

## The rule in one sentence

The kernel builds an ordered list of **design bases** (folders) and takes a file from the **first** base that has
it. The code is `eZTemplateDesignResource::findDesignBase()` in `kernel/common/eztemplatedesignresource.php`.

## Step 1: the list of designs

The design names come from `site.ini [DesignSettings]` of the current siteaccess:

1. `SiteDesign`,
2. every entry of `AdditionalSiteDesignList[]`, in order,
3. `StandardDesign` (default `standard`).

Duplicates are removed (`array_unique`), so listing `standard` in `AdditionalSiteDesignList[]` as well does no harm;
its first position counts.

## Step 2: each design in each extension

For **each design name** in that list, the kernel adds:

1. `extension/<extension>/design/<design>` for every extension in `design.ini [ExtensionSettings]
   DesignExtensions[]`, in the order of that list, if the folder exists;
2. then the kernel's own `design/<design>`, if it exists.

So the extensions are searched before the kernel **within each design name**, and a design name higher in the list
always beats a lower one, wherever it lives.

`DesignExtensions[]` is filled by the extensions themselves: each theme extension has a
`settings/design.ini.append.php` with `DesignExtensions[]=<its own name>`. Its position in the list follows the
order in which extensions are loaded (see [Extension loading order](../../../features/6.0/extension-loading-order.md)).

## A worked example

The siteaccess `site` of this installation sets `SiteDesign=media` and `AdditionalSiteDesignList[]` `ezwebin`,
`standard`, `base`. With the theme extensions active, the bases for a lookup look like this (only folders that exist
are kept):

| # | Base | Why |
|---|---|---|
| 1 | `extension/sevenx_themes_media/design/media` | design `media`, extension copy |
| 2 | `extension/ezwebin/design/ezwebin` | design `ezwebin`, extension copy |
| 3 | `extension/<ext>/design/standard` for every extension that has one (explayouts, ezjscore, ezoe ...) | design `standard`, extension copies |
| 4 | `design/standard` | design `standard`, kernel copy |
| 5 | `design/base` | design `base`, kernel copy |

Now the template `node/view/line.tpl`:

- if `media` has `templates/node/view/line.tpl`, base 1 wins;
- if not, but ezwebin has it, base 2 wins;
- otherwise the kernel's `design/standard/templates/node/view/line.tpl` is used.

The same search runs for `stylesheets/index.css` (through `ezcss_load`) and for `images/logo.png` (through `ezimage`).

## What the cascade is used for

| Kind of file | Folder inside a base | Looked up by |
|---|---|---|
| Templates | `templates/` | `{include uri='design:...'}`, view templates, the pagelayout |
| Override templates | `override/templates/` | `override.ini` `MatchFile=` |
| Stylesheets | `stylesheets/` | `ezcss`, `ezcss_load`, `ezcss_require`, `ezcssfiles` |
| Scripts | `javascript/` | `ezscript`, `ezscript_load`, `ezscript_require`, `ezscriptfiles` |
| Design images | `images/` | `ezimage` |
| Any design file | the path you give | `ezdesign` (for example `'stylesheets/print.css'\|ezdesign`) |

## Overrides come before the cascade

When a template is requested, the kernel first checks the override rules of `override.ini` for that template. If a rule
matches the current page (class, node, view mode ...), its `MatchFile` is used, searched under `override/templates/`
through the same bases. Only when no rule matches is the cascade's plain copy used. See
Override basics.

## The caches of the cascade

| Cache | When it matters | How to clear |
|---|---|---|
| In-memory list per request | always | nothing to do |
| Design base list on disk | only with `site.ini [DesignSettings] DesignLocationCache=enabled` (default `disabled`) | `php bin/php/ezcache.php --clear-id=design_base` |
| Template override cache | after adding a template in a design or a rule in `override.ini` | `php bin/php/ezcache.php --clear-id=template-override` |
| Compiled templates | after editing templates, when template compilation caches are kept | `--clear-id=template` |
| INI cache | after changing `site.ini` or `design.ini` | `--clear-tag=ini` |

> **Tip**
> A **new** template file that seems to be ignored is almost always the template override cache. A page that renders
> with an empty content area right after you added templates is the same cause. Clear `template-override` first.

On this installation, deploys clear these caches through `./console exp:velocity deploy` (template, template-override,
translation and design_base caches are among its steps).

## Rules of thumb

- **Put a file in your design only when it differs.** Everything else falls through to the designs below.
- **Do not edit `design/standard` or another shipped design.** Copy the template into your own design at the same path
  and change the copy. An upgrade then never overwrites your work.
- **Keep the fallback list short and intentional.** Each extra design is one more folder searched, and one more place a
  surprising template can come from.
- **Name the path, not the design, in templates.** Write `{include uri='design:parts/menu.tpl'}`, never a path with
  a design name in it; the cascade picks the right copy.

## Related pages

- [Designs and siteaccesses](designs-and-siteaccesses.md)
- [Design extensions](design-extensions.md)
- [The design directory layout](design-directory-layout.md)
- [Debugging a design](debugging-a-design.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
