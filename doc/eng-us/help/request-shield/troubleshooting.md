---
title: "Request shield: troubleshooting"
audience: admin, hosting, developer
category: administration
order: 70
summary: Symptoms of the request shield with their cause and fix - did not run for this page, rules cannot be read, everything passes, root-owned files, editors sent to the browser check, 429 for real visitors, 405 for forms and the API, preloaders and own scripts, a proxy or CDN in front, shared hosting, out-of-date rule copies, refused page aliases, test failures, an empty chart, 403 on /rs.
keywords: request shield, troubleshooting, problems, 405, method, preloader, X-Exp-Self, Cloudflare, proxy, trust, X-Forwarded-For, shared hosting, open_basedir, tier, crawler, PDF, replace, rule copies, did not run for this page, rules cannot be read, fails open, shield failed, permissions, owner, root, browser check, 429, page alias, EXP-MODULES, EXP-SYSVIEW, EXP-ORIGIN, www.example.org, empty chart, log-level, /rs, SITE-DASH, PAUSED, trace
type: help
---
# Request shield: troubleshooting

## Contents

1. [First aid](#first-aid)
2. [Symptoms, causes and fixes](#symptoms-causes-and-fixes)
3. [Real visitors get 429](#real-visitors-get-429)
4. [Forms, the API or the layout editor get 405](#forms-the-api-or-the-layout-editor-get-405)
5. [Preloaders, warmers and your own scripts](#preloaders-warmers-and-your-own-scripts)
6. [A proxy, CDN or second web server in front](#a-proxy-cdn-or-second-web-server-in-front)
7. [Shared hosting](#shared-hosting)
8. [Finding out why a request was decided](#finding-out-why-a-request-was-decided)

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
| The layout editor says "Something went wrong" when you discard a draft or save (405 by rule `methods` in the log) | the site's copy of the admin rules predates exp_request_shield 0.0.1.4 and allows only GET, HEAD, POST and OPTIONS | take `EXP-ADMIN-SPA` (or `EXP-AH-SPA` on an admin host) and its `method` line from the extension's `rules/exponential-admin-uri.rules` (or `-host.rules`) into the site's copy |
| Setup > Preload sites stops after about 120 pages with 429, or the check after `exp:upgrade` fails on `/user/login` with 429 | the pace limit or the login's browser check answered the server's own requests | update exp_request_shield to 0.0.1.5 or later: the kernel signs its own requests (`X-Exp-Self`) and the shield lets them pass |
| Real visitors get 429 | the browser check page (status 429, normal: the browser solves it), or a budget shared by many visitors | [Real visitors get 429](#real-visitors-get-429) |
| A form, the REST API or the layout editor answers 405 | the method is not allowed there (`EXP-POST`, `method`) | [Forms, the API or the layout editor get 405](#forms-the-api-or-the-layout-editor-get-405) |
| Nearly every log line shows the same network; or nothing is ever counted | a proxy or CDN in front that the rules do not `trust` | [A proxy, CDN or second web server in front](#a-proxy-cdn-or-second-web-server-in-front) |
| Crawlers get the browser check on PDFs or the search | a crawler that only sends a search engine's name is treated like any visitor; verified crawlers never get the check | nothing to fix; to welcome a crawler of your own, `crawler <kind> ua /<pattern>/ dns <suffixes>` |
| After `replace [EXP-SEARCHES] ...` every page counts against the search budget | the shipped rule stands inside a `match` block; the replacement was written without `at` | add `at regex (?i)/content/(advanced)?search(/\|$)` ([cookbook](cookbook.md#protect-the-search)) |
| `test` fails shipped examples after a `replace` (`expected uncached, got check by EXP-SEARCHES`) | the shipped examples were written for the old numbers | expected; your examples next to the `replace` count |
| A rule from a new extension release is missing on the site (for example `EXP-ADMIN-SPA`, `EXP-IMAGES`) | the copies in `settings/request-shield/` are written once and never updated by an update | compare them with `extension/exp_request_shield/rules/` ([cookbook](cookbook.md#after-an-update-of-the-extension)) |
| A page alias is refused (404 by `EXP-MODULES` or `EXP-SYSVIEW`) | the alias starts with a module name | add the alias as an exception with an `expect` example ([Monitor and enforce](monitor-and-enforce.md)) |
| `bin/request-shield test` fails five examples with `Origin: https://www.example.org` (403 by `EXP-ORIGIN`) after you named your hosts | the shipped examples use the documentation domain `www.example.org` | add `host www.example.org`: a reserved domain, never a real visitor's |
| The dashboard chart is empty | the rules name no log file, or `log-level off` | `set log ...`; `set log-level flag` |
| `/rs/...` answers 403 | the default rule `SITE-DASH` closes the library's own pages to all but the server | open them for your addresses or with a token ([the extension's dashboard.md](https://github.com/se7enxweb/exp_request_shield/blob/main/doc/dashboard.md)) |
| A mode button is disabled | the web server cannot write the rule file or its directory | make it writable, or change `set mode` on the server |
| Under Velocity, `X-RS: allow-uncached shield error` on refusals | a library whose inner steps changed: the extension fell back | update the extension |

## Real visitors get 429

First look at the log line (Setup > Request shield > Log, or `grep` the address' network in
`var/request-shield/log/shield.log`):

| Log line | What happened | What to do |
|---|---|---|
| `challenge 429 "always" rule=EXP-LOGIN` (or `EXP-FORMS`, `EXP-ADMIN-CHECK`) | the browser check page was sent; its status is 429. A browser with JavaScript and cookies solves it in a second and goes on | nothing. If the visitor stays on the page: JavaScript or cookies are blocked (a privacy add-on), which the page says |
| `challenge 429 "pages" rule=EXP-PACE` | more than 120 pages a minute from one address: often an office, a school or a mobile network behind one IPv4 address | `exempt` the office's address, or a faster pace ([cookbook](cookbook.md#image-heavy-pages-and-busy-intranets)) |
| `challenge 429 "images" rule=EXP-IMAGES` | a page with thousands of new images (aliases made on demand) | a bigger image budget ([cookbook](cookbook.md#image-heavy-pages-and-busy-intranets)) |
| `challenge 429 "searches" rule=EXP-SEARCHES` | more than ten searches a minute | normal for a person only when paging fast; raise the budget if the search is cheap |
| `throttle 429 "banned"` | a ban (`ban after ...`) | the visitor caused many refusals first; find those lines. A ban ends by itself (at most after `ban-max`); the library's lists page (`/rs/waf/lists`) can lift it earlier |
| the same visitor checked again and again | the pass is bound to the browser (`bind-user-agent on`), or the secret differs between servers | one secret on every server ([several web servers](cookbook.md#several-web-servers)) |
| everyone gets 429 at the same time | all visitors share one address: a proxy in front is not trusted | [a proxy in front](#a-proxy-cdn-or-second-web-server-in-front) |

While you look, **Monitor** on the dashboard lets everyone in and still logs what would have been decided.

## Forms, the API or the layout editor get 405

The log says `reject 405 "method"`. The shield allows POST only where Exponential takes forms (`EXP-POST`), and PUT,
PATCH and DELETE only for the layout editor's API (`EXP-ADMIN-SPA`).

- A form of your own extension: `allow POST **/<module>/**` ([cookbook](cookbook.md#forms-of-your-own-extensions)).
- The REST API: `method ... PUT PATCH DELETE` and `allow PUT PATCH DELETE /api/**`
  ([cookbook](cookbook.md#the-rest-api)).
- The layout editor's "discard draft" or "save" says "Something went wrong": the site's copy of the admin rules
  predates exp_request_shield 0.0.1.4; take `EXP-ADMIN-SPA` and its `method` line from the extension's
  `rules/exponential-admin-uri.rules` (or `EXP-AH-SPA` from `-host.rules`).

## Preloaders, warmers and your own scripts

The pace allows 120 pages a minute per address, and the login always asks for the browser check, which no script can
solve. So a crawler of your own is checked after 120 pages:

- **Setup > Preload sites, the upgrade's address check, static publishing**: they sign their requests (`X-Exp-Self`)
  since exp_request_shield 0.0.1.5 with the kernel from 2026-11-14 on. Update both.
- **A script of your own on the same installation**: sign it with `expRequestShield::selfHeader()`
  ([cookbook](cookbook.md#sign-your-own-scripts-requests)).
- **A tool on another machine** (a CDN's warm-up, an SEO crawler): `exempt <its address> until <day>`.
- A signed request still gets 429: the signature is older than two minutes (make one per request), was made for
  another address or method, or the script cannot read `var/request-shield/cache/self.key` (run it as the site's
  user).

## A proxy, CDN or second web server in front

| Setup | Symptom without the fix | Fix |
|---|---|---|
| Cloudflare or another CDN | every visitor seems to come from the CDN: they share one budget and get 429 together | `trust` the CDN's ranges, or let the web server set `REMOTE_ADDR` (`mod_remoteip`, nginx `real_ip`) ([cookbook](cookbook.md#behind-cloudflare-a-cdn-or-a-load-balancer)) |
| nginx in front of Apache on the same machine | every request comes from `127.0.0.1`, which is exempt: nothing is ever counted or checked | `trust 127.0.0.1 ::1` and `X-Forwarded-For` from nginx ([cookbook](cookbook.md#nginx-apache-litespeed-and-velocity-in-front)) |
| a load balancer and two web servers | visitors are checked again on the other server; budgets count twice as high | one `secret` for all, `store file` on a shared folder ([cookbook](cookbook.md#several-web-servers)) |
| LiteSpeed with Cloudflare | as for Cloudflare | "Use Client IP in Header" for trusted addresses, or `trust` |

The shield believes `X-Forwarded-For` only from a trusted address, so a visitor cannot pretend to be someone else by
sending the header.

## Shared hosting

| Symptom | Cause | Fix |
|---|---|---|
| `check` says tier S0: budgets, bans and the pass cookie are off | no writable directory for the store | make `var/request-shield/` writable for the site's PHP user |
| `check` says tier S1 | no APCu: counters in files | nothing to fix; about 25 microseconds a request |
| PHP's error log: `open_basedir restriction` from the shield | `store-dir` or `log` outside the allowed folders | keep both inside the installation (`var/request-shield/...`) |
| The deploy user and the web server's user cannot read each other's files | the shield writes `0600` | `set file-mode 0640`, `set dir-mode 02750`, a shared group |

## Finding out why a request was decided

The log names the rule. To see the decision step by step, on the server, as the web user:

```bash
php extension/exp_request_shield/bin/request-shield trace settings/request-shield/request-shield.rules "GET https://www.example.org/the/path"
```

With `set debug-header on` in the rule file, every answer carries an `X-RS` header with the decision; switch it off
again afterwards.
