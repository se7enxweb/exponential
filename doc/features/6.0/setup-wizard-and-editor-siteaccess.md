# The setup wizard's new look, and the editor siteaccess

On 2026-10-01 and 2026-10-02 the setup wizard got a new look and now installs **three** siteaccesses instead of
two: the site, the admin and the **editor**, an administration interface for people who edit content but do not
administer the site.

## The three siteaccesses

| Siteaccess | Host convention | For | Design |
|---|---|---|---|
| site | `yourdomain.com` | visitors | your site design |
| admin | `admin.yourdomain.com` | administrators | admin4 (or the admin design chosen) |
| editor | `edit.yourdomain.com` | editors | `editor`, which falls back to `admin4`, then `admin3`, `admin2`, `admin` |

The editor siteaccess is made from the installed admin one (`settings/siteaccess/editor`): its `SiteName` is
"Editor", it has its own `SiteURL`, the admin's extension settings, the content tabs only (Dashboard, Content
structure, Media, Users, Store, Tags, Newsletter), no developer toolbar, no Layouts node tab, and the modules
behind the hidden tabs answer 404 (`setup`, `visual`, `explayouts_ui`, `explayouts_ui_api`, `git_manager`,
`xrowextract`, `bccie`). `ExtensionSettingsSiteAccess=admin` makes extensions' admin settings apply there too.
The site's service worker stays out of the editor siteaccess, as it does of the admin (`/editor` joins `/admin`),
and the wizard gives the editor a right sidebar that starts with Bookmarks.

`design/editor` has no templates of its own: it is the design of the editor siteaccess and a README describes
what makes it an editor's administration. Templates go there only where the editor must look different.

## In the wizard

- **Site access step** names the three and the hostname convention.
- **Site details step** has a required Editor path, port or hostname field, prefilled with `editor`, `8082` or
  `edit.<host>`, checked like the admin field. The Database field lists only SQLite database files found in
  `var/storage/sqlite3` (a file with the "SQLite format 3" header, or an empty file), instead of a paragraph of
  every file, SQL dumps included.
- **Finished page** shows the user, admin and editor sites, each with its address, and the Exponential multisite
  picture.
- The look: one white card on a dark slate page with a soft orange glow (`setup3.css`, loaded after `setup.css` and
  `setup2.css`, overrides only), the progress bar as a slim orange bar under the logo, Help and Summary in a
  side panel. The footer reads "Exponential copyright 1998-2026 7x & Exponential Foundation" and the links point
  to exponential.earth.
- The wizard no longer logs "Language 'eng-GB' does not exist or is not used!" on every page (the setup
  siteaccess only).
- Velocity's warm-up never takes the wizard's lease: with `CheckValidity=true` each warm-up render used to count as
  the wizard's first page and locked everyone out with "The site is being set up" for half an hour after a restart.

## Kickstarter

`kickstart.ini` names the editor like the admin siteaccess:

| Key | Default | Meaning |
|---|---|---|
| `EditorAccess` | `editor` | the siteaccess name |
| `EditorAccessPort` | `8082` | port access method |
| `EditorAccessHostname` | `edit.<host>` | host access method, by convention `edit.<your domain>` |

`kickstart.ini-dist` carries the three settings, commented, after the admin ones.

## Existing installations

Nothing changes for an installation made earlier. To get an editor siteaccess, copy `settings/siteaccess/editor`
and `design/editor` from a fresh installation, set its `SiteName`, `SiteURL` and `SiteDesign`, and map it like the
admin siteaccess in `site.ini` (`[SiteAccessSettings]`). The design list in the editor's `site.ini.append.php`
(`AdditionalSiteDesignList[]`) names `admin4`, `admin3`, `admin2`, `admin`.

Related: [hidden admin tabs](hidden-admin-tabs.md), [admin4 design](admin4-design.md),
[kickstarter CLI](../../bc/6.0/kickstartercli.md), [October 2026 chronicle](../../history/2026/2026-10.md).
