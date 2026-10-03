# Installation name: never confuse staging with production again

When a team runs a development copy, a staging copy and the live site, the
browser tabs look the same. The installation name feature puts the installation's
name where you see it: in front of the page title on every installation that is
not production, in a comment in the page head, and wherever a template asks for
it. Added on 7 August 2026 (`cbfee1241e`, classes `ExpInstallationDetailsOutputFilter`
and `ExpInstallationOperator`).

## Settings

File `settings/site.ini`, block `[SiteSettings]`. The keys are not in the shipped
`site.ini`; add them to `settings/override/site.ini.append.php`.

| Key | Default | Scope | Meaning |
|---|---|---|---|
| `EzInstallationName` | not set | global | The name of this installation, for example `staging`. When empty or missing the feature does nothing. |
| `ProductionInstallationList[]` | not set | global | The names that count as production. If `EzInstallationName` is in the list, the installation is production. |

```ini
[SiteSettings]
EzInstallationName=staging
ProductionInstallationList[]
ProductionInstallationList[]=live
```

## Switch the output filter on

The filter is a `response/output` listener. Register it in `settings/override/site.ini.append.php`:

```ini
[Event]
Listeners[]=response/output@ExpInstallationDetailsOutputFilter::filter
```

The `[Event]` block is private API that may change (see the comment in
`settings/site.ini`). Without the listener the template operators below still work.
Clear the INI cache after the change (`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`).

## What it does to a page

| Where | Effect | When |
|---|---|---|
| Directly after the first `<head>` | An invisible comment `<!-- [I] staging -->` | always, when the name is set |
| The first `<title>` | Becomes `<title>[staging] ...` | only when the installation is **not** production |
| `<!--INSTALLATION_NAME-->` | Replaced by the name, or by nothing on production | everywhere in the output |

Only the first `<head>` and the first `<title>` are touched, so inline SVG or code
samples that contain these words are left alone. The name is HTML escaped.

## Template operators

```
{installation_name()}                       {* staging *}
{installation_name( hide_on_prod=true() )}  {* empty on production *}
{if is_production_system()} ... {/if}
```

`installation_name( hide_on_prod )` returns the configured name, or an empty
string on production when `hide_on_prod` is true. `is_production_system()` is
true when the name is in `ProductionInstallationList[]`. Use it to load analytics
only in production:

```
{if is_production_system()}
    {include uri='design:page_head_analytics.tpl'}
{/if}
```

## Limits

- The check is by name: a typo in the list makes the live site look like staging.
- A page that has no `<head>` or `<title>` (a JSON answer) is not altered.
- The filter runs on every response that passes the `response/output` event.

## Related

- Month page: [August 2026](../../history/2026/2026-08.md)
