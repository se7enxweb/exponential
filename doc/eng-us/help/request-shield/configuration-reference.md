---
title: "Request shield: configuration reference"
audience: admin, hosting, developer
category: administration
order: 45
summary: Every way to configure the request shield (exp_request_shield) in one place - where its configuration lives, the constants in config.php, requestshield.ini, the cronjob part, the files on disk, the rule file format, the order a request is checked in, every rule line and every set key with its default and effect, every shipped Exponential rule by ID, the expect line, the log line and the signed own requests.
keywords: request shield, exp_request_shield, configuration, reference, rule file, rules, set, settings, defaults, EXP_REQUEST_SHIELD_RULES, EXP_REQUEST_SHIELD_ROOT, EXP_REQUEST_SHIELD_VAR, EXP_REQUEST_SHIELD_DISABLED, requestshield.ini, AdminSettings, cronjob, host, trust, method, allow, restrict, block, unblock, query, limit, no-limit, challenge, challenge-exempt, exempt, deny, feed, ban, crawlers, post-origin, api-path, backend, cache-query, match, site, replace, include, expect, monitor, mode, pass-ttl, store, store-dir, log, log-level, log-ip, recheck, debug-header, EXP-PACE, EXP-IMAGES, EXP-SEARCHES, EXP-LOGIN, EXP-POST, X-Exp-Self, selfHeader
type: help
---
# Request shield: configuration reference

This page lists everything that configures the request shield (the extension `exp_request_shield`, which bundles the
request-shield library): the few settings outside the rule files, the rule file format, every rule line and every
`set` key with its default, and every rule the extension ships for Exponential. It is a reference to look things up
in. For worked, copy-paste examples read the [cookbook](cookbook.md); for installing the shield, the
[setup guide](../../guides/request-shield.md).

The tables follow request-shield 0.4.0, the version bundled with exp_request_shield 0.0.1.x. On the server,
`php extension/exp_request_shield/bin/request-shield vocabulary` prints the rule words of the installed version.

## Contents

1. [Where the configuration lives](#where-the-configuration-lives)
2. [The main rule file and the constants](#the-main-rule-file-and-the-constants)
3. [requestshield.ini and the cronjob part](#requestshieldini-and-the-cronjob-part)
4. [Files on disk](#files-on-disk)
5. [The rule file format](#the-rule-file-format)
6. [The order a request is checked in](#the-order-a-request-is-checked-in)
7. [Every rule line](#every-rule-line)
8. [Every set key](#every-set-key)
9. [The shipped Exponential rules](#the-shipped-exponential-rules)
10. [The built-in rule sets](#the-built-in-rule-sets)
11. [The expect line](#the-expect-line)
12. [The log line](#the-log-line)
13. [The installation's own requests](#the-installations-own-requests)
14. [Related pages](#related-pages)

## Where the configuration lives

The shield runs before the kernel starts, so it never reads an INI file. Its configuration is in these places:

| Where | What it configures | Who changes it |
|---|---|---|
| The **rule files** (`settings/request-shield/*.rules`) | everything the shield decides: hosts, paths, methods, budgets, the browser check, the log, the mode | you, on the server; the mode also from the administration |
| **Constants** in `config.php` | which rule file, which directories, the shield switched off | you, before the hook line |
| `var/request-shield/PAUSED` | the shield paused while the file exists | **Pause** / **Switch on** in the administration, or `touch` / remove |
| `settings/override/requestshield.ini.append.php` | only the administration pages (the dashboard's period, the log's page size) | you |
| `settings/override/cronjob.ini.append.php` | when the daily cronjob part `requestshield` runs | you |

## The main rule file and the constants

The shield reads one **main rule file**, which includes the others. It is the first of:

| Order | File | When |
|---|---|---|
| 1 | the file named by `EXP_REQUEST_SHIELD_RULES` | the constant is defined in `config.php` before the hook line |
| 2 | `settings/request-shield/request-shield.rules` | the site has its own file (recommended) |
| 3 | `extension/exp_request_shield/rules/request-shield.rules` | the extension's default, replaced by every update |

`php extension/exp_request_shield/bin/request-shield rules` prints the file in use and where it comes from; the
dashboard's setup box says the same.

The constants, defined in `config.php` **before** the hook line, only when the default does not fit:

| Constant | Default | Effect |
|---|---|---|
| `EXP_REQUEST_SHIELD_RULES` | see above | the main rule file, an absolute path |
| `EXP_REQUEST_SHIELD_ROOT` | two levels above the extension | the installation's root |
| `EXP_REQUEST_SHIELD_VAR` | `<root>/var/request-shield` | where the compiled settings (`cache/`), `PAUSED`, the backups of mode switches and the key for own requests (`cache/self.key`) live |
| `EXP_REQUEST_SHIELD_DISABLED` | not defined | `true`: the hook does nothing at all (restart Velocity after changing it) |

```php
// config.php, above the hook line: a rule file kept outside the installation
define( 'EXP_REQUEST_SHIELD_RULES', '/etc/exponential/www.example.org/request-shield.rules' );

if ( is_file( __DIR__ . '/extension/exp_request_shield/bootstrap/hook.php' ) )
    require __DIR__ . '/extension/exp_request_shield/bootstrap/hook.php';
```

## requestshield.ini and the cronjob part

`requestshield.ini` holds only settings of the administration pages. Override them in
`settings/override/requestshield.ini.append.php`:

| `[AdminSettings]` | Default | Effect |
|---|---|---|
| `DashboardHours` | `24` | the hours the dashboard sums up and draws, 1 to 168 |
| `DashboardRecent` | `15` | the newest log lines on the dashboard |
| `LogHours[]` | `1`, `6`, `24`, `72`, `168` | the periods offered on the log page |
| `LogPageSize` | `50` | log lines per page |
| `ShowRuleText` | `enabled` | show the text of the rule files on the rules page (`disabled` hides it, for example when a rule file names office addresses) |

The extension also brings the cronjob part **`requestshield`**, scheduled daily at 04:23
(`CrontabSchedule_requestshield=23 4 * * *` in its `cronjob.ini.append.php`). It runs
`request-shield feeds update` (the public blocklists the rules name with `feed`) and `request-shield crawlers update`
(the address lists search engines and AI crawlers publish), so that blocklists and crawler verification stay current.
Run it as the site's user; the kernel's crontab installer writes it into that user's crontab. Until the first run,
`feed` rules decide nothing.

## Files on disk

| Path | What | Written by |
|---|---|---|
| `extension/exp_request_shield/lib/requestshield/` | the library (and `php74/`, the same files for PHP 7.4) | the package |
| `extension/exp_request_shield/rules/` | the shipped rule files | the package, replaced by every update |
| `settings/request-shield/` | the site's main rule file and its copies of `exponential*.rules` | you, or the first mode switch in the administration |
| `var/request-shield/cache/` | the compiled settings; `self.key`, the key of the installation's own requests | the shield |
| `var/request-shield/store/` | counters (without APCu), the secret of the pass cookie, lists, feeds, statistics | the shield (path from `set store-dir`) |
| `var/request-shield/log/shield.log` | one line per decision, rotated at `log-max-size` | the shield (path from `set log`) |
| `var/request-shield/backup/` | the previous rule file of each mode switch | the administration |
| `var/request-shield/PAUSED` | pauses the shield while it exists | you, or **Pause** |
| `var/log/requestshield.log` | every switch made in the administration, with the user's login | the administration |

All of `var/request-shield/` must be writable by the user the site's PHP runs as, and must never be served by the
web server (a standard Exponential virtual host refuses `var/` except the storage paths it serves on purpose). Files
the shield makes are `0600` and folders `0700` unless `set file-mode` and `set dir-mode` say otherwise. Run the command
line as the site's user (`sudo -u <web user> php extension/exp_request_shield/bin/request-shield ...`), or files made
by root will be unreadable for the site.

## The rule file format

```
ids SITE                                          # this file's rule IDs start with SITE-
version 2026-11-15.1                              # shown by check and on the rules page

include exponential-admin-uri.rules               # another file, relative to this one

[SITE-OLD] block /old/**                          # a rule with an ID; the comment is its description
expect GET /old/page   404                        # an example, decided by "request-shield test"
expect GET /oldies     answered                   # a near miss that must pass
```

- **One rule per line**: a word, then values separated by spaces. `#` starts a comment at the start of a line or
  after a space (`\#` is a literal `#`). The comment after a rule is its **description**, shown on the rules pages
  and when an example fails.
- **`[ID]`** before a rule names it: letters, digits, `-`, `_`, `.`. The log, `trace` and the dashboard name the rule
  by it (`rule=SITE-OLD`). A rule without an ID is named by file and line (`request-shield.rules:12`). An ID used twice
  is an error. `[ID@n]` is a revision; as a reference (`unblock [SCAN-BACKUP@1]`) it names the revision you reviewed,
  and `check` warns when a library update changes that rule.
- **`ids <PREFIX> [required]`** at the top: every ID in the file must start with `<PREFIX>-`; `required` makes an ID
  mandatory for every rule.
- **`include <file or glob>`** reads further files, relative to the including file. An include may **not** leave its
  directory with `..`: this is why the site's main file has copies of `exponential*.rules` next to it. `include @name`
  reads a built-in set (`@wordpress`, `@tracking`, ...).
- **Paths** are simple patterns, matched against the path without the query:

  | Pattern | Matches | Does not match |
  |---|---|---|
  | `/wp-admin/**` | `/wp-admin`, `/wp-admin/x/y.php` | `/wp-adminx` |
  | `/page/*` | `/page/about` (`*` stays in one segment) | `/page/a/b` |
  | `*.sql` | `/dump.sql`, `/a/b/dump.sql` (no leading `/`: anywhere) | `/dump.sqlx` |
  | `**/admin/**` | `/admin`, `/shop/admin/x` (in any directory) | `/administrator` |
  | `/file?.txt` | `/file1.txt` (`?` is one character) | `/file12.txt` |

  `regex` switches the rest of the line to regular expressions without delimiters
  (`block regex (?i)^/(phpmyadmin|adminer)`); `(?i)` makes one case-insensitive. Every pattern is compiled when the
  files are read, so a broken one is an error then, never a silent miss. URI siteaccesses stand in front of the path
  (`/ger/content/search`): write `**/content/search` or a regex with `(/[a-z0-9_]+)?` in front where they matter.
- **`none`** as the first value empties a list first, defaults included (`exempt none 192.0.2.1`).
- **`match <paths> { ... }`** groups the rules of one area: inside, rules apply to those paths only, and a `limit`
  counts only the requests there. `match regex ... { }` takes a regular expression.
- **`site <names> { ... }`** adds rules for some websites (host names, `*.example.org`, `default`). The website is
  picked by the web server's server name (`set site-from server-name`, the default), so a visitor cannot choose
  another website's rules with a `Host` header. **After the first `site` block, only further `site` blocks,
  `include`, `ids` and `version` may follow**, and server-wide lines (`trust`, `store-dir`, `secret`, `ban`, `feed`,
  `set recheck` ...) may not stand inside one. Use absolute paths for `store-dir` and `log` in a file with site
  blocks.
- **Later lines win** for `set` keys: put the site's own settings **after** the `include` of the shipped rules, which
  set a mode, a store and a log of their own.
- **Changes need no restart.** Every server notices a changed rule file within `recheck` seconds (10 by default) and
  compiles it again. A file with a mistake never takes the site down: the last good settings stay in force (or, at
  the very first compile, the shield lets everything through) and PHP's error log names the file and line once a
  minute.

## The order a request is checked in

`request-shield trace <main.rules> "GET https://www.example.org/path"` shows these steps for one request, each with
its result; the first refusal ends the request:

| # | Step | Rule lines that decide it |
|---|---|---|
| 1 | Kept out | `deny`, the deny list |
| 2 | Public lists | `feed` |
| 3 | Banned | `ban` |
| 4 | Kind of request | `method` |
| 5 | Size | `set max-uri`, `max-query-parameters`, `max-header-bytes` |
| 6 | Disguised address | built in: encodings, `..`, paths out of the website's folder |
| 7 | Website name | `host` |
| 8 | Addresses only attackers ask for | `block`, `unblock`, the built-in scanner paths |
| 9 | Where forms may be sent | `allow` |
| 10 | Where forms come from | `post-origin` |
| 11 | Areas for certain visitors | `restrict` |
| 12 | Known crawlers | `crawlers`, `crawler` |
| 13 | Known parameters | `query` |
| 14 | Attack patterns | `block query`, `block header`, `@attacks` |
| 15 | May a cache keep the answer? | `cache-path`, `cache-query`, `cache-ignore` |
| 16 | Pace (one line per budget) | `limit`, `no-limit` |
| 17 | Browser check | `challenge`, `challenge-exempt`, `api-path` |

An `exempt` address skips the budgets and the browser check, but blocked paths and attack patterns still refuse it.
A request with a valid `X-Exp-Self` signature (the installation's own request) skips the shield entirely.

## Every rule line

Defaults are those of the library; where the shipped Exponential rules set something else, the column "Shipped" says
so.

| Line | Default | Shipped | Effect |
|---|---|---|---|
| `host <names>` | any host | not set: write it in the site's file | the site's host names; a request for any other name gets 404 |
| `trust <addresses or ranges>` | nobody | not set | who may send `X-Forwarded-For`, `-Proto` and `-Host`: the load balancer, reverse proxy or CDN. From anyone else these headers are removed before the site runs. `X-Forwarded-For` is read right to left up to the first address no trusted proxy vouches for |
| `method <METHODS>` | `GET HEAD POST OPTIONS` | adds `PUT PATCH DELETE` (admin rules) | the methods allowed at all; any other: 405 |
| `allow <METHODS> <paths>` | everywhere | `EXP-POST`, `EXP-ADMIN-POST`, `EXP-ADMIN-SPA` | those methods only at those paths, anywhere else 405. Several `allow` lines for one method add up |
| `restrict <paths> to <addresses or ranges>` | open | `SITE-DASH` (`/rs/**`) | only those addresses may open the paths, others 403. In monitor mode a restrict refuses nobody |
| `block <paths>` | the built-in scanner and devtools paths | `EXP-SYSVIEW`, `EXP-MODULES`, `EXP-INTERNAL`, `EXP-INI`, `EXP-VIEWPARAMS` | 404 before the site sees the request |
| `unblock <pattern>` / `unblock [ID]` / `unblock @set` | | | takes a block back: one pattern, one rule, a whole set |
| `unblock [<ID>] at <paths> [for <addresses>]` | | `EXP-SYSVIEW-OK`, `EXP-DOWNLOAD`, `EXP-ADMIN-SYS` | a block lifted at some paths only (and only for some addresses) |
| `block query\|header <Name>\|headers\|anywhere <regex>` | | `@attacks` | an attack pattern in the query or the headers: 403. Values are decoded and normalized first. Form bodies are not looked at |
| `query <name> <type> ... [at <paths>]` | | `EXP-SEARCH-Q`, `EXP-ADMIN-Q` | the query parameters the site takes and their types: `int`, `number`, `word`, `id`, `list`, `text`, `any` or `/regex/`; `*` for every name. Only `text` values and unknown parameters are scanned for attacks |
| `query strict` | off | `monitor` (`EXP-STRICT`) | any parameter no `query` line names: 404 |
| `query drop [at <paths>]` | off | | unknown parameters are removed instead, the request goes on without them (GET and HEAD) |
| `cache-path <paths>` | `any` | | what a cache may keep |
| `cache-query <names>` | `any` | `none` (`EXP-CACHE-Q`) | the parameters a cached address may have; a request with others is answered but never kept (`allow-uncached`) |
| `cache-ignore <names>` / `cache-ignore @tracking` | | | parameters no cache key holds, removed before the cache and the site run |
| `limit <name> <n>/<unit> [challenge-at <n>] [on-demand] [on-exceeded challenge] [at <paths>]` | `requests 600/min` | `no-limit requests`; `EXP-PACE`, `EXP-IMAGES`, `EXP-SEARCHES` | a budget per visitor (an IPv4 address or an IPv6 /64). Units `s`, `sec`, `min`, `hour`, `day`, also `20/10s`. Past `challenge-at` the browser check; past the limit 429 with `Retry-After`, or with `on-exceeded challenge` the check that, once solved, starts the counter again. `at <paths>` or a `match` block: only requests there count. Each budget needs its own name |
| `no-limit <name>` | | `requests` | switches a budget off, the default one too |
| `challenge [<METHODS>] <paths> [max-age <duration>]` | | `EXP-LOGIN`, `EXP-FORMS`, `EXP-ADMIN-CHECK` | always the browser check there, once per pass; `max-age 15m`: a pass from the last 15 minutes. With methods first, only those (`challenge POST **`: every form sent) |
| `challenge-exempt <paths>` | | | never the browser check there (feeds, webhooks, APIs for programs); budgets past their limit answer 429 instead |
| `api-path <paths>` | | `EXP-API`, `EXP-ADMIN-BG` | a check there comes as JSON (429 with the header `Request-Shield-Challenge`), never as a page. Requests that ask for JSON or are a page's background fetch get JSON anyway |
| `post-origin same [missing check\|allow\|refuse] [except <paths>]` | off | `EXP-ORIGIN`, `EXP-ORIGIN-X` | forms (POST, PUT, PATCH, DELETE) only from the website's own pages: `Origin`, else `Referer`; another website 403; neither header: the browser check (`missing check`, the default) |
| `post-origin except <paths>` | | `EXP-ORIGIN-X` | paths left out (payment callbacks, webhooks) |
| `backend <paths>` | | `EXP-ADMIN-AREA` | the editors' area, counted apart in the statistics |
| `exempt <addresses or ranges> [until <day>[T<hh:mm>]]` | `127.0.0.1 ::1` | | never counted and never checked; still refused for blocked paths and attack patterns |
| `deny <addresses or ranges> [until <day>[T<hh:mm>]]` | | | 403 before every other check |
| `feed <name> [<url> \| from <file>] deny\|check\|count\|ban-signal <n> [at <paths>]` | | `spamhaus-drop deny`, `feodo deny` | a public blocklist, fetched by the cronjob part; above the site blocks |
| `ban after <n> <signal> in <time> for <time>` | | `monitor` (`EXP-BAN`: 10 refusals in 10 minutes, 1 hour) | a visitor past `<n>` signals (`refusals`, `limits`, `checks`, or a budget's name) is answered only 429 for a while; repeated bans grow (`ban-growth`, `ban-max`) |
| `crawlers <kind> allow\|check\|block` | `allow` | all four kinds `allow` | what verified crawlers get, by kind: `search`, `ai-search`, `ai-user`, `ai-training`. `allow`: never the browser check (only the budgets, answered with a pause); `check`: like any visitor; `block`: 403 |
| `crawler <ID> allow\|check\|block` | | | the same for one crawler (`CRAWL-GPTBOT`, ...) |
| `crawler <kind> ua /<pattern>/ [dns <suffixes>] [ranges <lists>]` | | | a crawler of your own, verified by DNS or an address list |
| `monitor <rule>` | | `EXP-BAN`, `EXP-STRICT` | before `block`, `restrict`, `allow`, `limit`, `challenge`, `ban`, `feed`, `query strict`: the rule is logged as it would decide (`monitor-...`), not enforced |
| `replace [<ID>] <rule>` | | | swaps a rule in one line, keeping its ID and its count in the log. Write the replacement complete: a rule from inside a `match` block needs its `at <paths>` (or its own `match` block), or it counts everywhere |
| `expect ...` | | about 150 | an example for `request-shield test`, see [below](#the-expect-line) |
| `match <paths> { ... }` / `site <names> { ... }` | | | see [the format](#the-rule-file-format) |
| `ids`, `version`, `include` | | | see [the format](#the-rule-file-format) |
| `dashboard-access "<principal>"\|* sha256:<hash> [until <day>]` | | | who may open the library's own pages below `/rs` with a token (`request-shield access-token`); keep it in a file only the owner can read |
| `stats-group "<name>" <websites>`, `stats-skip <paths>` | | | statistics per customer; paths not counted when they pass |
| `plugin <class> [from <file>]` | | | a plugin told about every decision |
| `set <key> <value>` | | | see [every set key](#every-set-key) |

## Every set key

`${NAME}` in a value is an environment variable, `${NAME:-default}` with a default.

| Key | Default | Shipped | Effect |
|---|---|---|---|
| `mode` | `enforce` | `enforce` | `off` (nothing is decided), `monitor` (everything logged as it would be decided, nobody refused), `enforce`, `strict` (enforce, and the rules marked strict) |
| `recheck` | `10` (seconds) | | how often the rule files are checked for changes; `0`: on every request |
| `store` | `auto` | | where counters live: `auto` (APCu when usable, else files), `apcu`, `file`, `memory`. Use `file` under Velocity and with several PHP-FPM pools, so all workers share one count |
| `store-dir` | `.request-shield/store` next to the main file | `var/request-shield/store` | counters without APCu, the secret, lists, feeds, statistics. Relative to the rule file |
| `secret` | made in `store-dir` | | the key of the pass cookie, at least 32 characters. Several servers behind one load balancer need the same one: `set secret ${SHIELD_SECRET}` |
| `pass-ttl` | `1h` | `2h` (admin host: `8h`) | how long a solved browser check lasts |
| `solution-ttl` | `5m` | | how long a solution may take to come back |
| `difficulty-min`, `difficulty-max` | `50000`, `500000` | | the work of the browser check; doubled per solve within an hour, up to the maximum |
| `cookie`, `solution-cookie` | `rsp`, `rss` | | the cookie names |
| `bind-user-agent` | `on` | | the pass holds only for the browser it was solved in |
| `debug-header` | `off` | | `on`: every answer carries `X-RS: <decision>; rule=<ID>` (for testing; switch it off again) |
| `strip-untrusted-forwarded` | `on` | | removes `X-Forwarded-*` from `$_SERVER` when the sender is not trusted |
| `search-engines` | `on` | | `off`: no crawler is recognized |
| `crawler-verify` | `both` | | `ranges` (only the published address lists, no DNS on the request path), `dns`, `both` |
| `dns-lookups` | `30` a minute | | DNS lookups to verify crawlers, all requests together; `0`: none |
| `app-challenge` | `off` | | `on`: the site may ask for the check with the response header `X-RS-Check: 1` |
| `ipv6-prefix` | `64` | | the IPv6 prefix one visitor is counted under |
| `max-uri`, `max-query-parameters`, `max-header-bytes` | `4096`, `64`, `16384` | | size limits; past them the request is refused before the site runs |
| `language` | `auto` | | the language of the shield's own pages: the visitor's browser language among those there are texts for (English and German built in), or a code |
| `text.<key>`, `text.<lang>.<key>` | | | your own texts for the check page and the error pages (`set text.en.title One moment, please`) |
| `home` | none | `/` | where the shield's own pages link to, "To the home page" |
| `challenge-logo` | a shield | | an SVG file of your own in the middle of the check page |
| `error-page` | the shield's page | | a page of your own for a status: `set error-page 404 errors/404.html`, `4xx` for all; placeholders `{status}` `{title}` `{text}` `{wait}` `{home}` `{lang}` `{reference}` |
| `widget-path`, `widget-difficulty` | off, `25000` | | the browser check inside a form, solved while the visitor types |
| `log` | none | `var/request-shield/log/shield.log` | the log file; without it the dashboard's chart stays empty |
| `log-level` | `stop` | `flag` | `stop`: refused, slowed, checked; `flag`: also requests let through uncached (unknown parameters, POSTs) and solved checks; `all`: every request (briefly only); `off` |
| `log-ip` | `masked` | | `masked`: the visitor's network (IPv4 /24, IPv6 /48), never one person's address; `full`: the address (personal data: say so in the privacy notice) |
| `log-max-size` | `10M` | | then one rotation to `shield.log.1` |
| `file-mode`, `dir-mode` | `0600`, `0700` | | the modes of what the shield writes; for a group: `0640` / `02750` |
| `stats` | `off` | `off` | the library's statistics below `/rs/stats/` (`on`, or parts) |
| `dashboard-path` | `/rs` | | where the library's own pages live |
| `dashboard-session` | `8h` | | how long a token login to those pages lasts |
| `api`, `api-write`, `api-origins` | `on`, `off`, none | `api off` | the JSON API below `/rs/api/v1` |
| `live`, `live-keep` | `off`, `1h` | | the live view with full addresses (APCu or files) |
| `lists-dir` | `<store-dir>/lists` | | where `allow.rules` and `deny.rules` of the command line live |
| `feeds-max-age` | `3d` | | an older fetched blocklist is not used |
| `ban-growth`, `ban-max`, `ban-keep` | `2`, `1d`, `memory` | | each ban within a day this many times as long, at most this long; `file`: bans survive a restart of APCu |
| `site-from` | `server-name` | | which name picks a `site` block: the web server's server name, or `host` (the `Host` header) |
| `cache-unknown-query` | `uncached` | | a parameter `cache-query` does not name: `uncached`, or `hit-only` |
| `http-cache`, `http-cache-*` | `off` | | the library's own HTTP cache; Exponential sites use the kernel's HTTP cache instead |
| `stats-hours`, `stats-days`, `stats-months`, `stats-flush`, `stats-hosts`, `stats-depth`, `stats-slow`, `stats-path`, `crawler-log*` | | | details of the statistics |

The library's own reference has each with a longer explanation:
[settings](https://github.com/cjw-network/request-shield/blob/main/docs/reference/settings.md).

## The shipped Exponential rules

The default main file includes `exponential-admin-uri.rules`, which includes `exponential.rules`. Every rule has an ID;
change one in the site's main file with `replace [ID] ...`, take it back with `unblock [ID]` or `no-limit <name>`, and
never edit the copies, so they can be refreshed.

**The frontend** (`exponential.rules`):

| ID | Rule | What it does |
|---|---|---|
| (settings) | `include @tracking @attacks @devtools`, `set pass-ttl 2h`, `set home /` | marketing parameters known, attack patterns refused, build files and debug pages refused |
| `EXP-SYSVIEW` | `block regex (?i)/content/view(/\|$)` | `/content/view/...`, pages by node number, 404 in the frontend |
| `EXP-SYSVIEW-OK` | `unblock [EXP-SYSVIEW] at **/content/view/sitemap/* **/content/view/tagcloud/*` | the sitemap and the tag cloud stay open |
| `EXP-MODULES` | `block regex (?i)^(/(ger\|eng))?/(setup\|class\|section\|role\|...\|pdf)(/\|$)` | admin modules, 404 in the frontend; **name your URI siteaccesses** instead of `ger\|eng` |
| `EXP-INTERNAL` | `block regex ^/(settings\|kernel\|lib\|bin\|autoload\|cronjobs\|update\|tests\|doc\|var/[^/]+/(cache\|log))(/\|$)` | the installation's own folders |
| `EXP-INI` | `block *.ini *.ini.append *.ini.append.php *.tpl` | settings and template sources, anywhere |
| `EXP-DOWNLOAD` | `unblock [SCAN-BACKUP@1] at **/content/download/** /var/*/storage/**` | uploaded archives (`.zip`, `.sql` ...) stay downloadable |
| `EXP-VIEWPARAMS` | `block regex (?i)/\((offset\|limit\|year\|month\|day)\)/(?![0-9]{1,6}(/\|$))` | view parameters must be numbers up to 6 digits |
| `EXP-POST` | `allow POST **/user/login **/content/action ... /api/**` | POST only where Exponential takes a form; **add your extensions' modules** |
| `EXP-LOGIN` | `challenge **/user/login **/user/register **/user/forgotpassword **/user/password/** max-age 15m` | the login and registration: the browser check first |
| `EXP-FORMS` | `challenge **/content/action **/content/tipafriend/** **/comment/add/** **/newsletter/subscribe/** max-age 30m` | contact forms, comments, newsletter |
| `EXP-ORIGIN` | `post-origin same` | forms only from the website's own pages |
| `EXP-ORIGIN-X` | `post-origin except **/paypal/notify_url/** **/xrowpaypal/notify/**` | payment callbacks |
| `EXP-API` | `api-path **/ezjscore/** /api/**` | ezjscore calls and the REST API get a check as JSON |
| `EXP-SEARCHES` | `limit searches 10/min on-exceeded challenge` (in `match regex (?i)/content/(advanced)?search(/\|$)`) | ten searches a minute, then the check |
| `EXP-SEARCH-Q` | `query SearchText text ... SearchDate /-1\|[1-5]/ ...` | the search form's parameters and their types |
| `EXP-CACHE-Q` | `cache-query none` | a cache keeps addresses without a query only |
| `EXP-SEARCH-CHECK` | `challenge **/content/search **/content/advancedsearch max-age 1h` | commented out: take the `#` away while a search is attacked |
| `EXP-PACE` | `limit pages 300/min challenge-at 120 on-exceeded challenge at regex ^(?!...images...)` | the pace: the check past 120 pages a minute and again past 300, image addresses left out |
| `EXP-IMAGES` | `limit images 3000/min on-exceeded challenge at regex ^(...content/image/\|/index_image.php\|...storage/images...)` | image aliases made on demand: 3000 a minute |
| `EXP-BAN` | `monitor ban after 10 refusals in 10m for 1h` | a scanner an hour off the site; watched only |
| `EXP-STRICT` | `monitor query strict` | unknown query parameters: 404; watched only |

**The admin as `/admin`** (`exponential-admin-uri.rules`): `EXP-ADMIN-BG` (`api-path /admin/user/megamenu/**`),
the `method` line with `PUT PATCH DELETE`, `EXP-ADMIN-SPA` (`allow PUT PATCH DELETE /admin/explayouts_ui_api/**`, the
layout editor), and in `match /admin/**`: `EXP-ADMIN-SYS` (system URLs and admin modules open), `EXP-ADMIN-POST`
(`allow POST`), `EXP-ADMIN-CHECK` (`challenge`: the browser check once per pass), `EXP-ADMIN-AREA` (`backend`),
`EXP-ADMIN-Q` (`query * text`) and the commented `EXP-ADMIN-NET` (`restrict to <office>`).

**The admin on a host of its own** (`exponential-admin-host.rules`): the same as `EXP-AH-*` rules in
`site admin.example.org { ... }`, with `set pass-ttl 8h` for editors.

**The default main file** (`request-shield.rules`): `SITE-ADMIN-*` for the other admin siteaccesses (`/adminui`,
`/editor`, `/admin2` to `/admin4l`, `/admintest_*`), `include @wordpress`, the `crawlers` lines, the two `feed` lines,
the paths below `var/request-shield/`, `set stats off`, `SITE-DASH` (`restrict /rs/** to 127.0.0.1 ::1`) and
`set api off`.

## The built-in rule sets

| Set | IDs | Read |
|---|---|---|
| scanners | `SCAN-HIDDEN`, `SCAN-BACKUP`, `SCAN-TEST`, `SCAN-DBTOOL`, `SCAN-CGI`, `SCAN-CONFIG`, `SCAN-SECRETS`, `SCAN-KEYS` | always (`unblock @scanners` takes them back) |
| devtools | `DEV-BUILD`, `DEV-APPCONFIG`, `DEV-DEBUG`, `DEV-APIDOCS`, `DEV-SOURCE` | always |
| crawlers | `CRAWL-GOOGLE`, `CRAWL-GPTBOT`, ... | always: the known crawlers and their address lists |
| `@wordpress` | `WP-FOLDERS`, `WP-SCRIPTS` | `include @wordpress`: `/wp-admin`, `/wp-login.php`, `xmlrpc.php` ... 404 |
| `@tracking` | `TRACK-UTM`, `TRACK-GOOGLE`, `TRACK-META`, ... | `include @tracking`: `utm_*`, `gclid`, `fbclid` ... are known parameters |
| `@attacks` | `ATK-SQL-*`, `ATK-XSS-*`, `ATK-LFI`, `ATK-JNDI`, ... | `include @attacks`: attack patterns in the query and headers, 403 |

## The expect line

```
expect <METHOD> <address> <outcome> [by <ID>] [from <address>] [with pass] [times <n>] [ua "<User-Agent>"] [header <Name>:<value>]...
```

| Part | Meaning |
|---|---|
| `<address>` | a path with its query, or a full URL when the host matters (`https://admin.example.org/...`), encoded as a browser sends it |
| `passes` | answered, and a cache may keep it |
| `uncached` | answered, never kept |
| `answered` | either: the site answers (for near misses that must not be refused) |
| `check` | the browser check (page or JSON) |
| `json-check` | the check as JSON only |
| `403`, `404`, `405`, `429` ... | refused with that status |
| `by <ID>` | the rule that must decide; without it, the last rule with an ID above the line in the same file |
| `from <address>` | the visitor's address; default `198.51.100.7` |
| `with pass` | the visitor holds a solved check |
| `times <n>` | sent `n` times in a row; the outcome is the last one's |
| `ua "..."`, `header Name:value` | a User-Agent, extra headers (several allowed) |

`php extension/exp_request_shield/bin/request-shield test <main.rules>` decides every example on a fresh store in
memory: nothing is counted, logged or sent. Rules marked `monitor` and `set mode monitor` are tested **as enforced**,
so the examples can be written before a rule is switched on (`--as-written` tests them as they are). `--only=<ID>`
tests one rule; `--junit=<file>` writes a report for CI. Exit code 0: all pass; 1: one fails; 2: a mistake in the
files.

## The log line

```
2026-11-15T10:32:48+00:00 198.51.100.0/24 challenge 429 "always" rule=EXP-LOGIN "GET https://www.example.org/user/login" "Mozilla/5.0 ..."
2026-11-15T10:34:33+00:00 203.0.113.0/24 reject 404 "blocked path" rule=EXP-MODULES "GET https://www.example.org/setup/info" "curl/8.5"
2026-11-15T10:35:02+00:00 198.51.100.0/24 allow-uncached 200 "challenge solved" "GET https://www.example.org/user/login" "Mozilla/5.0 ..."
```

The fields: time, the visitor's network (or address with `log-ip full`), the decision, the status, the reason, then
optional `rule=`, `claimed=` (a crawler name that was not verified), `wait=` (seconds of a pause), `ref=` (the
reference shown on the error page), the request with its full address, and the User-Agent.

| Decision | Meaning | Dashboard group |
|---|---|---|
| `reject` | refused (403, 404, 405, 414 ...) | refused |
| `throttle` | a pause, 429 with `Retry-After` (a budget past its limit, a ban) | refused |
| `challenge` | the browser check was sent (429 is the status of the check page; a real browser solves it by itself) | checked |
| `allow-uncached` | let through, but no cache may keep it (POSTs, unknown parameters, a solved check) | other |
| `monitor-...` | what a watched rule, or monitor mode, would have done; the visitor got in | watched |

## The installation's own requests

Since exp_request_shield 0.0.1.5, requests the installation sends to its own pages (Setup > Preload sites, the address
checks after an upgrade, static publishing) carry a signature and pass the shield before it runs:

- The header is `X-Exp-Self: <unix time>.<signature>`, the signature an HMAC-SHA256 over the time, the method, the
  host (without port) and the path with its query, made with `var/request-shield/cache/self.key` (64 hexadecimal
  characters, readable by the site's user only, made on first use).
- It holds for **120 seconds** and only for the address and method it was made for. Anyone else's request is decided
  as always; the key never leaves the server.
- PHP code of the installation makes one with `expRequestShield::selfHeader( $url, $method )`, which returns the
  header line or `null` (no key, or an address without a host). The class needs nothing from the kernel. See
  [the cookbook](cookbook.md#sign-your-own-scripts-requests).
- The kernel's preloader (`expPreloadRunner`) and the upgrade's smoke check send it whenever the extension is installed
  (Exponential 6.0.15 from 2026-11-14 on).

## Related pages

- [Request shield: cookbook](cookbook.md): recipes with copy-paste rules and their examples
- [Request shield: troubleshooting](troubleshooting.md)
- [Request shield: the rules](the-rules.md): the rules page of the administration
- [Setting up the request shield](../../guides/request-shield.md)
- [The request shield](../../features/6.0/request-shield.md) (feature reference)
- The library's reference: [rule files](https://github.com/cjw-network/request-shield/blob/main/docs/reference/rule-files.md),
  [settings](https://github.com/cjw-network/request-shield/blob/main/docs/reference/settings.md),
  [command line](https://github.com/cjw-network/request-shield/blob/main/docs/reference/cli.md)
