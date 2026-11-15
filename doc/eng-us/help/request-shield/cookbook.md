---
title: "Request shield: cookbook"
audience: admin, hosting, developer
category: administration
order: 47
summary: Copy-paste recipes for the request shield (exp_request_shield), each with the rule lines, the expect examples that prove them and how to check them - host names and siteaccesses, the search, downloads and PDFs, help PDFs, login and registration, forms of your own extensions, the REST API, the administration, webhooks, monitoring and cron, signing your own scripts' requests, image-heavy pages, page aliases, Cloudflare, nginx, LiteSpeed and Apache in front, several servers, shared hosting, monitor mode rollout, bans, deny lists, own texts and error pages, reading the log, testing and updating the rule copies.
keywords: request shield, exp_request_shield, cookbook, recipes, examples, rules, search, PDF, downloads, exphelp, login, register, REST API, webhooks, cron, monitoring, uptime, exempt, X-Exp-Self, selfHeader, preloader, images, image aliases, EXP-IMAGES, EXP-PACE, EXP-SEARCHES, page alias, unblock, Cloudflare, CF-Connecting-IP, X-Forwarded-For, trust, nginx, LiteSpeed, mod_remoteip, load balancer, secret, shared hosting, APCu, monitor mode, enforce, ban, deny, error page, log, tuning, expect, test, trace, replay
type: help
---
# Request shield: cookbook

Recipes for the request shield, each a few lines to copy into the site's main rule file,
`settings/request-shield/request-shield.rules`, with `expect` examples that prove them. Every rule and example on this
page was run through `request-shield test` against the rules shipped with exp_request_shield 0.0.1.x.

Every word used here is explained in the [configuration reference](configuration-reference.md). The site's own
file and how to make it: [Setting up the request shield](../../guides/request-shield.md#2-give-the-site-its-own-rules).

## Contents

1. [How to use a recipe](#how-to-use-a-recipe)
2. [Name the site's hosts and siteaccesses](#name-the-sites-hosts-and-siteaccesses)
3. [Protect the search](#protect-the-search)
4. [Downloads and PDFs](#downloads-and-pdfs)
5. [Help PDFs and the help search](#help-pdfs-and-the-help-search)
6. [Login and registration](#login-and-registration)
7. [Forms of your own extensions](#forms-of-your-own-extensions)
8. [The REST API](#the-rest-api)
9. [The administration](#the-administration)
10. [Webhooks](#webhooks)
11. [Cron jobs, monitoring and uptime checks](#cron-jobs-monitoring-and-uptime-checks)
12. [Sign your own scripts' requests](#sign-your-own-scripts-requests)
13. [Image-heavy pages and busy intranets](#image-heavy-pages-and-busy-intranets)
14. [Page aliases that look like system paths](#page-aliases-that-look-like-system-paths)
15. [Print layouts and keyword lists](#print-layouts-and-keyword-lists)
16. [Query parameters of an extension](#query-parameters-of-an-extension)
17. [Behind Cloudflare, a CDN or a load balancer](#behind-cloudflare-a-cdn-or-a-load-balancer)
18. [nginx, Apache, LiteSpeed and Velocity in front](#nginx-apache-litespeed-and-velocity-in-front)
19. [Several web servers](#several-web-servers)
20. [Shared hosting](#shared-hosting)
21. [Roll out with monitor mode](#roll-out-with-monitor-mode)
22. [Ban scanners for an hour](#ban-scanners-for-an-hour)
23. [Keep an address out, or let one in](#keep-an-address-out-or-let-one-in)
24. [Your own texts and error pages](#your-own-texts-and-error-pages)
25. [Read the log and tune](#read-the-log-and-tune)
26. [Test rules with expect lines](#test-rules-with-expect-lines)
27. [After an update of the extension](#after-an-update-of-the-extension)

## How to use a recipe

1. Copy the lines into `settings/request-shield/request-shield.rules`, **after** the line
   `include exponential-admin-uri.rules` (or `-host.rules`) and before any `site` block. Replace the documentation
   addresses (`192.0.2.0/24`, `203.0.113.10`) and names (`www.example.org`) with your own. Keep the lines the file
   got from the extension's default, above all `[SITE-DASH] restrict /rs/** to 127.0.0.1 ::1` and `set api off`,
   which keep the library's own pages below `/rs` closed.
2. Keep an ID on every rule (`[SITE-...]`), so the log and the dashboard name it, and keep the `expect` lines: they are
   the proof that the rule does what it says, now and after the next update.
3. Check and test, as the user the site's PHP runs as:

   ```bash
   php extension/exp_request_shield/bin/request-shield check settings/request-shield/request-shield.rules
   php extension/exp_request_shield/bin/request-shield test  settings/request-shield/request-shield.rules
   ```

4. Nothing to restart: every server reads the changed file within 10 seconds. Watch **Setup > Request shield > Log**
   for a while.

A recipe that **replaces** a shipped rule (`replace [EXP-...]`) changes its numbers, so the shipped examples written
for the old numbers now fail in `test`, for example `expected uncached, got check by EXP-SEARCHES`. That is expected:
the examples next to your `replace` line are the ones that count. `test --only=<ID>` tests one rule.

## Name the site's hosts and siteaccesses

The first recipe for every site: only the site's names are answered, and the admin modules are refused behind every
URI siteaccess the site has (the shipped rule knows only `/ger` and `/eng`).

```
host        www.example.org example.org admin.example.org
replace     [EXP-MODULES] block regex (?i)^(/(site|ger|eng|fre))?/(setup|class|section|role|state|workflow|trigger|visual|package|infocollector|url|ezinfo|collaboration|pdf)(/|$)   # admin modules in the frontend: 404, behind every URI siteaccess of this site
expect GET /fre/setup/info                         404 by EXP-MODULES
expect GET /shop/package                           answered   # a page alias that only names a module
```

Keep `www.example.org` in the `host` line even when it is not your name: the shipped examples send it as their
`Origin`, and it is a reserved documentation domain no visitor uses.

## Protect the search

A search is the most expensive page there is. The shipped budget allows ten searches a minute per visitor, then the
browser check (`EXP-SEARCHES`). A slower search server, five a minute:

```
replace     [EXP-SEARCHES] limit searches 5/min on-exceeded challenge at regex (?i)/content/(advanced)?search(/|$)   # five searches a minute, then the check
expect GET /content/search?SearchText=yoga  times 5   uncached
expect GET /content/search?SearchText=yoga  times 6   check by EXP-SEARCHES
expect GET /news  times 40   answered   # reading pages does not use it up
```

Write the `at regex ...` part: the shipped rule stands inside a `match` block, and a `replace` without `at` would count
**every** request against the search budget.

While the search is attacked by many addresses at once, check every searcher once an hour:

```
[SITE-SEARCH-CHECK] challenge **/content/search **/content/advancedsearch max-age 1h   # under attack: every search checked
expect GET /content/search?SearchText=yoga                 check
expect GET /content/search?SearchText=yoga with pass       uncached
```

While it is in the file, `test` reports the shipped search examples as `check`. Remove the rule again when the attack
is over.

## Downloads and PDFs

Files sent by `content/download` (PDFs, office files, archives) are rendered by PHP and attract crawlers that page
through every one. A budget of their own, then the check:

```
match /content/download/** {
  [SITE-DOWNLOADS] limit downloads 30/min on-exceeded challenge             # downloads: 30 a minute per visitor, then the check
  expect GET /content/download/12/34/file/report.zip  times 30   answered
  expect GET /content/download/12/34/file/report.zip  times 31   check
}
expect GET /news  times 40   answered
```

Or the browser check before every PDF download, once an hour (a real browser solves it in about a second; a crawler
that only sends a search engine's name does not):

```
[SITE-PDF-CHECK] challenge **/content/download/**/*.pdf max-age 1h          # every PDF download: the browser check first
expect GET /content/download/12/34/file/report.pdf               check
expect GET /content/download/12/34/file/report.pdf with pass     answered
expect GET /content/download/12/34/file/photo.zip                answered
expect GET /content/download/12/34/file/report.pdf ua "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)" from 66.249.66.1   answered   # the real Googlebot: never the check
```

A crawler verified by its operator's published addresses never gets the check (`crawlers ... allow`); past a budget it
gets a pause (429 with `Retry-After`), which crawlers understand. Files in `var/storage/` that the web server sends
itself never reach PHP, so no rule sees them.

## Help PDFs and the help search

The help viewer's PDF export and search (`/exphelp/pdf/...`, `/exphelp/search`) build their answer for each request.
When the help is readable without a login, give them a budget:

```
match regex (?i)^(/[a-z0-9_]+)?/exphelp/(pdf|search)(/|$) {
  [SITE-HELP-HEAVY] limit helpheavy 10/min on-exceeded challenge            # help PDFs and the help search: 10 a minute, then the check
  expect GET /exphelp/pdf/guides  times 10   answered
  expect GET /exphelp/pdf/guides  times 11   check
}
```

## Login and registration

The shipped `EXP-LOGIN` asks for the browser check before the login, the registration and the password forms, and
accepts a pass from the last 15 minutes. Five minutes:

```
replace     [EXP-LOGIN] challenge **/user/login **/user/register **/user/forgotpassword **/user/password/** max-age 5m   # a pass from the last five minutes
expect GET /user/login                            check by EXP-LOGIN
expect GET /user/login with pass                  answered
```

Ten registrations an hour per visitor, then the check again:

```
match /user/register/** {
  [SITE-REGISTER] limit registrations 10/hour on-exceeded challenge         # the registration form: ten an hour per visitor
  expect POST /user/register header Origin:https://www.example.org with pass times 10   answered
  expect POST /user/register header Origin:https://www.example.org with pass times 11   check
}
```

Exponential's own lockout after failed logins (`site.ini [UserSettings] MaxNumberOfFailedLogin`, off by default)
works behind the shield as before; the shield keeps bots from reaching the form at all.

## Forms of your own extensions

`EXP-POST` allows POST only where Exponential takes forms; a form of your own extension at `/myforms/send` answers
405. Allow its module, keep forms from other websites out (the shipped `EXP-ORIGIN` already does), and ask for the
check once per half hour:

```
[SITE-MYFORMS-POST]  allow POST **/myforms/**                               # the forms of the extension myforms
[SITE-MYFORMS-CHECK] challenge POST **/myforms/** max-age 30m               # a form sent: the browser check first, once per half hour
expect POST /myforms/send header Origin:https://www.example.org with pass   answered
expect POST /myforms/send header Origin:https://evil.example                403 by EXP-ORIGIN
expect POST /news/2026/fit-and-healthy                                      405 by EXP-POST
```

A form that a visitor sends without a pass gets the check page and is sent again by itself afterwards; nothing typed
is lost. A form with file uploads needs the check inside the form instead (`set widget-path`, see the library's
[browser check in the form](https://github.com/cjw-network/request-shield/blob/main/docs/features/RSF03-03-browser-check-in-the-form.md)).

## The REST API

The REST API (`/api/ezp/v2/...`) changes content with PUT, PATCH and DELETE, and its clients are programs that run no
JavaScript. Allow the methods there only, never send the browser check (a client past its budget gets 429 with
`Retry-After`), and give the API a budget of its own:

```
method      GET HEAD POST OPTIONS PUT PATCH DELETE
[SITE-REST-METHODS] allow PUT PATCH DELETE /api/**                          # the REST API changes content with PUT, PATCH and DELETE
expect DELETE /api/ezp/v2/content/objects/57                     answered
expect DELETE /news/2026/fit-and-healthy                         405 by EXP-ADMIN-SPA   # elsewhere still 405 (named by the first allow rule for DELETE)
[SITE-REST-NOCHECK] challenge-exempt /api/**                                 # API clients run no JavaScript: never the check, only the pause
[SITE-REST-BUDGET]  limit api 300/min at /api/**                            # the API: 300 requests a minute per client, then 429
expect GET /api/ezp/v2/content/objects/57  times 300   answered
expect GET /api/ezp/v2/content/objects/57  times 301   429
```

The API's own authentication (OAuth2, session, basic) is unchanged; the shield decides before it. A partner's server
with a fixed address can be exempted from the budget with `exempt <address>`.

## The administration

**Only from the office or the VPN** (403 for everyone else):

```
[SITE-ADMIN-NET] restrict /admin/** to 192.0.2.0/24 2001:db8:10::/48 198.51.100.7   # the administration: office, VPN, and the examples' test address
expect GET /admin/dashboard from 203.0.113.9          403
expect GET /admin/dashboard from 192.0.2.44 with pass answered
```

`198.51.100.7` is the address `test` sends its examples from (a documentation range, never a real visitor's); without
it in the list, the shipped admin examples fail with 403. In monitor mode a `restrict` refuses nobody. With the admin
on a host of its own, write `restrict /** to ...` inside its `site` block.

**Never the browser check for the office** (it is still refused for blocked paths and attack patterns):

```
exempt      192.0.2.0/24                                                     # the office: never counted, never checked
```

**One check per working day** for editors: `set pass-ttl 8h` (on a shared host for visitors too; in a `site` block for
the admin host only, as `exponential-admin-host.rules` does).

**Other admin siteaccesses** (`/adminui`, `/editor`, `/admin4` ...) are covered by the default main file's `SITE-ADMIN-*`
rules. An admin siteaccess with another name needs the same block:

```
match regex ^/(backoffice)(/|$) {
  [SITE-BO-SYS]   unblock [EXP-SYSVIEW] [EXP-MODULES]                       # system URLs and admin modules
  expect GET /backoffice/content/view/full/2 with pass   answered
  [SITE-BO-POST]  allow POST                                                # editors save everywhere
  expect POST /backoffice/content/edit/87/1/eng-GB with pass   answered
  [SITE-BO-CHECK] challenge                                                 # the browser check once per pass
  expect GET /backoffice/dashboard                       check
  [SITE-BO-Q]     query * text                                              # every parameter known, free text scanned
  expect GET /backoffice/content/edit/87?anything=goes with pass   answered
}
```

## Webhooks

A payment provider or a repository posts to the site from its servers: no page of the site, no browser.

```
[SITE-HOOK-POST]    allow POST /webhook/**                                  # payment and repository webhooks post here
[SITE-HOOK-ORIGIN]  post-origin except /webhook/**                          # posted by a server, not from a page of the site
[SITE-HOOK-NOCHECK] challenge-exempt /webhook/**                            # a server solves no browser check
expect POST /webhook/payments header Origin:https://pay.example.net   answered
expect POST /webhook/payments                                         answered
```

Check the webhook's signature in the receiving module as before; the shield only lets the request reach it.

## Cron jobs, monitoring and uptime checks

- **Cron jobs** (`runcronjobs.php`, `ezcache.php`, `exp:` console commands) run on the command line and never pass the
  shield. Nothing to configure.
- **An uptime monitor** that asks a page every minute from a fixed address: exempt it, with an end date so it does not
  stay forever:

  ```
  exempt      203.0.113.10 until 2027-12-31                                  # the uptime monitor: never counted, never checked
  expect GET /news  from 203.0.113.10 times 500                        answered
  expect GET /settings/site.ini  from 203.0.113.10                     404 by EXP-INTERNAL   # still refused for internal files
  ```

- **A monitor without fixed addresses** (a cloud service): let it ask a plain page with `HEAD` or `GET` at most once a
  minute; that stays far below the pace. Never point it at the login or the search.
- **The server itself** (`127.0.0.1`, `::1`) is exempt by default.

## Sign your own scripts' requests

The installation's preloader, the upgrade check and static publishing sign their requests (`X-Exp-Self`) and pass the
shield. A script of your own that runs on the same installation can do the same, for example a sitemap warmer:

```php
<?php
// php bin/php/ezexec.php extension/mysite/scripts/warm.php
$url = 'https://www.example.org/news';
$header = expRequestShield::selfHeader( $url, 'GET' );   // "X-Exp-Self: <time>.<signature>", or null
$ch = curl_init( $url );
curl_setopt_array( $ch, array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => $header ? array( $header ) : array(),
) );
$body = curl_exec( $ch );
```

- Make a new header for every request: it holds two minutes and only for that method and address (path and query).
- Outside the kernel, `require 'extension/exp_request_shield/classes/exprequestshield.php';` first; the class needs
  nothing else.
- The key is `var/request-shield/cache/self.key`, readable by the site's user only. Run the script as that user.
- A tool on another machine cannot sign (the key never leaves the server): exempt its address instead.

Measured on a live site: the login asked without the header got the browser check (429, `rule=EXP-LOGIN`), the same
request with it got 200.

## Image-heavy pages and busy intranets

Exponential 6.0.15 makes image aliases on demand: a page with hundreds of new images asks one address per image. The
shipped `EXP-IMAGES` allows 3000 a minute per visitor; a gallery site with pages of thousands of new images:

```
replace     [EXP-IMAGES] limit images 6000/min on-exceeded challenge at regex ^((/[a-z0-9_]+)?/content/image/|/index_image\.php|/var/([^/]+/)?storage/images(-versioned)?/)   # a gallery site: 6000 image addresses a minute
expect GET /content/image/abc/x.jpg  times 5000     answered
```

An intranet where a whole office shares one address needs a faster pace (the shipped `EXP-PACE`: the check past 120
pages a minute):

```
replace     [EXP-PACE] limit pages 600/min challenge-at 240 on-exceeded challenge at regex ^(?!(/[a-z0-9_]+)?/content/image/|/index_image\.php|/var/([^/]+/)?storage/images(-versioned)?/)   # an intranet: twice the pace
expect GET /news  times 240                        answered
expect GET /news  times 241                        check by EXP-PACE
```

Keep the `at regex ...` of both, so images and pages keep counting apart. Better still for an office: `exempt` its
address.

## Page aliases that look like system paths

A page alias that starts like a folder or module the shield refuses (`/doc/...`, `/url/...`) gets 404. Lift the block
for exactly that path, with an example that the block still stands elsewhere:

```
[SITE-DOC]   unblock [EXP-INTERNAL] at /doc /doc/**                          # /doc is a content section here, not the installation's doc/ folder
expect GET /doc/getting-started                      answered
expect GET /settings/site.ini                        404 by EXP-INTERNAL
[SITE-ALIAS] unblock [EXP-MODULES] at /url /url/**                           # the section "url" (a page alias), not the url module
expect GET /url/how-short-links-work                 answered
expect GET /setup/info                               404 by EXP-MODULES
```

`check` then warns that blocked paths are open there for everyone; for a content section that is intended.

## Print layouts and keyword lists

Two rules from a live Exponential site. Scanners wrap their paths in `/layout/set/print/...`, and each cost a full page
before its 404; readers rarely use the print layout:

```
[SITE-LAYOUT-SET] challenge **/layout/set/** max-age 12h                     # another layout of a page: the browser check first
expect GET /layout/set/print/news/article            check
expect GET /layout/set/print/news/article with pass  answered
```

Keyword pages (`/content/keyword/<word>`) are lists without a cache that crawlers page through:

```
match regex (?i)/content/keyword(/|$) {
  [SITE-KEYWORDS] limit keywords 20/min on-exceeded challenge               # keyword lists: 20 a minute per visitor, then the check
  expect GET /content/keyword/Demo%20Website  times 20   answered
  expect GET /content/keyword/Demo%20Website  times 21   check
}
```

## Query parameters of an extension

The shipped `EXP-STRICT` watches unknown query parameters (`monitor query strict`): the log shows
`monitor-reject 404 "unknown parameter"`. Name your extension's parameters with their types, so that their values are
also no longer scanned as free text:

```
[SITE-SHOP-Q] query sort word  page int  color list at /shop/**              # the shop's filter parameters
expect GET /shop/shirts?sort=price&page=2&color=red,blue   answered
expect GET /shop/shirts?page=abc                            404 by EXP-STRICT   # a page that is no number
```

Types: `int`, `number`, `word`, `id`, `list`, `text`, `any`, or `/regex/`. Once the log shows no more
`unknown parameter` lines from real visitors, enforce it: `replace [EXP-STRICT] query strict`.

## Behind Cloudflare, a CDN or a load balancer

Behind a proxy, every request seems to come from the proxy's address: all visitors share one budget, the log shows one
network, and the pace refuses everyone at once. Name the proxy with `trust`; the shield then reads the visitor's
address from `X-Forwarded-For`, and only from there (anyone else's header is removed):

```
# Cloudflare's ranges, from https://www.cloudflare.com/ips/ (check them now and then)
trust       173.245.48.0/20 103.21.244.0/22 103.22.200.0/22 103.31.4.0/22 141.101.64.0/18 108.162.192.0/18 190.93.240.0/20 188.114.96.0/20 197.234.240.0/22 198.41.128.0/17 162.158.0.0/15 104.16.0.0/13 104.24.0.0/14 172.64.0.0/13 131.0.72.0/22
trust       2400:cb00::/32 2606:4700::/32 2803:f800::/32 2405:b500::/32 2405:8100::/32 2a06:98c0::/29 2c0f:f248::/32
[SITE-ADMIN-NET] restrict /admin/** to 192.0.2.0/24 198.51.100.7
expect GET /admin/dashboard from 162.158.1.1 header X-Forwarded-For:192.0.2.44   check by EXP-ADMIN-CHECK   # through Cloudflare, from the office: on to the check
expect GET /admin/dashboard from 162.158.1.1 header X-Forwarded-For:203.0.113.9  403        # through Cloudflare, from elsewhere
expect GET /admin/dashboard from 198.51.100.50 header X-Forwarded-For:192.0.2.44 403        # the header from anyone else: not believed
```

- A load balancer of your own: `trust 10.0.0.0/8` (its network), nothing else.
- When the web server already puts the visitor's address into `REMOTE_ADDR` (Apache `mod_remoteip`, nginx
  `real_ip`, LiteSpeed "Use Client IP in Header"), the shield sees the visitor directly and needs no `trust` line.
  Configure only one of the two.
- **How to tell:** read the log's second column. If nearly every line shows the same network, the proxy is not
  trusted yet.

## nginx, Apache, LiteSpeed and Velocity in front

- **nginx in front of Apache on the same server**: every request comes from `127.0.0.1`, which is **exempt** by
  default, so nothing is counted or checked at all. Trust it:

  ```
  trust       127.0.0.1 ::1                                                   # nginx in front, on the same server
  expect GET /news  from 127.0.0.1 header X-Forwarded-For:203.0.113.9 times 121   check by EXP-PACE   # the visitor counts, not the proxy
  expect GET /news  from 127.0.0.1 times 500                                  answered   # the proxy itself stays exempt
  ```

  and let nginx send `proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;`.
- **nginx behind Cloudflare** with the address resolved by nginx itself:
  `set_real_ip_from <each Cloudflare range>;` and `real_ip_header CF-Connecting-IP;` in the `http` block; then no
  `trust` line.
- **Apache behind Cloudflare**: `RemoteIPHeader CF-Connecting-IP` and one `RemoteIPTrustedProxy <range>` per
  Cloudflare range (`mod_remoteip`); then no `trust` line.
- **LiteSpeed and OpenLiteSpeed**: "Use Client IP in Header" set to "Trusted IP Only", with the proxy's addresses
  marked as trusted in the server's access control. On shared LiteSpeed hosting ask the hoster which applies.
- **Exponential Velocity** ends TLS itself and passes the client's address to PHP; no `trust` line unless another
  proxy stands in front. Rule changes need no restart; adding the hook does (`./console exp:velocity restart`). Use
  `set store file` so all workers share one count.

## Several web servers

Behind a load balancer with two or more web servers:

- **One secret** for the pass cookie, or a visitor who solved the check on one server is checked again on the next:
  `set secret ${SHIELD_SECRET}` with the same value (at least 32 characters) in every server's environment, or one
  `store-dir` on a shared file system.
- **Counters** are per server with APCu (`store auto`): a visitor spread across two servers gets twice the budget.
  Halve the numbers, or `set store file` with `store-dir` on a shared file system.
- **Rule files** the same on every server (deploy them with the code); each server compiles its own copy.

## Shared hosting

The shield runs on any hosting with PHP 7.4 or newer:

- Without APCu it keeps its counters in files (tier S1, about 25 microseconds a request). `check` prints the tier.
- Keep `store-dir` and `log` inside the installation (`var/request-shield/...`), so `open_basedir` allows them; the
  shipped paths already do.
- Without a writable directory (tier S0) the budgets, bans and the pass cookie switch off; the path and attack rules
  still work. `check` says what is off.
- If a deploy user and the web server's user differ, share a group: `set file-mode 0640`, `set dir-mode 02750`.

## Roll out with monitor mode

On a site with many own addresses, start by watching:

1. `set mode monitor` in the site's main file (or **Monitor** on the dashboard). Every decision is logged as it would
   be taken (`monitor-reject`, `monitor-challenge` ...); nobody is refused.
2. For a few days, while editors work and the search is used, read **Setup > Request shield > Log**, group
   **Watched**. Each line names its rule.
3. For each line that was a real visitor or editor, add an exception with an `expect` example (the recipes above),
   then `check` and `test`.
4. `set mode enforce` (or **Enforce**). Keep reading the log for a day.

A single new rule can be watched while everything else enforces: put `monitor` in front of it.

```
[SITE-OLDAPI] monitor block /old-api/**                                      # the old API: watched first
expect GET /old-api/v1/items                         404
```

`test` checks watched rules as if they were enforced, so the example is ready for the day `monitor` is taken away;
`test --as-written` tests them as they are now.

## Ban scanners for an hour

The shipped `EXP-BAN` (10 refusals in 10 minutes, an hour off the site) only watches. A ban of your own that enforces:

```
[SITE-BAN] ban after 20 refusals in 10m for 1h                               # enforced: a scanner is an hour off the site after 20 refusals
expect GET /settings/site.ini  times 20            404 by EXP-INTERNAL
expect GET /settings/site.ini  times 21            429 by SITE-BAN
```

Test it with `test --only=SITE-BAN --as-written`: without `--as-written`, `test` also enforces the watched `EXP-BAN`,
and the two bans count each refusal twice. Repeated bans within a day grow (`set ban-growth 2`, `set ban-max 1d`).

## Keep an address out, or let one in

From the command line, with an end date and a reason, without editing the rule file (the lists live in
`var/request-shield/store/lists/`, and every server reads them within its recheck):

```bash
php extension/exp_request_shield/bin/request-shield deny   settings/request-shield/request-shield.rules 203.0.113.0/24 --for=7d --reason="form spam"
php extension/exp_request_shield/bin/request-shield allow  settings/request-shield/request-shield.rules 192.0.2.10 --until=2026-12-31 --reason="partner import"
php extension/exp_request_shield/bin/request-shield lists  settings/request-shield/request-shield.rules
php extension/exp_request_shield/bin/request-shield unlist settings/request-shield/request-shield.rules 203.0.113.0/24
```

`deny` answers 403 before every other check; `allow` writes an `exempt` line (never counted, never checked). In a rule
file the same is `deny 203.0.113.0/24 until 2026-12-31`.

## Your own texts and error pages

```
set         text.en.title One moment, please                                 # the check page's title
set         text.de.title Einen Moment, bitte
set         error-page 404 errors/4xx.html                                   # a page of the site's own for the shield's 404
```

`errors/4xx.html` lies next to the rule file, up to 64 KB, read when the rules compile, with the placeholders
`{status}`, `{title}`, `{text}`, `{wait}`, `{home}`, `{lang}` and `{reference}`:

```html
<!doctype html><html lang="{lang}"><title>{title}</title>
<h1>{title}</h1><p>{text}</p><p><a href="{home}">Home</a> {reference}</p></html>
```

## Read the log and tune

The log is `var/request-shield/log/shield.log`, one line per decision
([the fields](configuration-reference.md#the-log-line)). **Setup > Request shield > Log** shows it by period and group.
On the server:

```bash
# which rules decided most today
grep "^$(date +%F)" var/request-shield/log/shield.log | grep -o 'rule=[A-Z0-9_.-]*' | sort | uniq -c | sort -rn | head
# which networks one rule stopped most
grep ' rule=EXP-PACE ' var/request-shield/log/shield.log | awk '{print $2}' | sort | uniq -c | sort -rn | head
# what the watched rules would have refused
grep ' monitor-' var/request-shield/log/shield.log | tail -20
# follow it live
tail -f var/request-shield/log/shield.log
```

To see why one request was decided, step by step:

```bash
php extension/exp_request_shield/bin/request-shield trace settings/request-shield/request-shield.rules "GET https://www.example.org/content/view/full/2"
```

```
  ✓ Website name                      "www.example.org" is one of this site's names
  ✕ Addresses only attackers ask for  refused: /content/view/…: pages by node number (only the admin needs them)  [EXP-SYSVIEW]
  ...
This visitor gets "not found" (404) — the site never sees it. Decided by EXP-SYSVIEW.
```

`--ip=<address>` and `--ua="<User-Agent>"` trace as another visitor. With `set debug-header on`, every answer carries
`X-RS: <decision>; rule=<ID>` to see in the browser's network tab; switch it off again afterwards.

Tuning, in short: a rule that refuses real visitors gets an exception (`unblock ... at`, `allow`, `query`); a budget
that checks real visitors gets bigger numbers with `replace`; an address that should never be checked gets `exempt`.
Each change with an `expect` example.

## Test rules with expect lines

Every rule should have at least one example that it decides, and one near miss that must pass:

```
[SITE-OLD] block /old/**                         # the old site's folder
expect GET /old/page                 404         # what it refuses
expect GET /oldies                   answered    # a near miss: must pass
```

```bash
php extension/exp_request_shield/bin/request-shield test settings/request-shield/request-shield.rules
php extension/exp_request_shield/bin/request-shield test settings/request-shield/request-shield.rules --only=SITE-OLD
php extension/exp_request_shield/bin/request-shield test settings/request-shield/request-shield.rules --junit=var/tmp/rules.xml
```

`test` lists the rules without an example at the end. Before enforcing a change, send real traffic through the rules:
the web server's access log (only its 2xx and 3xx lines count) or a session saved from the browser's network tab
as HAR:

```bash
php extension/exp_request_shield/bin/request-shield replay settings/request-shield/request-shield.rules /var/log/httpd/access_log --ip=log
php extension/exp_request_shield/bin/request-shield replay settings/request-shield/request-shield.rules session.har
```

It names every good request the rules would refuse, with the rule.

## After an update of the extension

The site's copies of `exponential.rules` and `exponential-admin-*.rules` in `settings/request-shield/` are written
**once**, when the site's file is made, and an update of the extension never changes them. After every update:

```bash
diff -u settings/request-shield/exponential.rules           extension/exp_request_shield/rules/exponential.rules
diff -u settings/request-shield/exponential-admin-uri.rules extension/exp_request_shield/rules/exponential-admin-uri.rules
```

If the copies are unchanged (all site changes live in the main file, as recommended), copy the new versions over;
otherwise take the changes in by hand. Then `check` and `test`. The extension's changelog says when a shipped rule
changed, for example the image budget in 0.0.1.4 and the layout editor's methods in 0.0.1.4.

## Related pages

- [Request shield: configuration reference](configuration-reference.md)
- [Request shield: troubleshooting](troubleshooting.md)
- [Request shield: monitor and enforce](monitor-and-enforce.md)
- [Setting up the request shield](../../guides/request-shield.md)
- The library's [Exponential use case](https://github.com/cjw-network/request-shield/blob/main/docs/use-cases/exponential.md),
  with the reason for every shipped rule
