---
title: Per-site settings in extensions
audience: government, admin, developer
category: public-sector
order: 50
summary: How each department site keeps its settings, design and templates together in one extension that applies only while its siteaccesses run (settings/override inside the extension), so a multisite installation stays manageable and each site can live in its own Git repository.
keywords: multi-site settings, extension per site, settings/override in extension, siteaccess settings, ExtensionSettingsSiteAccess, INI cache, Git repository per site
type: help
---
# Per-site settings in extensions

This page is for administrators and developers of an installation with many sites. Since January 2026 an extension
that holds a site can carry override settings that are active only while one of its siteaccesses runs, and clearing
the INI cache clears it for every project ([Per-site settings inside extensions](../../../../features/6.0/multi-site-ini-overrides.md)).

## Contents

1. [Layout of a site extension](#layout-of-a-site-extension)
2. [Setting it up](#setting-it-up)
3. [Why this suits departments](#why-this-suits-departments)
4. [Translation siteaccesses](#translation-siteaccesses)

## Layout of a site extension

```
extension/site_dept_example/
  settings/override/site.ini.append.php        settings for all siteaccesses of this site
  settings/siteaccess/dept_example/...         settings of one siteaccess
  design/dept_example/templates/...            the design layer
  translations/...                             interface texts (extension named in TranslationExtensions[])
```

## Setting it up

1. Give the extension a `settings/siteaccess/<siteaccess>/` directory for its siteaccess; the new directories are only
   looked up when it does, so unrelated extensions cost nothing.
2. Put settings for every siteaccess of the extension in its `settings/override/`.
3. Clear the INI cache: `php bin/php/ezcache.php --clear-tag=ini`.

## Why this suits departments

- One Git repository per department site, with its own history and reviewers.
- The central settings stay small; a department's change cannot change another's settings.
- Moving a site to another installation means moving its extension and its content.

## Translation siteaccesses

A language siteaccess that should look like the department's main site gets
`[SiteAccessSettings] ExtensionSettingsSiteAccess=<main siteaccess>`
([Extension loading order](../../../../features/6.0/extension-loading-order.md)).

## Related pages

- [Per-site settings inside extensions](../../../../features/6.0/multi-site-ini-overrides.md)
- [Adding a department site](adding-a-department-site.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
