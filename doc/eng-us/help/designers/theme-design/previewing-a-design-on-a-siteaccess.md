---
title: Previewing a design on a siteaccess
audience: designer, admin
category: theme-design
order: 160
summary: How to see a new or changed design on real content without touching the live site: a preview siteaccess with the same content and your design, the settings it needs, and how to switch the live site over when the design is ready.
keywords: preview design, test siteaccess, staging design, new siteaccess, design preview, switch design, launch design, AvailableSiteAccessList, RelatedSiteAccessList, SiteDesign
type: help
---
# Previewing a design on a siteaccess

This page is for designers who want to work on a design against the real content while visitors keep seeing the old
one. The usual way is a **preview siteaccess**: a second way into the same content, with your design. It answers:
which settings such a siteaccess needs, how to reach it, and how to switch over.

## Contents

1. [The idea](#the-idea)
2. [What an administrator sets up](#what-an-administrator-sets-up)
3. [The siteaccess settings](#the-siteaccess-settings)
4. [Reaching it](#reaching-it)
5. [Working on it](#working-on-it)
6. [Going live](#going-live)
7. [Clean up](#clean-up)

## The idea

The live siteaccess (say `site`) and the preview siteaccess (say `site_preview`) read the same database and the same
content. Only their designs differ. You develop in `site_preview`; nothing changes for `site` until you change its
`SiteDesign`.

## What an administrator sets up

Creating a siteaccess touches installation settings (`settings/override/site.ini.append.php`), which only an
administrator changes. Ask for:

1. `site_preview` added to `site.ini [SiteAccessSettings] AvailableSiteAccessList[]`;
2. `site_preview` added to `RelatedSiteAccessList[]` of the siteaccesses that share the database (so caches are
   cleared for it too);
3. a way to reach it: with the default `MatchOrder=uri;host;port` and `URIMatchType=element`, the path
   `/site_preview/` already works; a host name such as `preview.example.com` needs a host map entry
   ([Designs and siteaccesses](designs-and-siteaccesses.md)).
4. sign-in required for the preview, so search engines and visitors never see it:
   `[SiteAccessSettings] RequireUserLogin=true` in its `site.ini.append.php`.

## The siteaccess settings

Start with a copy of the live siteaccess's folder: `settings/siteaccess/site/` to `settings/siteaccess/site_preview/`
(only the design-related files are needed if the extension settings come in by `ExtensionSettingsSiteAccess`), then
change the design lines in `site.ini.append.php`:

```ini
[DesignSettings]
SiteDesign=mytheme
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=standard
AdditionalSiteDesignList[]=base

[SiteAccessSettings]
RequireUserLogin=true
# the extension siteaccess settings of the live site, e.g. its overrides
ExtensionSettingsSiteAccess=site

[TemplateSettings]
# while you develop: show which template drew what
ShowTemplatePathComments=enabled
```

Never copy database passwords or other secrets into a new file; a siteaccess file normally does not need them, because
the database settings come from the installation's override settings.

If your design extension should only be active for the preview, list it in `ActiveAccessExtensions[]` of the preview
siteaccess only.

## Reaching it

- `https://www.example.com/site_preview/` (uri matching), or the host the administrator mapped.
- Sign in with your own account. Use a test account with the same roles as an editor to see what editors see.

## Working on it

- Template changes show at once when template compilation checks the files' times
  (`site.ini [TemplateSettings] DevelopmentMode=enabled` on the preview; never on the live site).
- New template files, new override rules, new INI lines: clear the template-override and INI caches
  (`php bin/php/ezcache.php --clear-id=template-override` and `--clear-tag=ini`), or ask for a deploy.
- Stylesheets: with `DevelopmentMode=enabled` the packer is at level 0 and serves your files one by one, which is
  easier to debug ([The packer](the-packer.md)).

## Going live

1. Run the [theme launch checklist](checklist-theme-launch.md).
2. Change `SiteDesign` (and the fallback list) of the live siteaccess to the preview's values.
3. Activate the design extension for the live siteaccess if it was preview-only.
4. Clear caches and reload PHP (on Exponential installations with Velocity: `./console exp:velocity deploy`), then
   clear the content view cache so no page rendered with the old design is served.
5. Open the most important pages as a visitor (signed out) on a phone and a desktop.

## Clean up

Remove `ShowTemplatePathComments` and `DevelopmentMode` from any siteaccess visitors use, and remove the preview
siteaccess or keep it signed-in-only for the next design change.

## Related pages

- [Designs and siteaccesses](designs-and-siteaccesses.md)
- [Debugging a design](debugging-a-design.md)
- [Designs in a multisite installation](designs-in-multisite.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
