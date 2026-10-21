---
title: "Siteaccesses: site and admin"
audience: dxp-developer
category: dxp-development
order: 15
summary: What a siteaccess is on the DXP, the two the skeleton installs (site and admin), how configuration is resolved through default, groups, the siteaccess and global, and how to add a second site or language.
keywords: siteaccess, site, admin, admin_group, site_group, siteaccess groups, scope, default, global, multisite, languages, config resolver, ibexa.yaml
type: help
---
# Siteaccesses: site and admin

This page is for developers who configure what a request sees. A siteaccess is a named configuration that the DXP
selects for each request: which design, which languages, which part of the content tree, which settings. The idea
is the same as the siteaccess of Exponential 6, expressed in YAML.

## Contents

1. [The two siteaccesses you start with](#the-two-siteaccesses-you-start-with)
2. [How settings are resolved](#how-settings-are-resolved)
3. [Where the configuration lives](#where-the-configuration-lives)
4. [Adding a site or a language](#adding-a-site-or-a-language)
5. [Checking a value](#checking-a-value)

## The two siteaccesses you start with

| Siteaccess | Serves | URL |
|---|---|---|
| `site` | The Twig front end | `/` |
| `admin` | The administration interface | `/adminui/` |

Source: skeleton guide, section 2. The administration bundle expects its siteaccesses in the group named by
`admin_group_name` (`admin_group` by default).

## How settings are resolved

Settings under `ibexa.system` are read in this order, the later winning: `default`, the siteaccess group, the
siteaccess itself, and `global`. The comment in an installed `config/packages/ibexa.yaml` says so, and adds the tip:
"For multisite installations organize shared config into SiteAccess groups, on single-site you can place shared
config under `default`".

```yaml
ibexa:
    system:
        default:            # every siteaccess
            content:
                default_ttl: '%httpcache_default_ttl%'
        site_group:         # every siteaccess of the group
            image_variations: { }
        site:               # only this siteaccess
            languages: [eng-GB]
```

## Where the configuration lives

| What | Key |
|---|---|
| The list of siteaccesses, the default one, the groups, how a request is matched | `ibexa.siteaccess` (`list`, `default_siteaccess`, `groups`, `match`) |
| Settings per scope | `ibexa.system.<scope>` |
| The design of a siteaccess | `ibexa.system.<scope>.design` ([Twig and the design engine](twig-templates-and-the-design-engine.md)) |

The recipes write these files under `config/packages/`; a project may keep them in other YAML files that the kernel
imports. Search for them with `grep -rn "siteaccess:" config/`.

## Adding a site or a language

1. Add the new siteaccess to `ibexa.siteaccess.list` and to a group (a new `site2_group`, or the existing site group).
2. Add a matcher under `ibexa.siteaccess.match` (by host, by URI element, or another matcher the core offers).
3. Give it its scope under `ibexa.system`: `languages`, the tree root, the `design`.
4. Clear the cache and check the value (below).

Add administration siteaccesses only to the admin group, never the public ones.

## Checking a value

```bash
php bin/console exponential:debug:config-resolver languages --siteaccess=site
php bin/console exponential:debug:config-resolver design --siteaccess=admin
php bin/console debug:config ibexa siteaccess
```

`exponential:debug:config-resolver` (alias `ibexa:debug:config-resolver`) shows the value a siteaccess resolves
([Console commands](console-commands-and-renames.md)).

## Related pages

- [The admin UI](the-admin-ui.md)
- [Siteaccess settings in Exponential 6](../extension-development/part-2-builder/siteaccess-settings.md), for comparison
- [Coming from Exponential 6](coming-from-exponential-6.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
