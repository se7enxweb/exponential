# Installation name: never confuse staging with production again

This page is for teams that run a development copy, a staging copy and a live site side by side. Their browser tabs
look the same, and it is easy to change the wrong one. The installation name feature puts the name of the installation
where you see it: in front of the page title on every installation that is not production, in a comment in the page
head, and wherever a template asks for it. Added on 7 August 2026 (`cbfee1241e`, classes
`ExpInstallationDetailsOutputFilter` and `ExpInstallationOperator`).

## Set it up in three steps

1. Name the installation, and list the names that count as production, in `settings/override/site.ini.append.php`:

   ```ini
   [SiteSettings]
   EzInstallationName=staging
   ProductionInstallationList[]
   ProductionInstallationList[]=live
   ```

2. Switch the output filter on, in the same file:

   ```ini
   [Event]
   Listeners[]=response/output@ExpInstallationDetailsOutputFilter::filter
   ```

   The `[Event]` block is private API that may change (see the comment in `settings/site.ini`). Without the listener,
   the template operators below still work.

3. Clear the INI cache:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   ```

Check the result on the staging copy:

```bash
curl -s https://staging.example.com/ | grep -E '\[I\]|<title>'
```

Expected output, something like:

```
<!-- [I] staging -->
<title>[staging] Home / My site</title>
```

## Settings

The keys are not in the shipped `site.ini`; add them to `settings/override/site.ini.append.php`.

| File | Block | Key | Default | Scope | Meaning |
|---|---|---|---|---|---|
| `settings/site.ini` | `SiteSettings` | `EzInstallationName` | not set | global | The name of this installation, for example `staging`. When empty or missing, the feature does nothing. |
| `settings/site.ini` | `SiteSettings` | `ProductionInstallationList[]` | not set | global | The names that count as production. If `EzInstallationName` is in the list, the installation is production. |
| `settings/site.ini` | `Event` | `Listeners[]` | not set | global | `response/output@ExpInstallationDetailsOutputFilter::filter` switches the output filter on |

## What it does to a page

| Where | Effect | When |
|---|---|---|
| Directly after the first `<head>` | An invisible comment `<!-- [I] staging -->` | always, when the name is set |
| The first `<title>` | Becomes `<title>[staging] ...` | only when the installation is **not** production |
| `<!--INSTALLATION_NAME-->` | Replaced by the name, or by nothing on production | everywhere in the output |

Only the first `<head>` and the first `<title>` are touched, so inline SVG or code samples that contain these words are
left alone. The name is HTML escaped.

## Template operators

```
{installation_name()}                       {* staging *}
{installation_name( hide_on_prod=true() )}  {* empty on production *}
{if is_production_system()} ... {/if}
```

`installation_name( hide_on_prod )` returns the configured name, or an empty string on production when `hide_on_prod`
is true. `is_production_system()` is true when the name is in `ProductionInstallationList[]`.

Example: load analytics only in production.

```
{if is_production_system()}
    {include uri='design:page_head_analytics.tpl'}
{/if}
```

## Limits

- The check is by name: a typo in the list makes the live site look like staging.
- A page that has no `<head>` or `<title>` (a JSON answer) is not altered.
- The filter runs on every response that passes the `response/output` event.

## Related pages

- [A clean installation that says Exponential](clean-install-defaults.md)
- [Multi-site INI overrides](multi-site-ini-overrides.md), [INI override placements](../../specifications/6.0/ini-override-placements.md)
- [6.0.15 changelog](../../changelogs/6.0/6.0.15.md)
- [Chronicle: August 2026](../../history/2026/2026-08.md)
