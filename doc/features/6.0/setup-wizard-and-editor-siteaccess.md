# The setup wizard's new look, and the editor siteaccess

This page is for anyone who installs Exponential, and for administrators who want a simpler admin for their editors.
On 2026-10-01 and 2026-10-02 the setup wizard got a new look, and it now installs **three** siteaccesses instead of
two: the site, the admin, and the **editor**, an administration interface for people who edit content but do not
administer the site.

## The three siteaccesses

| Siteaccess | Host convention | For | Design |
|---|---|---|---|
| site | `yourdomain.com` | visitors | your site design |
| admin | `admin.yourdomain.com` | administrators | admin4 (or the admin design chosen) |
| editor | `edit.yourdomain.com` | editors | `editor`, which falls back to `admin4l`, then `admin4`, `admin3`, `admin2`, `admin` |

The editor siteaccess (`settings/siteaccess/editor`) is made from the installed admin one. Compared with the admin:

- its `SiteName` is "Editor" and it has its own `SiteURL`;
- it carries the admin's extension settings; `ExtensionSettingsSiteAccess=admin` makes extensions' admin settings
  apply there too;
- it shows the content tabs only: Dashboard, Content structure, Media, Users, Store, Tags, Newsletter;
- it has no developer toolbar and no Layouts node tab;
- the modules behind the hidden tabs answer 404: `setup`, `visual`, `explayouts_ui`, `explayouts_ui_api`,
  `git_manager`, `xrowextract`, `bccie`, `syndication` (feed export and import are site administration, not
  editing);
- the site's service worker stays out of it, as it does of the admin (`/editor` joins `/admin`);
- the wizard gives it a right sidebar that starts with Bookmarks.

`design/editor` has no templates of its own. It is the design of the editor siteaccess, and a README describes what
makes it an editor's administration. Put templates there only where the editor must look different.

## Install with the wizard

1. **Site access step**: the wizard names the three siteaccesses and the hostname convention.
2. **Site details step**: fill the required Editor path, port or hostname field. It is prefilled with `editor`,
   `8082` or `edit.<host>` and checked like the admin field. The Database field lists only SQLite database files found
   in `var/storage/sqlite3` (a file with the "SQLite format 3" header, or an empty file), instead of every file, SQL
   dumps included.
3. **Finished page**: you see the user, admin and editor sites, each with its address, and the Exponential multisite
   picture.

The look: one white card on a dark slate page with a soft orange glow (`setup3.css`, loaded after `setup.css` and
`setup2.css`, overrides only), the progress bar as a slim orange bar under the logo, Help and Summary in a side panel.
The footer reads "Exponential copyright 1998-2026 7x & Exponential Foundation", and the links point to
exponential.earth.

Fixes of the same change:

- The wizard no longer logs "Language 'eng-GB' does not exist or is not used!" on every page (the setup siteaccess
  only).
- Velocity's warm-up never takes the wizard's lease. With `CheckValidity=true`, each warm-up render used to count as
  the wizard's first page and locked everyone out with "The site is being set up" for half an hour after a restart.

## Install with the Kickstarter

`kickstart.ini` names the editor like the admin siteaccess. `kickstart.ini-dist` carries the three settings, commented,
after the admin ones.

| File | Key | Default | Meaning |
|---|---|---|---|
| `kickstart.ini` | `EditorAccess` | `editor` (`<Access>_editor` when `editor` is taken) | the siteaccess name |
| `kickstart.ini` | `EditorAccessPort` | `AccessPort` + 2 (`8082` for `8080`), the next free port when that is taken | port access method |
| `kickstart.ini` | `EditorAccessHostname` | `edit.<AccessHostname without www.>` (`edit.example.com` for `www.example.com`) | host access method |

The default never equals the site's or the admin's value: two siteaccesses on one port or host would make one of
them unreachable. `exp:install` takes `--editor-access`, `--editor-port` and `--editor-host`, and refuses a value
that collides.

## Add an editor siteaccess to an existing installation

Nothing changes for an installation made earlier. To get an editor siteaccess:

1. Copy `settings/siteaccess/editor` and `design/editor` from a fresh installation.
2. Set its `SiteName`, `SiteURL` and `SiteDesign`. The design list in the editor's `site.ini.append.php`
   (`AdditionalSiteDesignList[]`) names `admin4l`, `admin4`, `admin3`, `admin2`, `admin`.
3. Map it like the admin siteaccess in `site.ini`, block `[SiteAccessSettings]`.
4. Clear the caches: `php bin/php/ezcache.php --clear-all --allow-root-user`.

## Related pages

- [Hidden admin tabs](hidden-admin-tabs.md), [admin links follow permissions](admin-links-follow-permissions.md), [the admin4 design](admin4-design.md)
- [Install in one command](install-in-one-command.md), [Kickstarter CLI](kickstarter-cli.md), [Kickstarter CLI reference](../../bc/6.0/kickstartercli.md)
- [Upgrade checklist of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md)
- [6.0.15 changelog](../../changelogs/6.0/6.0.15.md)
- History: [October 2026](../../history/2026/2026-10.md), [January 2024, second half](../../history/2024/2024-01b.md)
