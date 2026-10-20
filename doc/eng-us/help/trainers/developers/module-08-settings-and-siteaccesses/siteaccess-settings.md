---
title: "Lesson 8.2: Siteaccess settings"
audience: trainer, developer
category: training
order: 82
summary: Learners see a siteaccess from a developer's side, list the siteaccesses and how requests are matched to them, compare settings between two siteaccesses, and decide whether a setting belongs in the global override, a siteaccess or an extension. 45 minutes.
keywords: developers course, siteaccess, settings/siteaccess, AvailableSiteAccessList, RelatedSiteAccessList, MatchOrder, URIMatchType, ActiveAccessExtensions, siteaccess scope, exp:ini scopes, -s siteaccess, where does a setting belong
type: help
---
# Lesson 8.2: Siteaccess settings

A siteaccess is a set of settings chosen for a request: the public site, the administration, the editor, a second
language. For developers it answers two questions: which siteaccess is running, and where a setting should be
written so it applies to the right ones. This lesson covers both; the Multisite and site packages course goes deep
into matching and several sites.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 8: Settings and siteaccesses](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Developers |
| Prerequisites | [Lesson 8.1](how-settings-are-read.md) |

## Learning objectives

After this lesson, learners can:

- list the siteaccesses of an installation and the folder that holds each one's settings;
- read `MatchOrder` and the matching settings that choose a siteaccess for a request;
- run a command for a given siteaccess with `-s <siteaccess>`;
- compare one setting between two siteaccesses with `exp:ini where`;
- decide whether a setting belongs in the global override, a siteaccess folder or an extension.

## Trainer's notes

### How to present it

Sources: `settings/site.ini [SiteAccessSettings]`,
[What a siteaccess is](../../multisite-and-packages/module-01-siteaccesses-for-several-sites/what-a-siteaccess-is.md)
(Multisite and site packages course) and
[Siteaccess host and language matching](../../../../features/6.0/siteaccess-host-and-language-matching.md).

1. **What it is** (3 minutes). A siteaccess is a folder `settings/siteaccess/<name>/` with settings for one way
   into the installation, listed in `[SiteAccessSettings] AvailableSiteAccessList[]`.
2. **Matching** (4 minutes). `MatchOrder=uri;host;port` by default: the first part of the path, the host name, the
   port. `URIMatchType=element` with `URIMatchElement=1` takes the first path element (`/admin/...`). An
   installation from `exp:install` uses path matching by default.
3. **Related siteaccesses** (2 minutes). `RelatedSiteAccessList[]` names the siteaccesses that share one content
   database, for example a site, its administration and its editor.
4. **Commands** (2 minutes). Scripts and `exp:ini` run for a siteaccess: `-s <sa>`, or the second argument of
   `exp:ini where`.
5. **Where a setting belongs** (4 minutes).

   | The setting is for | Write it in |
   |---|---|
   | Every siteaccess, this machine only | `global` (`settings/override/`) |
   | One siteaccess, this machine only | `siteaccess:<sa>` |
   | Every installation of your extension | `extension:<ext>` |
   | One siteaccess of every installation of your extension | `extension:<ext>:siteaccess:<sa>` |

### Common questions

**Which siteaccess is a page served by?** The debug output names it; `exp:ini where ... <sa>` shows its settings.

**Can an extension be active for one siteaccess only?** Yes, with `ActiveAccessExtensions[]` in that siteaccess's
`site.ini.append.php` (lesson 7.3).

**What is the admin siteaccess's design?** Its own `[DesignSettings] SiteDesign`, usually `admin4` on new
installations.

### Pitfalls

- **Settings for a site written to the global override.** They then apply to the administration too.
- **A new siteaccess folder that is not listed.** A folder without an entry in `AvailableSiteAccessList[]` is never
  chosen.
- **Testing with the wrong siteaccess.** A script without `-s` runs with the default siteaccess, which may not be
  the one you changed.

## Hands-on exercise

On the development installation.

1. List the siteaccess folders: `ls settings/siteaccess/`.
2. List the available siteaccesses in effect:
   `php bin/php/console exp:ini get site.ini/SiteAccessSettings/AvailableSiteAccessList`.
3. Read the matching: `php bin/php/console exp:ini where site.ini/SiteAccessSettings/MatchOrder`.
4. List the scopes your installation offers: `php bin/php/console exp:ini scopes`.
5. Compare the design of two siteaccesses:
   `php bin/php/console exp:ini where site.ini/DesignSettings/SiteDesign <sa>` and the same with `admin`.
6. Run the playground for two siteaccesses and compare a link: add `{'content/view/full/2'|ezurl}` to `play.tpl`
   and run `php bin/php/ezexec.php -s <sa> extension/mysite_hello/scripts/play.php`, then with `-s admin`.
7. For each of these settings, write the scope you would choose: the site's name, debug output on your laptop,
   your extension's operator registration, a template override for the public site only.
8. Check your answers with the table of the trainer's notes.

## Expected result

- Step 2: the siteaccess names, for example `site`, `admin` and `editor` on an `exp:install` installation.
- Step 3: `MatchOrder` with its value in effect.
- Step 5: different designs, for example the site design and `admin4`.
- Step 6: the links differ in their siteaccess part when matching is by path.
- Step 7: siteaccess (or an extension's siteaccess scope), global, `extension:mysite_hello`,
  `extension:mysite_hello:siteaccess:<sa>`.
- The trainer discusses step 7 with the group.

## Quiz

1. Which setting lists the siteaccesses an installation may choose?
2. What is the default `MatchOrder`?
   A. `host;uri;port` B. `uri;host;port` C. `port;host;uri` D. `uri`
3. How do you run a script for the siteaccess `admin`?
4. In which scope does a template override for the public site of every installation of your extension belong?
5. Why is the global override a poor place for settings of the public site only?

<details>
<summary>Answers</summary>

1. `[SiteAccessSettings] AvailableSiteAccessList[]`.
2. **B.** `uri;host;port`, in `settings/site.ini`.
3. With `-s admin`, for example `php bin/php/ezexec.php -s admin <script>`.
4. `extension:<ext>:siteaccess:<sa>`, the extension's `settings/siteaccess/<sa>/`.
5. It applies to every siteaccess, the administration and the editor included.

</details>

## Further reading

- [What a siteaccess is](../../multisite-and-packages/module-01-siteaccesses-for-several-sites/what-a-siteaccess-is.md):
  the Multisite and site packages course.
- [Siteaccess host and language matching](../../../../features/6.0/siteaccess-host-and-language-matching.md).
- [Multi-site INI overrides](../../../../features/6.0/multi-site-ini-overrides.md): per-site settings inside
  extensions.

## Related pages

- [Module 8: Settings and siteaccesses](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
