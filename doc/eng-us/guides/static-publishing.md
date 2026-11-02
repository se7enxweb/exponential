# Static publishing: an overview for administrators

This guide is for administrators who look after an Exponential 6.0.15+ installation and want to know what static
publishing adds, what it needs and what it changes in daily work, before they set up the first target.

[Guides](README.md) · Related: [Static publishing](../features/6.0/static-publishing.md),
[Host static sites on GitHub Pages with Exponential 6.0.15+](host-static-sites-on-github-pages.md),
[The help manual](../help/static-publishing/README.md)

## What you'll have at the end

- A clear picture of the parts: targets, builds, publishes, the history and the triggers.
- The settings, permissions, cronjob and tables an installation needs for it.
- A checklist for switching it on.

## The parts

| Part | What it is | Where |
|---|---|---|
| **Target** | One public copy: which front siteaccess, how links are written, which repository and branch, how the push signs in | `[Target_<id>]` in `staticcache.ini`, made with the wizard in Setup > Static publishing |
| **Build** | The siteaccess crawled as the anonymous user, cleaned, its links rewritten, its files collected, extras and a manifest written | `exp:static:build`, **Build** in the administration |
| **Publish** | The build committed in the target's own git repository, tagged `static/<target>/<n>` and pushed, never forced | `exp:static:publish`, **Publish** on the preview |
| **History** | Every publish with its commit and tag; a rollback is a new commit | `exp:static:rollback`, **History** |
| **Triggers** | Publishing on content publish (debounced) or on a schedule | `Triggers[]`, the cronjob part `staticpublish` |

The target's folder `var/<site>/static-publish/<target>/` is the copy and its git repository. It is never inside
the installation's own repository.

## What it needs

- Static publishing switched on: `[StaticPublishSettings] Enabled=enabled`, and the target's id in `TargetList[]`.
  The wizard writes both to `settings/override/staticcache.ini.append.php`.
- The upgrade step `6.0.15-static-publish-tables`, which creates `exp_static_run` and `exp_static_page` on every
  supported database. `exp:upgrade` runs it.
- `git` on the server, for publishing; PHP's curl and openssl for the GitHub integration.
- A secret in Setup > Secrets for the push (a token or a deploy key), named by the target's `Credential`. A secret is
  never written into the target itself.
- The cronjob part `staticpublish` in your cronjob schedule, if pushes should be retried or targets publish
  themselves.
- The commands run as the system user that owns the site's files.

## Who may do what

The policies `staticpublish/read`, `build`, `publish` and `setup` take a Target limitation. Publishing, rolling back,
saving a target and making a deploy key ask "Verify it's you". Give editors `read` and, if they should publish
single pages from the website sidebar, `build` and `publish` for their target only. See
[Who may publish](../help/static-publishing/who-may-publish-roles-and-policies.md).

## What changes day to day

- Editors work as before. The public copy changes only when a target is built and published, by a person, a
  trigger or a schedule.
- The dashboard block **Static publishing** shows stale pages per target: pages whose content changed after the last
  publish.
- Search, forms, comments, logins and the basket need a server. The build gives each a fallback and lists them in its
  report; see [Dynamic features](static-publishing-dynamic-features.md).
- The live installation stays where it is. It can stay internal; the copy is what the public sees, while forms can
  still post to the live site's form endpoint.

## Switching it on, a checklist

1. Run `./console exp:upgrade` so the tables exist.
2. Store the push credential in Setup > Secrets.
3. Make the target in Setup > Static publishing > **New target** ([The setup wizard](../help/static-publishing/the-setup-wizard.md)).
4. Build, read the report and the diff, preview the copy ([Build, preview and publish](../help/static-publishing/build-preview-and-publish.md)).
5. Read [Keep private content private](../help/static-publishing/keep-private-content-private.md) before the first
   public push.
6. Publish, then set GitHub Pages (or your host) to serve the branch.
7. Give the roles their policies, and add the cronjob part if you use triggers.

## Next step

[Host static sites on GitHub Pages with Exponential 6.0.15+](host-static-sites-on-github-pages.md), or the
15-minute tutorial [Make a free website in 15 minutes](../help/static-publishing/make-a-free-website-in-15-minutes.md).
