---
title: request-shield and request rules
audience: hosting, admin
category: hosting
order: 50
summary: The three request layers a host can use with Exponential: request-shield, a firewall that decides before the kernel starts and is part of the default installation since 6.0.15 (exp_request_shield); the kernel's request classifier, which only classifies into security.log; and the kernel's request rules, which decide before a module view runs. How to install and switch each on, how they differ, and how a host can offer them.
keywords: request-shield, exp_request_shield, Setup > Request shield, WAF, web application firewall, request classifier, security.log, requestclassifier.ini, request rules, requestrules.ini, ezrequestrules.php, monitor mode, enforce, EXP_REQUEST_PREPEND_FILE, config.php hook, PAUSED, Velocity, system URLs, brute force, scanners
type: help
---
# request-shield and request rules

This page is for system administrators and security staff at a host. Three layers look at an Exponential request
before or while the kernel handles it, and they are often confused. This page says what each does, where it runs and
how a host switches it on. The facts about request-shield come from
[Security hardening: A shield in front](../../security-hardening.md#a-shield-in-front-request-shield); the classifier
from [Request logs](../../../features/6.0/request-logs.md); the request rules from
[Request rules](../../../bc/6.0/view_full_security.md).

## Contents

1. [The three layers](#the-three-layers)
2. [request-shield](#request-shield)
3. [The request classifier](#the-request-classifier)
4. [Request rules](#request-rules)
5. [Offering it to customers](#offering-it-to-customers)
6. [Checklist](#checklist)

## The three layers

| | request-shield | Request classifier | Request rules |
|---|---|---|---|
| What it does | **decides**: refuses, slows down, challenges, bans for a while, or only logs (monitor mode) | **classifies** only: probes, traversal, injection, attack tools, bursts of 404s | **decides** per module view: redirect, not found, access denied, sign in first, rewrite, log |
| Where it runs | before the kernel starts | inside the kernel, at the end of every request | inside the kernel, just before a module view runs |
| Part of Exponential | yes since 6.0.15: the extension `exp_request_shield` bundles it (library MIT, by the CJW Network), enforcing as installed | yes, on by default | yes; three rules ship defined and off |
| Settings | its rule files in `settings/request-shield/` | `settings/requestclassifier.ini` | `settings/requestrules.ini` |
| Its log | `var/request-shield/log/shield.log`, read in **Setup > Request shield** | `var/log/security.log` | as a rule's action decides |

A request the shield refuses never reaches the kernel, so it is not in `security.log`. In the shield's monitor mode the
same probe can be in both logs. The classifier's probe and injection rule names follow request-shield's names in lower
case, so a line in one can be found in the other.

## request-shield

[request-shield](https://request-shield.com) (source:
[cjw-network/request-shield](https://github.com/cjw-network/request-shield), by JAC Systeme GmbH / CJW Network, MIT
license) looks at every request before the kernel starts. Real visitors pass in microseconds; a refused request costs
about 30 µs instead of a rendered page.

### Part of the default installation

Since Exponential 6.0.15 a new installation has it. The root `composer.json` requires the extension
`exp_request_shield` (`se7enxweb/exp_request_shield`, GPL-2.0-or-later), which bundles the library (request-shield
0.4.0, plus a build of the same files for PHP 7.4; PHP 7.4 to 8.5) and the Exponential rules. One hook line in
`config.php` runs it before the kernel and before the HTTP cache's early exit, so cached pages are protected too:

```php
if ( is_file( __DIR__ . '/extension/exp_request_shield/bootstrap/hook.php' ) )
    require __DIR__ . '/extension/exp_request_shield/bootstrap/hook.php';
```

- **New installations** get the line from `config.php-RECOMMENDED`; the 7x site packages (`sevenx_multisite`,
  `cjw_multisite`) also activate the extension, which adds **Setup > Request shield** to the administration.
- **Upgraded installations** add the line to `config.php` themselves: an update never overwrites `config.php`
  (`./console exp:config:list` shows what is new in `config.php-RECOMMENDED`). Activate the extension
  (`ActiveExtensions[]=exp_request_shield`) for the admin pages.

The same hook covers both engines, and they give identical answers:

- **Apache or nginx with PHP-FPM**: `config.php` runs on every request, so the hook runs the shield right there. No
  `auto_prepend_file` is needed, so a host no longer has to set one per pool.
- **Velocity**: `config.php` runs once per worker, so the hook sets `EXP_REQUEST_PREPEND_FILE` itself and `index.php`
  runs the shield at the start of every request. There is no integration file to write by hand; restart Velocity
  after adding or removing the hook. Velocity answers pages from its own response cache before a worker starts, so the
  shield sees only what reaches a worker.

### Rules, modes and switching it off

The shield uses the site's own rule file, `settings/request-shield/request-shield.rules`, with copies of the
extension's `exponential*.rules` next to it, and the extension's `rules/request-shield.rules` until the site has its
own. Choose the administration's file there:

| File | When |
|---|---|
| `exponential.rules` | the frontend rules; included by one of the two below |
| `exponential-admin-uri.rules` | the administration is the siteaccess `/admin` on the same host |
| `exponential-admin-host.rules` | the administration has a host of its own |

The default rules also treat every other administration reached by a path like `/admin`: `/adminui`, `/editor`,
`/admin2` to `/admin4l` and `/admintest_*` keep their system URLs, modules and forms (since 0.0.1.1). For an
administration on a host of its own, the site's main file includes `exponential.rules` first, its own settings after
it (they win), and the copy of `exponential-admin-host.rules` last, with the site's admin host names in its `site`
line and without its own `include exponential.rules`
([configuration](https://github.com/se7enxweb/exp_request_shield/blob/main/doc/configuration.md)). The shield's own
JSON API stays off by default.

1. The rules **enforce as installed**. For a site with unusual addresses (page aliases that start with a module name,
   forms of its own extensions), switch to `set mode monitor` while you read the log, then back to `enforce`.
2. Adapt the site-specific lines: URI siteaccesses (`EXP-MODULES`), extension modules that take forms (`EXP-POST`),
   host names and load balancer (`host`, `trust`).
3. Check after every change, as the site's PHP user:

   ```bash
   php extension/exp_request_shield/bin/request-shield check settings/request-shield/request-shield.rules
   php extension/exp_request_shield/bin/request-shield test  settings/request-shield/request-shield.rules
   php extension/exp_request_shield/bin/request-shield trace settings/request-shield/request-shield.rules "GET https://www.example.com/user/login"
   ```

4. **Setup > Request shield** (in the System group, right after Log files; policies `requestshield/read` and
   `requestshield/manage`) shows the dashboard, the log and the rules, and has switches: pause, enforce, monitor, off.
5. To switch it off at once on every server, create `var/request-shield/PAUSED`; remove it to switch the shield on
   again. `set mode off` in the rules does the same for good.

The example rules hide system URLs and administration modules on the public site, refuse internal paths, put a
browser check before the sign-in and the administration, and ban an address for an hour after ten refusals in ten
minutes (verified search engines are never banned). The shield cannot tell a right password from a wrong one, and
does not see static files the web server serves itself: account lockout stays Exponential's job
(`MaxNumberOfFailedLogin`, two-step sign-in).

The feature: [Request shield](../../../features/6.0/request-shield.md); step by step:
[Request shield guide](../../../guides/request-shield.md); what changed for existing installations:
[Request shield (6.0.15)](../../../bc/6.0/request-shield.md).

## The request classifier

The kernel classifies every request once and writes what looks like an attack to `var/log/security.log` with category,
rule and confidence, whatever the status. Rule sets: `probes`, `traversal`, `injection`, `scanner`, `threshold`
(`Max404PerMinute=30` within `WindowSeconds=60`).

- It **never blocks**. Blocking belongs to the shield, the web server or a listener class registered in
  `[ClassifierSettings] Listeners[]` (none by default).
- Addresses are masked, the user agent is hashed and shortened; query values are kept in `security.log` as evidence,
  with secrets, e-mail and IP addresses masked.
- `Classification=disabled` switches it off; leave a rule set out of `RuleSets[]` to switch one off.

Reading and forwarding it: [Security log](../operations/security-log.md) and
[Request logs](../operations/request-logs.md).

## Request rules

The request rules decide just before a module view runs: *when* (typed as a system URL, full view, the user lacks a
policy, below a node, from a network) and *what* (redirect to the URL alias, not found, access denied, sign in first,
redirect, rewrite, log). Exemptions are policies, so they are granted in the role editor. Nothing changes until a rule
is listed in `RuleList[]`.

The common case, keeping anonymous visitors off `/content/view/full/<node>` while the URL alias keeps working, is one
shipped rule switched on per public siteaccess:

```ini
# settings/siteaccess/<public siteaccess>/requestrules.ini.append.php
[RequestRuleSettings]
RuleList[]
RuleList[]=system_url_full_view_to_alias
```

Leave the administration and editor siteaccesses alone: editors use system URLs there. Check and explain:

```bash
php bin/php/ezrequestrules.php -s <siteaccess> --list  --allow-root-user
php bin/php/ezrequestrules.php -s <siteaccess> --check --allow-root-user   # exit code 1 on problems, for CI
php bin/php/ezrequestrules.php -s <siteaccess> --uri=content/view/full/2 --allow-root-user
```

> **Warning**
> Rules restrict *how* allowed content may be reached; they are not a replacement for `content/read` limitations.
> An `ip` condition is only as trustworthy as the visitor address: behind a proxy, configure `TrustedProxies[]` and
> `ClientIpByCustomHTTPHeader` first ([TLS and HTTPS](tls-and-https.md#proxies-that-terminate-tls)).

## Offering it to customers

These are options a host can choose:

- request-shield enforcing on every site, as installed, with monitor mode for the first week on a site with unusual
  addresses, as part of a managed plan;
- the administration behind the shield's browser check or limited to the customer's network (`EXP-ADMIN-NET`);
- the system-URL rule on public siteaccesses as a default of the server template;
- a weekly look at `security.log` and the shield's log, with a report to the customer when a rule rises sharply.

## Checklist

- [ ] `exp_request_shield` installed and active; the site's rules in `settings/request-shield/`, never served.
- [ ] The hook line in `config.php` (an upgraded installation adds it itself); Velocity restarted after adding it;
      Setup > Request shield does not say "did not run for this page".
- [ ] `check` and `test` pass; `enforce`, or `monitor` only while the log is reviewed.
- [ ] `security.log` read weekly; the classifier left on.
- [ ] Request rules checked with `--check` on every siteaccess that uses them.
- [ ] Proxies trusted correctly before any address-based rule is relied on.

## Related pages

- [Security for hosts: overview](README.md)
- [Security hardening](../../security-hardening.md)
- [Restricting the administration](restricting-the-administration.md)
- [Security log](../operations/security-log.md)
- [HTTP cache: With request-shield in front](../../../bc/6.0/httpcache.md#with-request-shield-in-front)
- [Request shield](../../../features/6.0/request-shield.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
