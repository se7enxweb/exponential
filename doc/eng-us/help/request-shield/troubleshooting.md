---
title: "Request shield: troubleshooting"
audience: admin, hosting, developer
category: administration
order: 70
summary: Symptoms of the request shield with their cause and fix - did not run for this page, rules cannot be read, everything passes, root-owned files, editors sent to the browser check, 429 in the admin, refused page aliases, test failures with www.example.org, an empty chart, 403 on /rs.
keywords: request shield, troubleshooting, problems, did not run for this page, rules cannot be read, fails open, shield failed, permissions, owner, root, browser check, 429, page alias, EXP-MODULES, EXP-SYSVIEW, EXP-ORIGIN, www.example.org, empty chart, log-level, /rs, SITE-DASH, PAUSED, trace
type: help
---
# Request shield: troubleshooting

## Contents

1. [First aid](#first-aid)
2. [Symptoms, causes and fixes](#symptoms-causes-and-fixes)
3. [Finding out why a request was decided](#finding-out-why-a-request-was-decided)

## First aid

When you suspect the shield of refusing real visitors and need the site to work **now**: press **Pause** on the
dashboard, or create the file `var/request-shield/PAUSED` on the server. The shield then decides nothing, on every
server, from the next request. Find the cause in the log, fix it, then **Switch on**.

## Symptoms, causes and fixes

| Symptom | Cause | Fix |
|---|---|---|
| Dashboard: "The shield did not run for this page" | the hook is not in `config.php`, or Velocity was not restarted after adding it, or Velocity runs an `index.php` older than 6.0.15 | add the hook ([the guide](../../guides/request-shield.md#1-install-it-on-an-existing-site)); `./console exp:velocity restart` |
| "The rules cannot be read" | a mistake in a rule file | `bin/request-shield check <main rule file>` names the file and line |
| Everything passes in enforce mode; PHP's error log says the shield failed and let the request through | the shield fails open: a store or cache directory it cannot write | make `var/request-shield/` writable by the site's PHP user |
| Files in `var/request-shield/` owned by root, the site cannot read them | the command line was run as root | run `bin/request-shield` as the web user; fix the owner |
| An editor gets the browser check page in the administration | the administration asks for the browser check once per pass | allow JavaScript and cookies; for a fixed office address, `exempt <address>` in the rule file |
| Background requests in the administration fail with 429 | a background request without a pass gets the check as JSON | the page reloads itself; name other background paths with `api-path` |
| A page alias is refused (404 by `EXP-MODULES` or `EXP-SYSVIEW`) | the alias starts with a module name | add the alias as an exception with an `expect` example ([Monitor and enforce](monitor-and-enforce.md)) |
| `bin/request-shield test` fails five examples with `Origin: https://www.example.org` (403 by `EXP-ORIGIN`) after you named your hosts | the shipped examples use the documentation domain `www.example.org` | add `host www.example.org`: a reserved domain, never a real visitor's |
| The dashboard chart is empty | the rules name no log file, or `log-level off` | `set log ...`; `set log-level flag` |
| `/rs/...` answers 403 | the default rule `SITE-DASH` closes the library's own pages to all but the server | open them for your addresses or with a token ([the extension's dashboard.md](https://github.com/se7enxweb/exp_request_shield/blob/main/doc/dashboard.md)) |
| A mode button is disabled | the web server cannot write the rule file or its directory | make it writable, or change `set mode` on the server |
| Under Velocity, `X-RS: allow-uncached shield error` on refusals | a library whose inner steps changed: the extension fell back | update the extension |

## Finding out why a request was decided

The log names the rule. To see the decision step by step, on the server, as the web user:

```bash
php extension/exp_request_shield/bin/request-shield trace settings/request-shield/request-shield.rules "GET https://www.example.org/the/path"
```

With `set debug-header on` in the rule file, every answer carries an `X-RS` header with the decision; switch it off
again afterwards.
