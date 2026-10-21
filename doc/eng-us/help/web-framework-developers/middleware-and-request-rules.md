---
title: "Middleware: request rules, input and output listeners"
audience: web-framework-developer, developer
category: web-frameworks
order: 210
summary: What takes the place of middleware, before and after filters and request listeners in Exponential - request rules that allow, deny, redirect, rewrite or log after URL translation and policies, the request/input and response/output events that ezformtoken uses, siteaccess-level switches, and where each fits compared with a middleware pipeline.
keywords: middleware, filter, before filter, after filter, request listener, request rules, requestrules.ini, RuleList, Conditions, Action, ezpRequestRuleProvider, ezpRequestFactProvider, request/input, response/output, ezformtoken, SiteAccessRules, pipeline, web framework developer
type: help
---
# Middleware: request rules, input and output listeners

This page is for developers who put cross-cutting request logic into middleware (Laravel, Slim, Laminas, Express,
Django), before and after filters (Rails, CodeIgniter) or kernel listeners (Symfony). Exponential has no generic
middleware stack you add classes to. It has three places that together cover most middleware uses.

## Contents

1. [The three places](#the-three-places)
2. [Request rules](#request-rules)
3. [Input and output listeners](#input-and-output-listeners)
4. [Common middleware, translated](#common-middleware-translated)
5. [What has no equivalent](#what-has-no-equivalent)

## The three places

| Place | Runs | Can | Configured in |
|---|---|---|---|
| `request/preinput`, `request/input` listeners | before URL translation and routing | inspect the request, refuse it (as the CSRF check does), set up state | `site.ini [Event] Listeners[]` |
| Request rules | after URL alias translation and after the policies allowed the view | allow, not found, forbidden, login, redirect, redirect to alias, rewrite, log | `settings/requestrules.ini` |
| `response/preoutput`, `response/output` filters | after the pagelayout rendered | change the final output (as the form token insertion does) | `site.ini [Event] Listeners[]` |

The order inside a request is on [The request lifecycle](the-request-lifecycle.md).

## Request rules

Rules are settings: conditions and an action, asked in order, the first whose conditions all match decides
([Request rules](../../features/6.0/request-rules.md), complete guide with sixteen recipes:
[View full security](../../bc/6.0/view_full_security.md)).

```ini
# settings/siteaccess/<site>/requestrules.ini.append.php
<?php /*
[RequestRuleSettings]
RuleList[]=members_area

[Rule-members_area]
Description=Below node 864, anonymous visitors sign in first and come back.
Conditions[subtree]=864
Conditions[user]=anonymous
Action=login
*/ ?>
```

- 25 conditions, among them `requested_via` (system, alias, wildcard, front page), `uri`, `module_view`, `view_mode`,
  `node`, `subtree`, `class`, `section`, `state`, `user`, `role`, `policy`, `siteaccess`, `host`, `header`, `ip` and
  `fact`.
- 8 actions: `allow`, `notfound`, `forbidden`, `login`, `redirect`, `redirect_to_alias`, `rewrite`, `log`.
- Rules only take access away or send the visitor elsewhere; they never give access a policy refused.
- Any answer except `allow` is sent `private, no-store`.
- Check what the rules decide without changing anything: `php bin/php/ezrequestrules.php -s site --uri=content/view/full/2`.

In PHP you can add conditions, actions, facts (`ezpRequestFactProvider`) and whole rule sets computed at run time
(`ezpRequestRuleProvider`), registered in `[RequestRuleSettings]`
([Extending the rules in PHP](../../bc/6.0/view_full_security.md#10-extending-the-rules-in-php)).

## Input and output listeners

`ezformtoken` is the model of a middleware pair in Exponential. Its `settings/site.ini.append.php` registers:

```ini
[Event]
Listeners[]=request/input@ezxFormToken::input
Listeners[]=response/output@ezxFormToken::output
```

`ezxFormToken::input()` checks the token of every POST before routing; `ezxFormToken::output()` adds the hidden token
field to every `method="post"` form in the final HTML. A filter listener such as `response/output` must return the value
it receives, changed or not ([Events and listeners](events-and-listeners.md)).

## Common middleware, translated

| Middleware | In Exponential |
|---|---|
| Authentication required for an area | siteaccess `RequireUserLogin`; a request rule with `Action=login`; view `functions` |
| Authorization | policies on the view (`functions`), object checks in the view ([Authorization](authorization-roles-and-policies.md)) |
| CSRF | `ezformtoken` ([Forms and CSRF](forms-and-csrf-protection.md)) |
| IP allow list for an area | a request rule with `Conditions[ip]` |
| Redirect old addresses | URL wildcards or a request rule with `redirect` ([Pretty URLs](url-aliases-and-wildcards.md)) |
| Maintenance mode | `expMaintenance` in `index.php` (`php bin/php/maintenance.php on`) ([Maintenance mode](../../features/6.0/maintenance-mode.md)) |
| Security headers, Content Security Policy | `site.ini [ContentSecurityPolicy]` and the security headers ([Content Security Policy](../../features/6.0/content-security-policy.md)) |
| Response caching | the view cache, the role-aware HTTP cache, the Velocity response cache ([Caching](caching-layers.md)) |
| Request logging | request logs and the audit trail ([Request logs](../../features/6.0/request-logs.md), [Audit trail](../../features/6.0/audit-trail.md)) |
| Trusted proxies | `site.ini [HTTPHeaderSettings] TrustedProxies[]` ([Trusted proxies](../../bc/6.0/trusted-proxies.md)) |

## What has no equivalent

- **Per-route middleware lists** (`->middleware('auth')` on one route): use view `functions` and request rules with a
  `module_view` condition.
- **Wrapping the whole call in a closure** (`$next($request)`): listeners run before or after, not around. The
  runnable events (`runnable/view/before` and `after`) come closest for a single view.
- **Middleware ordering by priority**: listeners run in the order of `Listeners[]`; rules in the order of `RuleList[]`.

## Related pages

- [Hardening](../../bc/6.0/hardening.md)
- [Security for applications](../creators/app-creators/security-for-applications.md)
- [Event hook pattern](../../standards/patterns/event-hook.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
