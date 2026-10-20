# Exponential Admin UI: the adminui design and siteaccess

This page is for administrators who want a different look for the administration, and for site builders who look
after admin designs. Exponential Admin UI (the extension `exp_adminui`) is the Netgen Admin UI look and layout for the
Exponential administration: a dark side bar with the main sections and the user menu, a search header, a light left
column with the section's menu or content tree, and the Admin UI's tables, tabs and buttons. It runs as its own
siteaccess, `adminui`, next to the stock administration, which keeps its admin4 / admin4l design.

## What you get

- The Admin UI page frame, ported from Netgen Admin UI's Twig layout to Exponential templates. No Symfony is needed.
- Every admin view, in the Admin UI look. The Admin UI's own view templates are merged with everything Exponential
  added since (Exponential UI widgets instead of YUI, jQuery 4, escaping fixes, new menu entries and views). Views
  the Admin UI never had (audit, cronjobs, maintenance, Exponential Layouts, newsletter ...) come from admin4l and
  admin4 and get the Admin UI frame and colours.
- Exponential's logo and names. The accent colour is the Admin UI blue (admin4 keeps its orange).

## A new installation has it already

Since 2026-10-06 the installation makes the `adminui` siteaccess by default whenever `extension/exp_adminui` is in
the installation: the setup wizard, the kickstarter and `exp:install` (`CreateSites`, `createAdminUISiteAccess()` in
`kernel/setup/steps/ezstep_create_sites.php`), and the multisite package installer of `sevenx_multisite` and
`sevenx_multisite_clean`, which calls the same function as the last of its own post-install steps. What is written:

- `settings/siteaccess/adminui/`: a copy of the admin siteaccess's `*.ini.append.php` files as the installation wrote
  them (database, languages, `VarDir`, `RequireUserLogin=true`, `LoginPage=custom`, `ShowHiddenNodes=true`, the
  override, menu, toolbar, content structure menu, content and view cache settings), with `SiteName=Admin UI`,
  `SiteURL` the admin's address with the path `/adminui`, `SiteDesign=adminui`, the design chain `admin4l`, `admin4`,
  `admin3`, `admin2`, `admin` and `ActiveAccessExtensions[]=exp_adminui`. Its `icon.ini.append.php` and
  `ezoe.ini.append.php` hold no settings, so the extension's icon set and editor settings win.
- `adminui` in `AvailableSiteAccessList[]`, `RelatedSiteAccessList[]` and `SiteList[]` of
  `settings/override/site.ini.append.php`, matched by **URI only**: no `HostMatchMapItems[]` line, no port. `uri` is
  put in front of `MatchOrder` when the access type is host or port, so `https://<admin host>/adminui` works whatever
  the other siteaccesses are matched by.
- Not in `ActiveExtensions[]`: `exp_adminui` is an access extension and stays switched on for `adminui` alone. The
  multisite installer also leaves `adminui` out of the public siteaccesses (HTTP cache, anonymous login).

Without the extension nothing is written and `var/log/setup.log` says why ("no adminui siteaccess, the exp_adminui
extension is not in extension"). An installation made before then, or one that gets the extension later, is set up by
hand as below.

## Switch it on by hand

1. Install the extension in `extension/exp_adminui` (git clone of
   [se7enxweb/exp_adminui](https://github.com/se7enxweb/exp_adminui), or Composer once the package is published).
2. Create `settings/siteaccess/adminui/` as a copy of the admin siteaccess's settings and set:

   ```ini
   [ExtensionSettings]
   ActiveAccessExtensions[]=exp_adminui

   [DesignSettings]
   SiteDesign=adminui
   AdditionalSiteDesignList[]
   AdditionalSiteDesignList[]=admin4l
   AdditionalSiteDesignList[]=admin4
   AdditionalSiteDesignList[]=admin3
   AdditionalSiteDesignList[]=admin2
   AdditionalSiteDesignList[]=admin
   ```

   Leave out (or empty) the `icon.ini.append.php` and `ezoe.ini.append.php` of the copy: the extension brings its own.
3. Add `AvailableSiteAccessList[]=adminui` to `settings/override/site.ini.append.php`.
4. Clear the caches: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`, then
   `--clear-id=template,template-override,template-block,content,translation`; under Velocity also
   `./console exp:velocity cache clear --allow-root-user`.

Open `https://<host>/adminui` and sign in as for the administration.

## Settings

`extension/exp_adminui/settings/exp_adminui.ini`: the title, the logo type, the side bar entries (`[MenuPlugins]`), the
navigation parts without a path bar, the default node view tab and the code editor fields. The extension's guide
(`extension/exp_adminui/doc/`) describes each one, the architecture, every view and its template, the merge with
Exponential's changes, extending the design, known gaps and troubleshooting.

## Good to know

- Below 640 CSS pixels wide the Admin UI shows "The current window is too small", as Netgen Admin UI does.
- The extension is a design with settings: no PHP classes, no database tables. A template or stylesheet change needs
  the caches cleared, not a server restart.

## Related pages

- [The admin4 design](admin4-design.md) and [admin4l](admin4l.md)
- [Templates and design](../../guides/templates-and-design.md) (the guide)
