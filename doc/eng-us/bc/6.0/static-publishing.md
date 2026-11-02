# Static publishing

Read this page when you upgrade to Exponential 6.0.15 and want to know what static publishing adds to an existing
installation and whether anything you have changes. In short: nothing changes until you switch it on.

[Feature](../../features/6.0/static-publishing.md) · [Help manual](../../help/static-publishing/README.md) ·
[Host static sites on GitHub Pages](../../guides/host-static-sites-on-github-pages.md)

## What is added

| Area | Addition |
|---|---|
| Settings | `[StaticPublishSettings]` and the template block `[Target_example]` in `settings/staticcache.ini`; targets go in `settings/override/staticcache.ini.append.php` |
| Database | the tables `exp_static_run` and `exp_static_page`, made by the upgrade step `6.0.15-static-publish-tables` on every supported database |
| Classes | `kernel/classes/staticpublish/` (`expStatic*`) |
| Console | `exp:static:build`, `diff`, `serve`, `targets`, `publish`, `rollback`, `push`, `keys`, `status`, `github`, `dns` |
| Administration | the module `staticpublish` (Setup > Static publishing), the dashboard block `staticpublish`, a link in Setup > Cache |
| Policies | `staticpublish/read`, `build`, `publish`, `setup`, with a Target limitation |
| Cronjob | the part `staticpublish` (push retry, publish on content publish, schedule) |
| Audit | `system.staticpublish.publish`, `.rollback`, `.credential`, `.target`, `.build`, `.form` |
| Front end | the form endpoint `staticpublish/form`, used only by static copies with `Forms=endpoint` |

## What does not change

- **Off by default.** `[StaticPublishSettings] Enabled=disabled`: nothing is built, no cronjob part does anything, the
  dashboard block shows only a one-line invitation to the wizard, which each administrator can hide.
- **The static cache** (`[CacheSettings]`, `makestaticcache.php`) works as before. Static publishing uses its crawler
  in an export mode but writes nowhere the static cache reads.
- **No role gets the new policies** except Administrator, which has every function anyway. Give editors
  `staticpublish/read` or `publish` when you want them to see or publish.
- **No new requirement.** git is needed only on the server that publishes; PHP's curl and openssl only for the GitHub
  integration, which is switched off when they are missing.

## What to do when upgrading

1. Run the upgrade as usual (`./console exp:upgrade`); it makes the two tables.
2. Nothing else, unless you want to use it: then
   [Make a free website in 15 minutes](../../help/static-publishing/make-a-free-website-in-15-minutes.md).
3. If you add the cronjob part `staticpublish` to a cronjob group, it only acts on targets with triggers or pending
   pushes.

## Settings names that are reserved

`[StaticPublishSettings]` and blocks named `[Target_<id>]` in `staticcache.ini` belong to static publishing. An
override that already used these names for something else must rename them.
