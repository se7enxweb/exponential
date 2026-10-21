---
title: "Start the project: the case, a siteaccess and a design"
audience: builder, designer, developer
category: books
order: 100
summary: The chess club case of the Building a Site tutorial, and the setup it begins with (a siteaccess, a design, a settings extension and a design extension), done the Exponential 6 way instead of renaming the Plain site's folders.
keywords: tutorial, building a site, case, siteaccess, design, extension, site.ini, SiteDesign, VarDir, override, debug, Building book
type: help
---
# Start the project: the case, a siteaccess and a design

The tutorial *Building an eZ publish site* teaches by building one small site from nothing. This help keeps its case and its
order, and does every step the way it is done in Exponential 6. It follows the same arc as
[Building a site without writing code](../extension-development/part-2-builder/building-a-site-without-code.md), which
is the compact version; read that for the shortest path and this series for the longer walk with the book's pages (welcome
page, news, members, guestbook with approval, links).

## The case

A local chess club, *The Scandinavian Check Mates* (TSCM), about forty members who love chess and know how to use a
computer. The club's old site was made by a member who moved away. Their list of wishes:

- it should look good but not bloated,
- a page with **news** bulletins,
- a **list of members**,
- a **guestbook**,
- it should be easy to maintain and extend.

The tutorial's lesson is in the next sentence: a list like this is only the start; the club will ask for more, so build
flexibly. With a content model and templates, the guestbook and the members list are classes and views, not code.

## What the book did, and what you do

The book installed the *Plain* site, renamed its two siteaccess folders (`plain` to `tscm`, `plain_admin` to `tscm_admin`),
edited their `site.ini.append`, replaced every `plain` in the global override, removed `var/plain` and made `design/tscm`.
Do not rename a package's folders. Make your own, in extensions, so that an update of Exponential or of a package never
touches your work.

1. **Install** Exponential ([Getting started](../../guides/getting-started.md)). Any site package will do; the content
   classes used here (`folder`, `article`) are in all of them.
2. **Two extensions**: one for settings (the siteaccess) and one for the design. A settings extension keeps the
   siteaccess out of the kernel's `settings/`:

   ```bash
   mkdir -p extension/exp_tscm_settings/settings/siteaccess/tscm
   mkdir -p extension/exp_tscm_design/settings
   mkdir -p extension/exp_tscm_design/design/tscm/{images,stylesheets,templates,override/templates}
   ```

   Give both an `ezinfo.php` and an `extension.xml` ([ezinfo.php](../extension-development/part-3-extension-developer/ezinfo-php.md)).
3. **Register the design** in `extension/exp_tscm_design/settings/design.ini.append.php`:

   ```ini
   <?php /* #?ini charset="utf-8"?

   [ExtensionSettings]
   DesignExtensions[]=exp_tscm_design

   */ ?>
   ```
4. **The siteaccess** in `extension/exp_tscm_settings/settings/siteaccess/tscm/site.ini.append.php`: the book's settings
   (`SiteName`, `SiteDesign`, `VarDir`) are still the ones:

   ```ini
   [SiteSettings]
   SiteName=The Scandinavian Check Mates

   [DesignSettings]
   SiteDesign=tscm
   AdditionalSiteDesignList[]
   AdditionalSiteDesignList[]=base

   [FileSettings]
   VarDir=var/tscm
   ```

   Add the siteaccess to the list in the global override (`[SiteAccessSettings] AvailableSiteAccessList[]=tscm`) and choose how
   it is reached (URL, port or host name), as in [the setup wizard](../book-learning/the-setup-wizard-then-and-now.md).
   The book's `[...]/index.php/tscm` address is the old form; today the host name or the path comes from the web server
   and `MatchOrder` ([Siteaccess settings](../extension-development/part-2-builder/siteaccess-settings.md)).
5. **Activate** both extensions, the settings extension first ([Extension load order](../extension-development/part-3-extension-developer/extension-load-order.md)):
   `ActiveExtensions[]=exp_tscm_settings` and `ActiveExtensions[]=exp_tscm_design`, then
   `php bin/php/ezpgenerateautoloads.php -e` and clear the caches.
6. **Development settings** for your own copy only (the book turns caches off and debug on, and says so for the same
   reason): in the siteaccess file, `[DebugSettings] DebugOutput=enabled` and `[TemplateSettings] Debug=enabled`. Never on a live
   site ([Debug output and logs for developers](../extension-development/part-1-from-user-to-developer/debug-output-and-logs-for-developers.md)).
7. **Test.** Open the site. It shows the standard templates: your empty design falls back to the standard one, as the book
   says ("if the system cannot find something, it falls back to the standard design"). Sign in to the administration
   with your own administrator account, not the book's `admin` / `publish`; the passwords of a new installation are set by
   the installer ([Logging into the admin](../logging-into-the-admin.md)).

Clearing caches after a **new** template file: `php bin/php/ezcache.php --clear-id=template-override,design_base --allow-root-user`
([Which cache do I clear?](../manual/which-cache-do-i-clear.md)). The book makes the same point ("do this every time you add
a template").

Next: [the main layout](the-main-layout.md).

## Source and license

Adapted from the section on creating a new site and the main layout of of the tutorial *Building an eZ publish site* (written for eZ publish 3.2, 2003), originally by
ez.no and released again under the GNU Free Documentation License by 7x in 2024,
[Book-Building-An-eZPublish-Site](https://github.com/se7enxweb/Book-Building-An-eZPublish-Site). Copyright (c)
1999-2024 7x and Graham Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later
version, with no Invariant Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: rewritten for Exponential 6: extensions instead of renamed folders, current settings and operators, no inline style, and a modern page frame.
